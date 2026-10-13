<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Breed;
use App\Models\District;
use App\Models\Province;
use App\Models\Species;
use App\Models\Subdistrict;
use Illuminate\Http\Request;

/** ข้อมูลอ้างอิง (อ่านอย่างเดียว): ชนิด/สายพันธุ์สัตว์ และจังหวัด/อำเภอ/ตำบล */
class CatalogController extends Controller
{
    public function species()
    {
        return response()->json(['status' => true, 'message' => 'ดึงข้อมูลสำเร็จ', 'data' => Species::orderBy('id')->get()]);
    }

    public function speciesShow(Species $species)
    {
        return response()->json(['status' => true, 'message' => 'ดึงข้อมูลสำเร็จ', 'data' => $species->load('breeds')]);
    }

    public function breeds(Request $request)
    {
        $data = Breed::when($request->filled('species_id'), fn($q) => $q->where('species_id', $request->species_id))->orderBy('id')->get();

        return response()->json(['status' => true, 'message' => 'ดึงข้อมูลสำเร็จ', 'data' => $data]);
    }

    public function breedShow(Breed $breed)
    {
        return response()->json(['status' => true, 'message' => 'ดึงข้อมูลสำเร็จ', 'data' => $breed->load('species')]);
    }

    public function provinces()
    {
        return response()->json(['status' => true, 'message' => 'ดึงข้อมูลสำเร็จ', 'data' => Province::orderBy('name_th')->get()]);
    }

    public function provinceShow(Province $province)
    {
        return response()->json(['status' => true, 'message' => 'ดึงข้อมูลสำเร็จ', 'data' => $province->load('districts')]);
    }

    public function districts(Request $request)
    {
        $data = District::when($request->filled('province_id'), fn($q) => $q->where('province_id', $request->province_id))->orderBy('name_th')->get();

        return response()->json(['status' => true, 'message' => 'ดึงข้อมูลสำเร็จ', 'data' => $data]);
    }

    public function districtShow(District $district)
    {
        return response()->json(['status' => true, 'message' => 'ดึงข้อมูลสำเร็จ', 'data' => $district->load('province', 'subdistricts')]);
    }

    public function subdistricts(Request $request)
    {
        $data = Subdistrict::when($request->filled('district_id'), fn($q) => $q->where('district_id', $request->district_id))->orderBy('name_th')->get();

        return response()->json(['status' => true, 'message' => 'ดึงข้อมูลสำเร็จ', 'data' => $data]);
    }

    public function subdistrictShow(Subdistrict $subdistrict)
    {
        return response()->json(['status' => true, 'message' => 'ดึงข้อมูลสำเร็จ', 'data' => $subdistrict->load('district.province')]);
    }

    // ---------- เฉพาะแอดมิน (route ครอบด้วย middleware admin แล้ว) ----------

    public function storeSpecies(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100', 'unique:species,name']]);
        $row = Species::create($data);

        return response()->json(['status' => true, 'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว', 'data' => $row], 201);
    }

    public function updateSpecies(Request $request, Species $species)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100', 'unique:species,name,'.$species->id]]);
        $species->update($data);

        return response()->json(['status' => true, 'message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว', 'data' => $species->refresh()]);
    }

    public function destroySpecies(Species $species)
    {
        if ($species->pets()->exists() || $species->reports()->exists()) {
            return response()->json(['status' => false, 'message' => 'ลบไม่ได้ ยังมีสัตว์หรือประกาศใช้ชนิดนี้อยู่', 'data' => null], 403);
        }
        $species->delete();

        return response()->json(['status' => true, 'message' => 'ลบข้อมูลเรียบร้อยแล้ว', 'data' => null]);
    }

    public function storeBreed(Request $request)
    {
        $data = $request->validate([
            'species_id' => ['required', 'integer', 'exists:species,id'],
            'name' => ['required', 'string', 'max:100'],
        ]);
        $row = Breed::create($data);

        return response()->json(['status' => true, 'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว', 'data' => $row], 201);
    }

    public function updateBreed(Request $request, Breed $breed)
    {
        $data = $request->validate([
            'species_id' => ['sometimes', 'integer', 'exists:species,id'],
            'name' => ['sometimes', 'string', 'max:100'],
        ]);
        $breed->update($data);

        return response()->json(['status' => true, 'message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว', 'data' => $breed->refresh()]);
    }

    public function destroyBreed(Breed $breed)
    {
        if ($breed->pets()->exists() || $breed->reports()->exists()) {
            return response()->json(['status' => false, 'message' => 'ลบไม่ได้ ยังมีสัตว์หรือประกาศใช้สายพันธุ์นี้อยู่', 'data' => null], 403);
        }
        $breed->delete();

        return response()->json(['status' => true, 'message' => 'ลบข้อมูลเรียบร้อยแล้ว', 'data' => null]);
    }
}
