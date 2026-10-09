<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class SubdistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = database_path('data/subdistricts.json');
        $data = json_decode(file_get_contents($file), true);
        $now = Carbon::now();

        // จับคู่รหัสอำเภอกับ id ในฐานข้อมูล
        $districtIds = DB::table('districts')
            ->pluck('id', 'district_code')
            ->toArray();

        $rows = [];

        foreach ($data as $item) {
            $districtCode = $item['districtCode'];

            if (!isset($districtIds[$districtCode])) {
                continue;
            }

            $rows[] = [
                'district_id' => $districtIds[$districtCode],
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
