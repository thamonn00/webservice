<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\Report;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use RuntimeException;

class ReportController extends Controller
{
    private const RELATIONS = ['pet.species', 'pet.breed', 'photos', 'user:id,name'];

    /**
     * GET /api/reports
     * ตัวกรอง: type, status, species_id, province, district, subdistrict, q, per_page
     * ค้นหาใกล้ฉัน: lat + lng (+ radius_km, ค่าเริ่มต้น 10) จะเรียงตามระยะทางและมี distance_km
     */
    public function index(Request $request)
    {
        $request->validate([
            'type' => ['sometimes', Rule::in(['lost', 'found'])],
            'status' => ['sometimes', Rule::in(['active', 'found', 'closed', 'cancelled'])],
            'species_id' => ['sometimes', 'integer'],
            'province' => ['sometimes', 'string', 'max:255'],
            'district' => ['sometimes', 'string', 'max:255'],
            'subdistrict' => ['sometimes', 'string', 'max:255'],
            'q' => ['sometimes', 'string', 'max:255'],
            'lat' => ['required_with:lng', 'numeric', 'between:-90,90'],
            'lng' => ['required_with:lat', 'numeric', 'between:-180,180'],
            'radius_km' => ['sometimes', 'numeric', 'between:0.1,500'],
            'per_page' => ['sometimes', 'integer', 'between:1,100'],
        ]);

        $perPage = (int) $request->input('per_page', 20);
        $query = Report::with(self::RELATIONS)
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->input('type')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('species_id'), fn ($q) => $q->whereHas(
                'pet',
                fn ($pet) => $pet->where('species_id', $request->input('species_id'))
            ))
            ->when($request->filled('province'), fn ($q) => $q->where('province', $request->input('province')))
            ->when($request->filled('district'), fn ($q) => $q->where('district', $request->input('district')))
            ->when($request->filled('subdistrict'), fn ($q) => $q->where('subdistrict', $request->input('subdistrict')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.addcslashes($request->input('q'), '%_\\').'%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('title', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhere('place', 'like', $term)
                        ->orWhereHas('pet', fn ($pet) => $pet->where('name', 'like', $term));
                });
            });

        if ($request->filled('lat') && $request->filled('lng')) {
            return $this->nearby($request, $query, $perPage);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    /**
     * GET /api/reports/{report}/matches
     * แนะนำรายงานฝั่งตรงข้าม (lost <-> found) ที่น่าจะเป็นสัตว์ตัวเดียวกัน
     * ให้คะแนนจากสายพันธุ์ สี ระยะทาง และช่วงเวลา แล้วเรียงคะแนนมากไปน้อย
     */
    public function matches(Report $report)
    {
        $report->loadMissing('pet');
        $report->seen_at ??= now();

        $candidates = Report::with(self::RELATIONS)
            ->where('type', $report->type === 'lost' ? 'found' : 'lost')
            ->where('status', 'active')
            ->where('id', '!=', $report->id)
            ->whereHas('pet', fn ($pet) => $pet->where('species_id', $report->pet->species_id))
            ->whereBetween('seen_at', [
                $report->seen_at->copy()->subDays(30),
                $report->seen_at->copy()->addDays(30),
            ])
            ->get();

        $results = $candidates
            ->map(function (Report $candidate) use ($report) {
                $candidate->setAttribute('match_score', $this->matchScore($report, $candidate));
                if ($this->hasCoordinates($report) && $this->hasCoordinates($candidate)) {
                    $candidate->setAttribute('distance_km', round($this->distanceBetween($report, $candidate), 2));
                }

                return $candidate;
            })
            ->sortByDesc('match_score')
            ->take(10)
            ->values();

        return response()->json(['data' => $results]);
    }

    private function nearby(Request $request, $query, int $perPage): LengthAwarePaginator
    {
        $lat = (float) $request->input('lat');
        $lng = (float) $request->input('lng');
        $radius = (float) $request->input('radius_km', 10);

        $items = $query->withinBox($lat, $lng, $radius)->get()
            ->each(fn (Report $report) => $report->setAttribute(
                'distance_km',
                round(Report::distanceKm($lat, $lng, (float) $report->latitude, (float) $report->longitude), 2)
            ))
            ->filter(fn (Report $report) => $report->distance_km <= $radius)
            ->sortBy('distance_km')
            ->values();

        $page = Paginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );
    }

    private function matchScore(Report $a, Report $b): int
    {
        $score = 10; // ชนิดสัตว์ตรงกันอยู่แล้วจากการคัดกรอง

        if ($a->pet->breed_id && $a->pet->breed_id === $b->pet->breed_id) {
            $score += 30;
        }

        if ($a->pet->color && $b->pet->color
            && mb_strtolower(trim($a->pet->color)) === mb_strtolower(trim($b->pet->color))) {
            $score += 20;
        }

        if ($this->hasCoordinates($a) && $this->hasCoordinates($b)) {
            $km = $this->distanceBetween($a, $b);
            $score += $km <= 5 ? 30 : ($km <= 20 ? 20 : ($km <= 50 ? 10 : 0));
        } elseif ($a->province === $b->province) {
            $score += 10;
        }

        $days = abs($a->seen_at->diffInDays($b->seen_at));
        $score += $days <= 3 ? 20 : ($days <= 7 ? 10 : 5);

        return $score;
    }

    private function hasCoordinates(Report $report): bool
    {
        return $report->latitude !== null && $report->longitude !== null;
    }

    private function distanceBetween(Report $a, Report $b): float
    {
        return Report::distanceKm(
            (float) $a->latitude,
            (float) $a->longitude,
            (float) $b->latitude,
            (float) $b->longitude
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $this->ensureOwnedPet($request, $data['pet_id'], $data['type']);
        unset($data['photos'], $data['photo_urls']);
        $data['user_id'] = $request->user()->id;

        $report = Report::create($data);
        $this->attachPhotos($report, $request);

        return response()->json(
            $report->load(['pet.species', 'pet.breed', 'photos', 'user:id,name']),
            201
        );
    }

    public function show(Report $report)
    {
        return $report->load(['pet.species', 'pet.breed', 'photos', 'user:id,name']);
    }

    public function update(Request $request, Report $report)
    {
        $report = $this->ownedReport($request, $report);
        $data = $request->validate($this->rules(true, $report));

        if (isset($data['pet_id'])) {
            $this->ensureOwnedPet($request, $data['pet_id'], $data['type'] ?? $report->type);
        }

        unset($data['photos'], $data['photo_urls'], $data['delete_photo_ids']);
        $report->update($data);
        $this->removePhotos($request, $report);
        $this->attachPhotos($report, $request);

        return $report->refresh()->load(['pet.species', 'pet.breed', 'photos', 'user:id,name']);
    }

    public function destroy(Request $request, Report $report)
    {
        $report = $this->ownedReport($request, $report);
        // ลบ report แล้ว sightings จะ cascade ใน DB แต่ไฟล์รูปเบาะแสจะค้างใน disk
        $report->loadMissing(['photos', 'sightings.photos']);
        $photos = $report->photos->merge($report->sightings->flatMap->photos);
        $report->delete();
        $this->deleteStoredPhotos($photos);

        return response()->noContent();
    }

    private function ownedReport(Request $request, Report $report): Report
    {
        if ($request->user()->role === 'admin') {
            return $report;
        }

        return Report::where('user_id', $request->user()->id)->findOrFail($report->id);
    }

    private function ensureOwnedPet(Request $request, int $petId, string $type): void
    {
        // รายงาน "พบสัตว์" คนแจ้งไม่ใช่เจ้าของสัตว์ จึงไม่ต้องตรวจความเป็นเจ้าของ
        if ($type === 'found' || $request->user()->role === 'admin') {
            return;
        }

        Pet::where('user_id', $request->user()->id)->findOrFail($petId);
    }

    private function rules(bool $updating = false, ?Report $report = null): array
    {
        $required = $updating ? 'sometimes' : 'required';

        return [
            'pet_id' => [$required, 'integer', 'exists:pets,id'],
            'type' => [$required, Rule::in(['lost', 'found'])],
            'title' => [$required, 'string', 'max:255'],
            'description' => [$required, 'string'],
            'province' => [$required, 'string', 'max:255'],
            'district' => [$required, 'string', 'max:255'],
            'subdistrict' => [$required, 'string', 'max:255'],
            'place' => [$required, 'string', 'max:255'],
            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'seen_at' => [$required, 'date'],
            'contact_phone' => [$required, 'string', 'max:255'],
            'status' => ['sometimes', Rule::in(['active', 'found', 'closed', 'cancelled'])],
            'photos' => ['sometimes', 'array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'photo_urls' => ['sometimes', 'array', 'max:10'],
            'photo_urls.*' => ['required', 'url', 'max:2048'],
            'delete_photo_ids' => ['sometimes', 'array'],
            'delete_photo_ids.*' => [
                'integer',
                Rule::exists('report_photos', 'id')->where('report_id', $report?->id),
            ],
        ];
    }

    private function attachPhotos(Report $report, Request $request): void
    {
        foreach ($request->file('photos', []) as $photo) {
            $path = $photo->store('reports', 'public');
            if ($path === false) {
                throw new RuntimeException('Unable to store uploaded report photo.');
            }

            $report->photos()->create(['photo' => $path]);
        }

        foreach ($request->input('photo_urls', []) as $url) {
            $report->photos()->create(['photo' => $url]);
        }
    }

    private function removePhotos(Request $request, Report $report): void
    {
        $photoIds = $request->input('delete_photo_ids', []);
        if (!$photoIds) {
            return;
        }

        $photos = $report->photos()->whereIn('id', $photoIds)->get();
        $report->photos()->whereIn('id', $photoIds)->delete();
        $this->deleteStoredPhotos($photos);
    }

    private function deleteStoredPhotos(Collection $photos): void
    {
        $paths = $photos
            ->pluck('photo')
            ->filter(fn ($photo) => !filter_var($photo, FILTER_VALIDATE_URL))
            ->all();

        if ($paths) {
            if (!Storage::disk('public')->delete($paths)) {
                throw new RuntimeException('Unable to delete stored report photos.');
            }
        }
    }
}
