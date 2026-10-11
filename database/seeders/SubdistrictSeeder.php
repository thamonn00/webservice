<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;

class SubdistrictSeeder extends GeoJsonSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = $this->rows('subdistricts.json', [
            'provinceCode',
            'districtCode',
            'subdistrictCode',
            'subdistrictNameTh',
            'subdistrictNameEn',
        ]);
        $now = now();

        // จับคู่รหัสอำเภอกับ id ในฐานข้อมูล
        $districts = DB::table('districts')
            ->join('provinces', 'provinces.id', '=', 'districts.province_id')
            ->get([
                'districts.id',
                'districts.district_code',
                'provinces.province_code',
            ])
            ->keyBy('district_code');

        $rows = [];

        foreach ($data as $item) {
            $districtCode = $item['districtCode'];

            if (!isset($districts[$districtCode])) {
                throw new \RuntimeException("Unknown district code {$districtCode} in subdistricts.json.");
            }

            if ($districts[$districtCode]->province_code != $item['provinceCode']) {
                throw new \RuntimeException("Province code mismatch for district {$districtCode} in subdistricts.json.");
            }

            $rows[] = [
                'district_id' => $districts[$districtCode]->id,
                'subdistrict_code' => $item['subdistrictCode'],
                'name_th' => $item['subdistrictNameTh'],
                'name_en' => $item['subdistrictNameEn'],
                'postal_code' => $item['postalCode'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('subdistricts')->upsert(
                $chunk,
                ['subdistrict_code'],
                [
                    'district_id',
                    'name_th',
                    'name_en',
                    'postal_code',
                    'updated_at'
                ]
            );
        }
    }
}
