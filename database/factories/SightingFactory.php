<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sighting>
 */
class SightingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'report_id' => Report::factory(),
            'user_id' => User::factory(),
            'message' => $this->faker->paragraph(),
            'province' => 'กรุงเทพมหานคร',
            'district' => 'บางรัก',
            'subdistrict' => 'สีลม',
            'seen_place' => $this->faker->streetAddress(),
            'latitude' => $this->faker->latitude(5, 21),
            'longitude' => $this->faker->longitude(97, 106),
            'seen_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'contact' => $this->faker->optional()->numerify('0#########'),
        ];
    }
}
