<?php

namespace Database\Seeders;

use App\Models\Pet;
use App\Models\Photo;
use App\Models\Report;
use App\Models\Species;
use App\Models\Subdistrict;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            SpeciesSeeder::class,
            GeographySeeder::class,
        ]);

        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        if (Pet::exists() && Report::exists()) {
            return; // ข้อมูลตัวอย่างมีแล้ว ไม่สร้างซ้ำ
        }

        $users = User::where('role', 'user')->get();
        if ($users->isEmpty()) {
            return; // กัน random() บน collection ว่าง (เช่น DB มีแต่แอดมิน)
        }
        $photoPath = function (Species $s) {
            $key = match ($s->name) {
                'สุนัข' => 'dog',
                'แมว' => 'cat',
                'นก' => 'bird',
                'กระต่าย' => 'rabbit',
                'หนูแฮมสเตอร์' => 'hamster',
                default => 'dog',
            };

            return "/images/sample/{$key}.svg";
        };
        $subIds = Subdistrict::inRandomOrder()->limit(40)->pluck('id');

        // สัตว์เลี้ยง 20 ตัว กระจายให้ user 2 คน + รูป 1 รูป
        $pets = Pet::factory(20)->create(['user_id' => fn() => $users->random()->id]);
        foreach ($pets as $pet) {
            $pet->photos()->save(Photo::factory()->make(['path' => $photoPath($pet->species)]));
        }

        // ประกาศ "หาย" 12 รายการ (ผูกกับสัตว์ของเจ้าของ) + ประกาศ "พบ" 8 รายการ (ไม่รู้เจ้าของ)
        foreach ($pets->random(12) as $pet) {
            $r = Report::factory()->create([
                'user_id' => $pet->user_id,
                'pet_id' => $pet->id,
                'type' => 'lost',
                'species_id' => $pet->species_id,
                'breed_id' => $pet->breed_id,
                'color' => $pet->color,
                'title' => "ตามหา{$pet->name} {$pet->species->name}หาย",
                'subdistrict_id' => $subIds->random(),
                'contact_phone' => $pet->user->phone,
            ]);
            $r->photos()->save(Photo::factory()->make(['path' => $photoPath($pet->species)]));
        }

        foreach (range(1, 8) as $i) {
            $species = Species::inRandomOrder()->first();
            $r = Report::factory()->create([
                'user_id' => $users->random()->id,
                'pet_id' => null,
                'type' => 'found',
                'species_id' => $species->id,
                'subdistrict_id' => $subIds->random(),
                'title' => "พบ{$species->name}หลงทาง ตามหาเจ้าของ",
            ]);
            $r->photos()->save(Photo::factory()->make(['path' => $photoPath($species)]));
        }
    }
}
