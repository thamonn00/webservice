<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sighting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use RuntimeException;

class SightingController extends Controller
{
    /**
     * GET /api/sightings
     * ตัวกรอง: report_id, province, district, subdistrict, q (ข้อความ/สถานที่), per_page
     */
    public function index(Request $request)
    {
        $request->validate([
            'report_id' => ['sometimes', 'integer'],
            'province' => ['sometimes', 'string', 'max:255'],
            'district' => ['sometimes', 'string', 'max:255'],
            'subdistrict' => ['sometimes', 'string', 'max:255'],
            'q' => ['sometimes', 'string', 'max:255'],
            'per_page' => ['sometimes', 'integer', 'between:1,100'],
        ]);

        return Sighting::with(['report.pet.species', 'report.photos', 'photos'])
            ->when($request->filled('report_id'), fn ($q) => $q->where('report_id', $request->input('report_id')))
            ->when($request->filled('province'), fn ($q) => $q->where('province', $request->input('province')))
            ->when($request->filled('district'), fn ($q) => $q->where('district', $request->input('district')))
            ->when($request->filled('subdistrict'), fn ($q) => $q->where('subdistrict', $request->input('subdistrict')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.addcslashes($request->input('q'), '%_\\').'%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('message', 'like', $term)->orWhere('seen_place', 'like', $term);
                });
            })
            ->latest()
            ->paginate((int) $request->input('per_page', 20))
            ->withQueryString();
    }

    public function show(Sighting $sighting)
    {
        return $sighting->load(['report.pet.species', 'report.photos', 'photos']);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules(), [
            'report_id.exists' => 'รายงานนี้ไม่มีอยู่ หรือปิดรับเบาะแสแล้ว',
        ]);
        unset($data['photos'], $data['photo_urls']);
        $data['user_id'] = $request->user()->id;

        $sighting = Sighting::create($data);
        $this->attachPhotos($sighting, $request);

        return response()->json(
            $sighting->load(['report.pet.species', 'report.photos', 'photos']),
            201
        );
    }

    public function update(Request $request, Sighting $sighting)
    {
        $sighting = $this->ownedSighting($request, $sighting);
        $data = $request->validate($this->rules($sighting));

        unset($data['photos'], $data['photo_urls'], $data['delete_photo_ids']);
        $sighting->update($data);
        $this->removePhotos($request, $sighting);
        $this->attachPhotos($sighting, $request);

        return $sighting->refresh()->load(['report.pet.species', 'report.photos', 'photos']);
    }

    public function destroy(Request $request, Sighting $sighting)
    {
        $sighting = $this->ownedSighting($request, $sighting);
        $photos = $sighting->photos;
        $sighting->delete();
        $this->deleteStoredPhotos($photos);

        return response()->noContent();
    }

    private function ownedSighting(Request $request, Sighting $sighting): Sighting
    {
        if ($request->user()->role === 'admin') {
            return $sighting;
        }

        return Sighting::where('user_id', $request->user()->id)->findOrFail($sighting->id);
    }

    private function rules(?Sighting $sighting = null): array
    {
        $required = $sighting ? 'sometimes' : 'required';

        return [
            // แจ้งเบาะแสได้เฉพาะรายงานที่ยังเปิดอยู่ (status = active)
            'report_id' => $sighting
                ? ['sometimes', 'integer', 'exists:reports,id']
                : ['required', 'integer', Rule::exists('reports', 'id')->where('status', 'active')],
            'message' => [$required, 'string'],
            'province' => [$required, 'string', 'max:255'],
            'district' => [$required, 'string', 'max:255'],
            'subdistrict' => [$required, 'string', 'max:255'],
            'seen_place' => [$required, 'string', 'max:255'],
            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'seen_at' => [$required, 'date'],
            'contact' => ['sometimes', 'nullable', 'string', 'max:255'],
            'photos' => ['sometimes', 'array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'photo_urls' => ['sometimes', 'array', 'max:10'],
            'photo_urls.*' => ['required', 'url', 'max:2048'],
            'delete_photo_ids' => ['sometimes', 'array'],
            'delete_photo_ids.*' => [
                'integer',
                Rule::exists('sighting_photos', 'id')->where('sighting_id', $sighting?->id),
            ],
        ];
    }

    private function attachPhotos(Sighting $sighting, Request $request): void
    {
        foreach ($request->file('photos', []) as $photo) {
            $path = $photo->store('sightings', 'public');
            if ($path === false) {
                throw new RuntimeException('Unable to store uploaded sighting photo.');
            }

            $sighting->photos()->create(['photo' => $path]);
        }

        foreach ($request->input('photo_urls', []) as $url) {
            $sighting->photos()->create(['photo' => $url]);
        }
    }

    private function removePhotos(Request $request, Sighting $sighting): void
    {
        $photoIds = $request->input('delete_photo_ids', []);
        if (!$photoIds) {
            return;
        }

        $photos = $sighting->photos()->whereIn('id', $photoIds)->get();
        $sighting->photos()->whereIn('id', $photoIds)->delete();
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
                throw new RuntimeException('Unable to delete stored sighting photos.');
            }
        }
    }
}
