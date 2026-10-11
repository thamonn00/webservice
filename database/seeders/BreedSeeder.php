<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Breed;
use App\Models\Species;
use Illuminate\Database\Seeder;

class BreedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $breeds = [
            'สุนัข' => ['ไทยหลังอาน', 'ชิวาวา', 'ปอมเมอเรเนียน', 'ชิห์สุ', 'โกลเด้นรีทรีฟเวอร์', 'พุดเดิ้ล', 'บางแก้ว', 'ไม่ทราบสายพันธุ์'],
            'แมว' => ['วิเชียรมาศ', 'โคราช (สีสวาด)', 'เปอร์เซีย', 'สก็อตติชโฟลด์', 'บริติชชอร์ตแฮร์', 'แมวไทยทั่วไป', 'ไม่ทราบสายพันธุ์'],
            'นก' => ['นกแก้ว', 'นกเขา', 'นกกรงหัวจุก', 'ไม่ทราบสายพันธุ์'],
            'กระต่าย' => ['ฮอลแลนด์ลอป', 'ไลอ้อนเฮด', 'ไม่ทราบสายพันธุ์'],
            'หนูแฮมสเตอร์' => ['ซีเรียน', 'แคมป์เบลล์', 'วินเทอร์ไวท์', 'โรโบรอฟสกี้', 'ไม่ทราบสายพันธุ์'],
            'หมู' => ['หมูพันธุ์ไทย', 'หมูพันธุ์ต่างประเทศ', 'หมูพันธุ์ผสม', 'ไม่ทราบสายพันธุ์'],
            'โค' => ['โคพื้นเมือง', 'โคบราห์มัน', 'โคชาร์โรเล่ส์', 'ไม่ทราบสายพันธุ์'],
            'อื่น ๆ' => ['ไม่ทราบสายพันธุ์'],
        ];

        foreach ($breeds as $speciesName => $names) {
            $species = Species::where('name', $speciesName)->first();
            if (!$species) {
                continue;
            }
            foreach ($names as $name) {
                Breed::firstOrCreate(['species_id' => $species->id, 'name' => $name]);
            }
        }
    }
}
