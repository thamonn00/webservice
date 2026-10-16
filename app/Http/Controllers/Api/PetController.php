<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Concerns\ManagesPhotos;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PetController extends Controller
{
    use ManagesPhotos;

    private const WITH = ['species', 'breed', 'photos', 'user:id,name'];

    /** GET /api/pet — getAll + ค้นหา q / species_id / breed_id / gender + 1.5 ค้นหาสร้างสรรค์ */
    public function index(Request $request)
    {
        $request->validate([
            'species_id' => ['sometimes', 'integer', 'exists:species,id'],
            'breed_id' => ['sometimes', 'integer', 'exists:breeds,id'],
            'gender' => ['sometimes', Rule::in(['male', 'female', 'unknown'])],
            'q' => ['sometimes', 'string', 'max:255'],
            'per_page' => ['sometimes', 'integer', 'between:1,100'],
        ]);

        $pets = Pet::with(self::WITH)
            ->when($request->filled('species_id'), fn($q) => $q->where('species_id', $request->species_id))
            ->when($request->filled('breed_id'), fn($q) => $q->where('breed_id', $request->breed_id))
            ->when($request->filled('gender'), fn($q) => $q->where('gender', $request->gender))
            ->when($request->filled('q'), function ($q) use ($request) {
                $t = '%' . addcslashes($request->q, '%_\\') . '%';
                $q->where(fn($i) => $i->where('name', 'like', $t)->orWhere('color', 'like', $t));
            })
            
            ->latest('id')
            ->paginate($request->input('per_page', 15));

        return response()->json(['status' => true, 'message' => 'ดึงข้อมูลสำเร็จ', 'data' => $pets]);
    }

    /** POST /api/pet — insert (ต้องมี token) */
    public function store(Request $request)
    {
        $data = $request->validate($this->rules($request) + $this->photoRules());
        $pet = Pet::create($this->petData($data) + ['user_id' => $request->user()->id]);
        $this->syncPhotos($pet, $request, 'pets');

        return response()->json(['status' => true, 'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว', 'data' => $pet->load(self::WITH)], 201);
    }

    /** GET /api/pet/{id} — getByID + 404 */
    public function show(Pet $pet)
    {
        return response()->json(['status' => true, 'message' => 'ดึงข้อมูลสำเร็จ', 'data' => $pet->load(self::WITH)]);
    }

    /** PUT /api/pet/{id} — update (ต้องมี token) */
    public function update(Request $request, Pet $pet)
    {
        $pet = $this->ownedPet($request, $pet);
        $data = $request->validate($this->rules($request, $pet) + $this->photoRules());

        if (
            isset($data['species_id'])
            && (int) $data['species_id'] !== (int) $pet->species_id
            && !array_key_exists('breed_id', $data)
        ) {
            $data['breed_id'] = null;
        }

        $pet->update($this->petData($data));
        $this->syncPhotos($pet, $request, 'pets');

        return response()->json(['status' => true, 'message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว', 'data' => $pet->refresh()->load(self::WITH)]);
    }

    /** DELETE /api/pet/{id} — delete (ต้องมี token, ลบรูป+ไฟล์ด้วย) */
    public function destroy(Request $request, Pet $pet)
    {
        $this->ownedPet($request, $pet);
        $pet->delete();

        return response()->json([
            'status' => true,
            'message' => 'ลบข้อมูลเรียบร้อยแล้ว',
            'data' => null,
        ]);
    }

    private function ownedPet(Request $request, Pet $pet): Pet
    {
        if (
            $request->user()->role !== 'admin'
            && (int) $pet->user_id !== (int) $request->user()->id
        ) {
            throw new \Illuminate\Auth\Access\AuthorizationException('คุณไม่มีสิทธิ์จัดการข้อมูลสัตว์นี้');
        }

        return $pet;
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
            'name' => [$required, 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'unknown'])],
            'age' => ['nullable', 'integer', 'between:0,50'],
            'description' => ['nullable', 'string'],
        ];
    }

    private function petData(array $data): array
    {
        $petData = array_intersect_key($data, array_flip([
            'species_id',
            'breed_id',
            'name',
            'color',
            'gender',
            'age',
            'description',
        ]));

        if (array_key_exists('gender', $petData) && $petData['gender'] === null) {
            $petData['gender'] = 'unknown';
        }

        return $petData;
    }
}
