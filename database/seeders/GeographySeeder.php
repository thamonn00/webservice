<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeographySeeder extends Seeder
{
    /**
     * จังหวัด 77 / อำเภอ 928 / ตำบล 7,436 จาก database/data/*.json
     * รันซ้ำได้ (ข้ามถ้ามีข้อมูลแล้ว)
     */
    public function run()
    {
        if (DB::table('provinces')->exists()) {
            $this->command->info('ข้อมูลจังหวัด/อำเภอ/ตำบล มีอยู่แล้ว ข้าม');

            return;
        }

        $now = now();
        $read = fn(string $f) => json_decode(file_get_contents(database_path("data/{$f}.json")), true);

        DB::transaction(function () use ($read, $now) {
            foreach (array_chunk($read('provinces'), 500) as $chunk) {
                DB::table('provinces')->insert(array_map(fn($r) => [
                    'province_code' => $r['provinceCode'],
                    'name_th' => $r['provinceNameTh'],
                    'name_en' => $r['provinceNameEn'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $chunk));
            }
            $provinceIds = DB::table('provinces')->pluck('id', 'province_code');

            foreach (array_chunk($read('districts'), 500) as $chunk) {
                DB::table('districts')->insert(array_map(fn($r) => [
                    'province_id' => $provinceIds[$r['provinceCode']],
                    'district_code' => $r['districtCode'],
                    'name_th' => $r['districtNameTh'],
                    'name_en' => $r['districtNameEn'] ?? null,
                    'postal_code' => $r['postalCode'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $chunk));
            }
            $districtIds = DB::table('districts')->pluck('id', 'district_code');

            foreach (array_chunk($read('subdistricts'), 500) as $chunk) {
                DB::table('subdistricts')->insert(array_map(fn($r) => [
                    'district_id' => $districtIds[$r['districtCode']],
                    'subdistrict_code' => $r['subdistrictCode'],
                    'name_th' => $r['subdistrictNameTh'],
                    'name_en' => $r['subdistrictNameEn'] ?? null,
                    'postal_code' => $r['postalCode'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $chunk));
            }
        });
    }
}
