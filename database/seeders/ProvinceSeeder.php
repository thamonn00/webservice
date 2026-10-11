<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends GeoJsonSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = $this->rows('provinces.json', [
            'provinceCode',
            'provinceNameTh',
            'provinceNameEn',
        ]);
        $now = now();

        $rows = [];

        foreach ($data as $item) {
            $rows[] = [
                'province_code' => $item['provinceCode'],
                'name_th' => $item['provinceNameTh'],
                'name_en' => $item['provinceNameEn'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('provinces')->upsert(
                $chunk,
                ['province_code'],
                ['name_th', 'name_en', 'updated_at']
            );
        }
    }
}
