<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;

class DistrictSeeder extends GeoJsonSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = $this->rows('districts.json', [
            'provinceCode',
            'districtCode',
            'districtNameTh',
            'districtNameEn',
        ]);
        $now = now();

        // จับคู่รหัสจังหวัดกับ id ในฐานข้อมูล
        $provinceIds = DB::table('provinces')
            ->pluck('id', 'province_code')
            ->toArray();

        $rows = [];

        foreach ($data as $item) {
            $provinceCode = $item['provinceCode'];

            if (!isset($provinceIds[$provinceCode])) {
                throw new \RuntimeException("Unknown province code {$provinceCode} in districts.json.");
            }

            $rows[] = [
                'province_id' => $provinceIds[$provinceCode],
                'district_code' => $item['districtCode'],
                'name_th' => $item['districtNameTh'],
                'name_en' => $item['districtNameEn'],
                'postal_code' => $item['postalCode'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('districts')->upsert(
                $chunk,
                ['district_code'],
                [
                    'province_id',
                    'name_th',
                    'name_en',
                    'postal_code',
                    'updated_at'
                ]
            );
        }
    }
}
