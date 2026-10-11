<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use RuntimeException;

class PetController extends Controller
{
    /**
     * GET /api/pets
     * ตัวกรอง: species_id, breed_id, gender, q (ชื่อ/สี/คำอธิบาย), per_page
     */
    public function index(Request $request)
    {
        $request->validate([
            'species_id' => ['sometimes', 'integer'],
            'breed_id' => ['sometimes', 'integer'],
            'gender' => ['sometimes', Rule::in(['male', 'female', 'unknown'])],
            'q' => ['sometimes', 'string', 'max:255'],
            'per_page' => ['sometimes', 'integer', 'between:1,100'],
        ]);

        return Pet::with(['species', 'breed', 'photos'])
            ->when($request->filled('species_id'), fn ($q) => $q->where('species_id', $request->input('species_id')))
            ->when($request->filled('breed_id'), fn ($q) => $q->where('breed_id', $request->input('breed_id')))
            ->when($request->filled('gender'), fn ($q) => $q->where('gender', $request->input('gender')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.addcslashes($request->input('q'), '%_\\').'%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', $term)
                        ->orWhere('color', 'like', $term)
                        ->orWhere('description', 'like', $term);
                });
            })
            ->latest()
            ->paginate((int) $request->input('per_page', 20))
            ->withQueryString();
    }

    public function show(Pet $pet)
    {
        return $pet->load(['species', 'breed', 'photos']);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules($request));
        $pet = Pet::create($this->petData($data) + [
            'user_id' => $request->user()->id,
        ]);

        $this->attachPhotos($pet, $request);

        return response()->json(
            $pet->load(['species', 'breed', 'photos']),
            201
        );
    }

    public function update(Request $request, Pet $pet)
    {
        $pet = $this->ownedPet($request, $pet);
        $data = $request->validate($this->rules($request, $pet));

        if (isset($data['species_id'])
            && (int) $data['species_id'] !== (int) $pet->species_id
            && !array_key_exists('breed_id', $data)) {
            $data['breed_id'] = null;
        }

        $pet->update($this->petData($data));
        $this->removePhotos($request, $pet);
        $this->attachPhotos($pet, $request);

        return $pet->refresh()->load(['species', 'breed', 'photos']);
    }

    public function destroy(Request $request, Pet $pet)
    {
        $pet = $this->ownedPet($request, $pet);
        // เก็บไฟล์รูปทั้งหมดที่โยงถึง pet นี้ (รูปสัตว์ + รูปรายงาน + รูปเบาะแส)
        // เพราะลบ pet แล้ว reports/sightings จะ cascade ใน DB แต่ไฟล์ใน disk จะค้าง
        $pet->loadMissing(['photos', 'reports.photos', 'reports.sightings.photos']);
        $photos = $pet->photos
            ->merge($pet->reports->flatMap->photos)
            ->merge($pet->reports->flatMap(fn ($r) => $r->sightings->flatMap->photos));
        $pet->delete();
        $this->deleteStoredPhotos($photos);

        return response()->noContent();
    }

    private function ownedPet(Request $request, Pet $pet): Pet
    {
        if ($request->user()->role === 'admin') {
            return $pet;
        }

        return Pet::where('user_id', $request->user()->id)->findOrFail($pet->id);
    }

    private function rules(Request $request, ?Pet $pet = null): array
    {
        $required = $pet ? 'sometimes' : 'required';
        $speciesId = $request->input('species_id', $pet?->species_id);

        return [
            'species_id' => [$required, 'integer', 'exists:species,id'],
            'breed_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('breeds', 'id')->where('species_id', $speciesId),
            ],
            'name' => [$required, 'string', 'max:255'],
            'color' => ['sometimes', 'nullable', 'string', 'max:255'],
            'gender' => ['sometimes', Rule::in(['male', 'female', 'unknown'])],
            'age' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'description' => ['sometimes', 'nullable', 'string'],
            'photos' => ['sometimes', 'array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'photo_urls' => ['sometimes', 'array', 'max:10'],
            'photo_urls.*' => ['required', 'url', 'max:2048'],
            'delete_photo_ids' => ['sometimes', 'array'],
            'delete_photo_ids.*' => [
                'integer',
                Rule::exists('pet_photos', 'id')->where('pet_id', $pet?->id),
            ],
        ];
    }

    private function petData(array $data): array
    {
        return array_intersect_key($data, array_flip([
            'species_id',
            'breed_id',
            'name',
            'color',
            'gender',
            'age',
            'description',
        ]));
    }

    private function attachPhotos(Pet $pet, Request $request): void
    {
        $hasPrimaryPhoto = $pet->photos()->where('is_primary', true)->exists();

        foreach ($request->file('photos', []) as $photo) {
            $path = $photo->store('pets', 'public');
            if ($path === false) {
                throw new RuntimeException('Unable to store uploaded pet photo.');
            }

            $pet->photos()->create([
                'photo' => $path,
                'is_primary' => !$hasPrimaryPhoto,
            ]);
            $hasPrimaryPhoto = true;
        }

        foreach ($request->input('photo_urls', []) as $url) {
            $pet->photos()->create([
                'photo' => $url,
                'is_primary' => !$hasPrimaryPhoto,
            ]);
            $hasPrimaryPhoto = true;
        }
    }

    private function removePhotos(Request $request, Pet $pet): void
    {
        $photoIds = $request->input('delete_photo_ids', []);
        if (!$photoIds) {
            return;
        }

        $photos = $pet->photos()->whereIn('id', $photoIds)->get();
        $primaryWasRemoved = $photos->contains('is_primary', true);

        $pet->photos()->whereIn('id', $photoIds)->delete();
        $this->deleteStoredPhotos($photos);

        if ($primaryWasRemoved) {
            $replacement = $pet->photos()->oldest('id')->first();
            if ($replacement) {
                $replacement->update(['is_primary' => true]);
            }
        }
    }

    private function deleteStoredPhotos(Collection $photos): void
    {
        $paths = $photos
            ->pluck('photo')
            ->filter(fn ($photo) => !filter_var($photo, FILTER_VALIDATE_URL))
            ->all();

        if ($paths) {
            if (!Storage::disk('public')->delete($paths)) {
                throw new RuntimeException('Unable to delete stored pet photos.');
            }
        }
    }
}
