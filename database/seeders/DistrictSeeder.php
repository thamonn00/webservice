<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = database_path('data/districts.json');
        $data = json_decode(file_get_contents($file), true);
        $now = Carbon::now();

        // จับคู่รหัสจังหวัดกับ id ในฐานข้อมูล
        $provinceIds = DB::table('provinces')
            ->pluck('id', 'province_code')
            ->toArray();

        $rows = [];

        foreach ($data as $item) {
            $provinceCode = $item['provinceCode'];

            if (!isset($provinceIds[$provinceCode])) {
                continue;
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
