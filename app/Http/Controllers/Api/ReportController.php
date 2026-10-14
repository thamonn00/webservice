<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Concerns\ManagesPhotos;
use App\Models\Pet;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/** CRUD ประกาศหาย/พบ: อ่าน+เพิ่มได้ทุกคนที่มี token, แก้/ลบเฉพาะเจ้าของหรือ admin */
class ReportController extends Controller
{
    use ManagesPhotos;

    private const WITH = ['species', 'breed', 'photos', 'user:id,name', 'pet:id,name', 'subdistrict.district.province'];

    public function index(Request $request)
    {
        $request->validate([
            'type' => ['sometimes', Rule::in(['lost', 'found'])],
            'status' => ['sometimes', Rule::in(['open', 'closed'])],
            'species_id' => ['sometimes', 'integer', 'exists:species,id'],
            'q' => ['sometimes', 'string', 'max:255'],
            'per_page' => ['sometimes', 'integer', 'between:1,100'],
        ]);

        $rows = Report::with(self::WITH)
            ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('species_id'), fn($q) => $q->where('species_id', $request->species_id))
            ->when($request->filled('q'), function ($q) use ($request) {
                $t = '%' . addcslashes($request->q, '%_\\') . '%';
                $q->where(fn($i) => $i->where('title', 'like', $t)->orWhere('color', 'like', $t));
            })
            ->latest('id')
            ->paginate($request->input('per_page', 15));

        return response()->json(['status' => true, 'message' => 'ดึงข้อมูลสำเร็จ', 'data' => $rows]);
    }

    public function show(Report $report)
    {
        return response()->json(['status' => true, 'message' => 'ดึงข้อมูลสำเร็จ', 'data' => $report->load(self::WITH)]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules($request) + $this->photoRules());
        $this->assertPetOwner($request, $data['pet_id'] ?? null);
        $report = Report::create($this->reportData($data) + ['user_id' => $request->user()->id]);
        $this->syncPhotos($report, $request, 'reports');

        return response()->json(['status' => true, 'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว', 'data' => $report->load(self::WITH)], 201);
    }

    public function update(Request $request, Report $report)
    {
        $this->ownedReport($request, $report);
        $data = $request->validate($this->rules($request, $report) + $this->photoRules());
        $this->assertPetOwner($request, $data['pet_id'] ?? null);

        if (isset($data['species_id']) && (int) $data['species_id'] !== (int) $report->species_id && ! array_key_exists('breed_id', $data)) {
            $data['breed_id'] = null;
        }

        $report->update($this->reportData($data));
        $this->syncPhotos($report, $request, 'reports');

        return response()->json(['status' => true, 'message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว', 'data' => $report->refresh()->load(self::WITH)]);
    }

    public function destroy(Request $request, Report $report)
    {
        $this->ownedReport($request, $report);
        $report->delete();

        return response()->json(['status' => true, 'message' => 'ลบข้อมูลเรียบร้อยแล้ว', 'data' => null]);
    }

    private function ownedReport(Request $request, Report $report): Report
    {
        if ($request->user()->role !== 'admin' && (int) $report->user_id !== (int) $request->user()->id) {
            throw new \Illuminate\Auth\Access\AuthorizationException('คุณไม่มีสิทธิ์จัดการประกาศนี้');
        }

        return $report;
    }

    /** pet_id ต้องเป็นสัตว์ของตัวเอง ยกเว้นแอดมินผูกของใครก็ได้ */
    private function assertPetOwner(Request $request, $petId): void
    {
        if (empty($petId)) {
            return;
        }
        $pet = Pet::find($petId);
        if ($pet && $request->user()->role !== 'admin' && (int) $pet->user_id !== (int) $request->user()->id) {
            throw ValidationException::withMessages(['pet_id' => 'pet_id ต้องเป็นสัตว์ของคุณเอง']);
        }
    }

    private function rules(Request $request, ?Report $report = null): array
    {
        $required = $report ? 'sometimes' : 'required';
        $speciesId = $request->input('species_id', $report?->species_id);

        return [
            'pet_id' => ['sometimes', 'nullable', 'integer', 'exists:pets,id'],
            'species_id' => [$required, 'integer', 'exists:species,id'],
            'breed_id' => ['sometimes', 'nullable', 'integer', Rule::exists('breeds', 'id')->where('species_id', $speciesId)],
            'type' => [$required, Rule::in(['lost', 'found'])],
            'title' => [$required, 'string', 'max:150'],
            'color' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'subdistrict_id' => ['nullable', 'integer', 'exists:subdistricts,id'],
            'place' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'event_at' => [$required, 'date'],
            'contact_phone' => [$required, 'string', 'max:20'],
            'status' => ['sometimes', Rule::in(['open', 'closed'])],
        ];
    }

    private function reportData(array $data): array
    {
        return array_intersect_key($data, array_flip([
            'pet_id',
            'species_id',
            'breed_id',
            'type',
            'title',
            'color',
            'description',
            'subdistrict_id',
            'place',
            'latitude',
            'longitude',
            'event_at',
            'contact_phone',
            'status',
        ]));
    }
}
