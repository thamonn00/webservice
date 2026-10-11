<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\Report;
use App\Models\Sighting;
use App\Models\Species;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use RuntimeException;

class ResourceController extends Controller
{
    private const RESOURCES = ['pets', 'reports', 'sightings'];

    public function index(Request $request, string $resource)
    {
        $resource = $this->validateResource($resource);
        $editing = $request->filled('edit')
            ? $this->queryFor($resource)->findOrFail((int) $request->input('edit'))
            : null;

        /** @var \Illuminate\Pagination\LengthAwarePaginator $records */
        $records = $this->queryFor($resource)->latest()->paginate(12);
        $records->withQueryString();

        return view('admin.resources', [
            'resource' => $resource,
            'records' => $records,
            'editing' => $editing,
            'species' => Species::with('breeds')->orderBy('name')->get(),
            'pets' => Pet::orderBy('name')->get(),
            'reports' => Report::orderByDesc('created_at')->limit(200)->get(),
        ]);
    }

    public function store(Request $request, string $resource)
    {
        $resource = $this->validateResource($resource);
        $data = $this->validatedData($request, $resource);
        $record = $this->modelFor($resource);

        $data['user_id'] = $request->user()->id;

        $record->fill($data);
        $record->save();
        $this->syncPhotos($request, $record);

        return redirect()
            ->route('admin.resources.index', $resource)
            ->with('status', 'เพิ่มข้อมูลเรียบร้อยแล้ว');
    }

    public function update(Request $request, string $resource, int $record)
    {
        $resource = $this->validateResource($resource);
        $model = $this->queryFor($resource)->findOrFail($record);
        $data = $this->validatedData($request, $resource, $model);

        $model->fill($data);
        $model->save();
        $this->syncPhotos($request, $model);

        return redirect()
            ->route('admin.resources.index', $resource)
            ->with('status', 'บันทึกการแก้ไขเรียบร้อยแล้ว');
    }

