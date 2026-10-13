<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PhotoFactory extends Factory
{
    public function definition()
    {
        $key = $this->faker->randomElement(['dog', 'cat', 'bird', 'rabbit', 'hamster']);

        return [
            'path' => "/images/sample/{$key}.svg",
            'is_primary' => true,
        ];
    }
}
