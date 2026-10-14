<?php

namespace Database\Seeders;

use App\Models\Pet;
use App\Models\Photo;
use App\Models\Report;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // ต้องรันก่อน: factory ของ Pet/Report ดึง user, species, subdistrict จากฐานข้อมูล
        $this->call(UserSeeder::class);
        $this->call(SpeciesSeeder::class);
        $this->call(GeographySeeder::class);
         
 
        if (! app()->environment(['local', 'testing'])) {
            return;
        }
 
        // กันรันซ้ำ: ถ้ามีข้อมูล demo แล้วให้ข้าม (Species/Geography/User รันซ้ำได้อยู่แล้ว)
        if (Pet::exists() || Report::exists()) {
            $this->command->info('ข้อมูลสัตว์/ประกาศมีอยู่แล้ว ข้ามการ seed demo (ลบข้อมูลเดิมหรือใช้ migrate:fresh --seed เพื่อเริ่มใหม่)');

            return;
        }

        // Pet ก่อน Report: ReportFactory ประเภท lost ต้องดึง Pet มาสุ่มผูก
        // Pet 100 + Report 100 ตัวละ 1 รูป (รูปแก้ path ตาม species อัตโนมัติใน PhotoFactory)
        Pet::factory(100)
            ->has(Photo::factory(1), 'photos')
            ->create();

        Report::factory(100)
            ->has(Photo::factory(1), 'photos')
            ->create();
    }
}

