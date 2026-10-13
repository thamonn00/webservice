<?php

namespace Database\Seeders;

use App\Models\Breed;
use App\Models\Species;
use Illuminate\Database\Seeder;

class SpeciesSeeder extends Seeder
{
    /** ชนิดสัตว์ + สายพันธุ์ (รันซ้ำได้ ไม่เพิ่มข้อมูลซ้ำ) */
    public function run()
    {
        $data = [
            'สุนัข' => ['ไทยหลังอาน', 'บางแก้ว', 'ชิวาวา', 'โกลเดน รีทรีฟเวอร์', 'ชิบะ อินุ', 'ปอมเมอเรเนียน', 'พุดเดิ้ล', 'ปั๊ก', 'ไซบีเรียน ฮัสกี้', 'ลาบราดอร์', 'พันธุ์ผสม'],
            'แมว' => ['วิเชียรมาศ', 'โคราช (สีสวาด)', 'ขาวมณี', 'เปอร์เซีย', 'สก็อตติช โฟลด์', 'บริติช ช็อตแฮร์', 'เมนคูน', 'เบงกอล', 'พันธุ์ผสม'],
            'นก' => ['หงส์หยก', 'ค็อกคาเทล', 'ฟอพัส', 'นกแก้วมาคอว์', 'นกขุนทอง', 'นกกรงหัวจุก'],
            'กระต่าย' => ['ฮอลแลนด์ ลอป', 'ไลออนเฮด', 'เนเธอร์แลนด์ ดวอร์ฟ'],
            'หนูแฮมสเตอร์' => ['ซีเรียน', 'วินเทอร์ ไวท์', 'โรโบรอฟสกี้'],
            'อื่น ๆ' => [],
        ];

        foreach ($data as $speciesName => $breeds) {
            $species = Species::firstOrCreate(['name' => $speciesName]);
            foreach ($breeds as $breed) {
                Breed::firstOrCreate(['species_id' => $species->id, 'name' => $breed]);
            }
        }
    }
}
