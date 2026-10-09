<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;//
use Carbon\Carbon;//

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = database_path('data/provinces.json');
        $data = json_decode(file_get_contents($file), true);
        $now = Carbon::now();

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
