<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\Species;
use App\Models\Subdistrict;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    public function definition()
    {
        $type = $this->faker->randomElement(['lost', 'found']);
        $sub_id = Subdistrict::inRandomOrder()->value('id');

        // ประกาศ "หาย" ผูกกับสัตว์เลี้ยงจริงของสมาชิก (ถ้ามี)
        $pet = $type === 'lost' ? Pet::with(['species', 'user'])->inRandomOrder()->first() : null;

        if ($pet) {
            $user_id = $pet->user_id;
            $species_id = $pet->species_id;
            $breed_id = $pet->breed_id;
            $color = $pet->color;
            $title = 'ตามหา' . $pet->name . ' ' . $pet->species->name . 'หาย';
            $phone = optional($pet->user)->phone ?: '08' . $this->faker->numerify('########');
        } else {
            $species = Species::inRandomOrder()->first() ?? Species::firstOrCreate(['name' => 'อื่น ๆ']);
            $user_id = User::inRandomOrder()->value('id') ?? User::factory()->create()->id;
            $species_id = $species->id;
            $breed_id = null;
            $color = $this->faker->randomElement(['ขาว', 'ดำ', 'น้ำตาล', 'ส้ม', 'เทา', 'ขาว-ดำ']);
            $title = $type === 'lost'
                ? 'ตามหาน้องหาย ใครเจอช่วยแจ้งด้วย'
                : 'พบ' . $species->name . 'หลงทาง ตามหาเจ้าของ';
            $phone = '08' . $this->faker->numerify('########');
        }

        return [
            'user_id' => $user_id,
            'pet_id' => $pet?->id,
            'species_id' => $species_id,
            'breed_id' => $breed_id,
            'type' => $type,
            'title' => $title,
            'color' => $color,
            'description' => $this->faker->randomElement([
                'หายจากบ้านช่วงเย็น ใส่ปลอกคอ ตอบสนองเมื่อเรียกชื่อ',
                'เจอเดินอยู่ริมถนน ดูกลัว ๆ ตอนนี้พักอยู่กับผู้พบ',
                'ขนฟู ตัวไม่ใหญ่ มีรางวัลนำจับ',
            ]),
            'subdistrict_id' => $sub_id,
            'place' => $this->faker->randomElement(['หน้าตลาดสด', 'ใกล้โรงเรียน', 'ปั๊มน้ำมัน', 'ซอยข้างวัด', 'หมู่บ้านจัดสรร']),
            'latitude' => $this->faker->latitude(13.5, 18.5),
            'longitude' => $this->faker->longitude(99.5, 103),
            'event_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'contact_phone' => $phone,
            'status' => $this->faker->randomElement(['open', 'open', 'open', 'closed']),
        ];
    }
}
