<?php

namespace Database\Factories;

use App\Models\Species;
use App\Models\Subdistrict;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    public function definition()
    {
        $type = $this->faker->randomElement(['lost', 'found']);
        $sub = Subdistrict::inRandomOrder()->first();

        return [
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'pet_id' => null,
            'species_id' => Species::inRandomOrder()->value('id') ?? 1,
            'breed_id' => null,
            'type' => $type,
            'title' => $type === 'lost' ? 'ตามหาน้องหาย ใครเจอช่วยแจ้งด้วย' : 'พบสัตว์เลี้ยงหลงทาง ตามหาเจ้าของ',
            'color' => $this->faker->randomElement(['ขาว', 'ดำ', 'น้ำตาล', 'ส้ม', 'เทา', 'ขาว-ดำ']),
            'description' => $this->faker->randomElement([
                'หายจากบ้านช่วงเย็น ใส่ปลอกคอ ตอบสนองเมื่อเรียกชื่อ',
                'เจอเดินอยู่ริมถนน ดูกลัว ๆ ตอนนี้พักอยู่กับผู้พบ',
                'ขนฟู ตัวไม่ใหญ่ มีรางวัลนำจับ',
            ]),
            'subdistrict_id' => $sub?->id,
            'place' => $this->faker->randomElement(['หน้าตลาดสด', 'ใกล้โรงเรียน', 'ปั๊มน้ำมัน', 'ซอยข้างวัด', 'หมู่บ้านจัดสรร']),
            'latitude' => $this->faker->latitude(13.5, 18.5),
            'longitude' => $this->faker->longitude(99.5, 103),
            'event_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'contact_phone' => '08' . $this->faker->numerify('########'),
            'status' => $this->faker->randomElement(['open', 'open', 'open', 'closed']),
        ];
    }
}
