<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    public function definition()
    {
        return [
            'pet_id' => Pet::factory(),
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['lost', 'found']),
            'title' => $this->faker->sentence(5),
            'description' => $this->faker->paragraph(),
            'province' => 'กรุงเทพมหานคร',
            'district' => 'บางรัก',
            'subdistrict' => 'สีลม',
            'place' => $this->faker->streetAddress(),
            'latitude' => $this->faker->latitude(5, 21),
            'longitude' => $this->faker->longitude(97, 106),
            'seen_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'contact_phone' => $this->faker->numerify('0#########'),
            'status' => 'active',
        ];
    }

    public function configure()
    {
        // เจ้าของรายงาน = เจ้าของสัตว์ (ถ้าไม่ได้กำหนด user มาให้)
        return $this->afterCreating(function ($report) {
            if ($report->pet && $report->user_id !== $report->pet->user_id) {
                $report->updateQuietly(['user_id' => $report->pet->user_id]);
            }
        });
    }
}
