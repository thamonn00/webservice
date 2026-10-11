<?php

namespace Database\Factories;

use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReportPhoto>
 */
class ReportPhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'report_id' => Report::query()->inRandomOrder()->value('id') ?? Report::factory(),
            'photo' => 'https://placehold.co/640x480?text=Report+'.rawurlencode($this->faker->uuid()),
        ];
    }
}
