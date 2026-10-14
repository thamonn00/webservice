<?php

namespace Database\Factories;

use App\Models\Breed;
use App\Models\Species;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetFactory extends Factory
{
    public function definition()
    {
        $species = Species::inRandomOrder()->first();
        if (!$species) {
            $species = Species::firstOrCreate(['name' => 'อื่น ๆ']);
        }
        $breed = Breed::where('species_id', $species->id)->inRandomOrder()->first();
        if ($breed) {
            $breed_id = $breed->id;
        } else {
            $breed_id = null;
        }
        $user_id = User::inRandomOrder()->value('id');
        if (!$user_id) {
            $user = User::factory()->create();
            $user_id = $user->id;
        }

        return [
            'user_id' => $user_id,
            'species_id' => $species->id,
            'breed_id' => $breed_id,
            'name' => $this->faker->randomElement(['ถุงเงิน', 'มะลิ', 'ข้าวปุ้น', 'โมจิ', 'ทองดี', 'น้ำตาล', 'บราวนี่', 'ลัคกี้', 'ส้มโอ', 'หมูตุ๋น', 'ซูชิ', 'ดำ', 'เจ้าขาว', 'ลูกชิ้น', 'ปุยฝ้าย']),
            'color' => $this->faker->randomElement(['ขาว', 'ดำ', 'น้ำตาล', 'ส้ม', 'เทา', 'ขาว-ดำ', 'ส้ม-ขาว', 'ลายสามสี']),
            'gender' => $this->faker->randomElement(['male', 'female', 'unknown']),
            'age' => $this->faker->numberBetween(0, 15),
            'description' => $this->faker->randomElement([
                'ใส่ปลอกคอสีแดง มีกระพรวนที่คอ',
                'มีไมโครชิป ขี้อ้อน เป็นมิตรกับคน',
                'หางสั้น มีแผลเป็นที่หูซ้าย',
                'ขนยาวฟู ตาสองสี',
                'ขี้ตกใจ ถ้าเจอให้เรียกชื่อเบา ๆ',
            ]),
        ];
    }
}