    public function destroy(string $resource, int $record)
    {
        $resource = $this->validateResource($resource);
        $model = $this->queryFor($resource)->findOrFail($record);
        $photos = $model->photos;

        if ($resource === 'reports') {
            $photos = $photos->merge(
                $model->sightings()->with('photos')->get()->flatMap->photos
            );
        }

        if ($resource === 'pets') {
            // ลบ pet แล้ว reports/sightings จะ cascade ใน DB แต่ไฟล์จะค้าง ต้องเก็บมาลบด้วย
            $model->loadMissing(['reports.photos', 'reports.sightings.photos']);
            $photos = $photos
                ->merge($model->reports->flatMap->photos)
                ->merge($model->reports->flatMap(fn($r) => $r->sightings->flatMap->photos));
        }

        $model->delete();
        $this->deleteStoredPhotos($photos);

        return redirect()
            ->route('admin.resources.index', $resource)
            ->with('status', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

    private function validateResource(string $resource): string
    {
        abort_unless(in_array($resource, self::RESOURCES, true), 404);

        return $resource;
    }

    private function queryFor(string $resource): Builder
    {
        return match ($resource) {
            'pets' => Pet::with(['species', 'breed', 'photos', 'user']),
            'reports' => Report::with(['pet', 'photos', 'user']),
            'sightings' => Sighting::with(['report.pet', 'photos', 'user']),
        };
    }

    private function modelFor(string $resource): Model
    {
        return match ($resource) {
            'pets' => new Pet(),
            'reports' => new Report(),
            'sightings' => new Sighting(),
        };
    }

    private function validatedData(Request $request, string $resource, ?Model $model = null): array
    {
        $required = $model ? 'sometimes' : 'required';

        $rules = match ($resource) {
            'pets' => [
                'species_id' => [$required, 'integer', 'exists:species,id'],
                'breed_id' => [
                    'sometimes',
                    'nullable',
                    'integer',
                    Rule::exists('breeds', 'id')->where(
                        'species_id',
                        $request->input('species_id', $model?->species_id)
                    ),
                ],
                'name' => [$required, 'string', 'max:255'],
                'color' => ['sometimes', 'nullable', 'string', 'max:255'],
                'gender' => ['sometimes', Rule::in(['male', 'female', 'unknown'])],
                'age' => ['sometimes', 'nullable', 'integer', 'min:0'],
                'description' => ['sometimes', 'nullable', 'string'],
            ],
            'reports' => [
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
            ],
            'sightings' => [
                'report_id' => [$required, 'integer', 'exists:reports,id'],
                'message' => [$required, 'string'],
                'province' => [$required, 'string', 'max:255'],
                'district' => [$required, 'string', 'max:255'],
                'subdistrict' => [$required, 'string', 'max:255'],
                'seen_place' => [$required, 'string', 'max:255'],
                'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
                'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
                'seen_at' => [$required, 'date'],
                'contact' => ['sometimes', 'nullable', 'string', 'max:255'],
            ],
        };

        $rules += [
            'photos' => ['sometimes', 'array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'photo_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'photo_urls' => ['sometimes', 'array', 'max:10'],
            'photo_urls.*' => ['required', 'url', 'max:2048'],
            'delete_photo_ids' => ['sometimes', 'array'],
            'delete_photo_ids.*' => [
                'integer',
                Rule::exists($this->photoTableFor($resource), 'id')
                    ->where($this->photoForeignKeyFor($resource), $model?->id),
            ],
        ];

        $data = $request->validate($rules);
        unset($data['photos'], $data['photo_url'], $data['photo_urls'], $data['delete_photo_ids']);

        if (
            $resource === 'pets' && $model && isset($data['species_id'])
            && (int) $data['species_id'] !== (int) $model->species_id
            && !array_key_exists('breed_id', $data)
        ) {
            $data['breed_id'] = null;
        }

        return $data;
    }

    private function syncPhotos(Request $request, Model $model): void
    {
        $photoRelation = $model->photos();
        $photoIds = $request->input('delete_photo_ids', []);

        if ($photoIds) {
            $photosToDelete = $photoRelation->whereIn('id', $photoIds)->get();
            $primaryWasRemoved = $model instanceof Pet
                && $photosToDelete->contains('is_primary', true);
            $photoRelation->whereIn('id', $photoIds)->delete();
            $this->deleteStoredPhotos($photosToDelete);

            if ($primaryWasRemoved) {
                $replacement = $model->photos()->oldest('id')->first();
                if ($replacement) {
                    $replacement->update(['is_primary' => true]);
                }
            }
        }

        foreach ($request->file('photos', []) as $photo) {
            $directory = match (true) {
                $model instanceof Pet => 'pets',
                $model instanceof Report => 'reports',
                default => 'sightings',
            };
            $path = $photo->store($directory, 'public');
            if ($path === false) {
                throw new RuntimeException('บันทึกไฟล์รูปภาพไม่สำเร็จ');
            }

            $photoRelation->create([
                'photo' => $path,
                'is_primary' => $model instanceof Pet
                    && !$model->photos()->where('is_primary', true)->exists(),
            ]);
        }

        foreach ($request->input('photo_urls', []) as $url) {
            $photoRelation->create([
                'photo' => $url,
                'is_primary' => $model instanceof Pet
                    && !$model->photos()->where('is_primary', true)->exists(),
            ]);
        }

        if ($request->filled('photo_url')) {
            $photoRelation->create([
                'photo' => $request->input('photo_url'),
                'is_primary' => $model instanceof Pet
                    && !$model->photos()->where('is_primary', true)->exists(),
            ]);
        }
    }

    private function photoTableFor(string $resource): string
    {
        return match ($resource) {
            'pets' => 'pet_photos',
            'reports' => 'report_photos',
            'sightings' => 'sighting_photos',
        };
    }

    private function photoForeignKeyFor(string $resource): string
    {
        return match ($resource) {
            'pets' => 'pet_id',
            'reports' => 'report_id',
            'sightings' => 'sighting_id',
        };
    }

    private function deleteStoredPhotos(Collection $photos): void
    {
        $paths = $photos
            ->pluck('photo')
            ->filter(fn($photo) => !filter_var($photo, FILTER_VALIDATE_URL))
            ->all();

        if ($paths && !Storage::disk('public')->delete($paths)) {
            throw new RuntimeException('ลบไฟล์รูปภาพไม่สำเร็จ');
        }
    }
}
