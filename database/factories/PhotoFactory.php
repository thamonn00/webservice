<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PhotoFactory extends Factory
{
    public function definition()
    {
        // ใช้คู่กับ ->has(Photo::factory(), 'photos') — photoable_type/id
        // ถูกเซ็ตอัตโนมัติจากเจ้าของ (Pet/Report) ไม่ต้องใส่เอง
        $key = $this->faker->randomElement(['dog', 'cat', 'bird', 'rabbit', 'hamster']);

        return [
            'path' => "/images/sample/{$key}.svg",
            'is_primary' => false,
        ];
    }

    public function configure()
    {
        // ให้รูปแรกของแต่ละเจ้าของเป็นรูปหลักเพียงรูปเดียว
        // + แก้ path ให้ตรงชนิดสัตว์ของเจ้าของ (สุนัข=>dog ฯลฯ)
        return $this->afterCreating(function ($photo) {
            $hasPrimary = $photo->newQuery()
                ->where('photoable_type', $photo->photoable_type)
                ->where('photoable_id', $photo->photoable_id)
                ->where('is_primary', true)
                ->exists();

            $updates = [];

            if (! $hasPrimary) {
                $updates['is_primary'] = true;
            }

            // รูปต้องตรงชนิดสัตว์: โหลดเจ้าของ (Pet/Report) + species แล้ว map เป็นไฟล์ตัวอย่าง
            $owner = $photo->photoable;
            $speciesName = $owner?->species?->name;
            $key = match ($speciesName) {
                'สุนัข' => 'dog',
                'แมว' => 'cat',
                'นก' => 'bird',
                'กระต่าย' => 'rabbit',
                'หนูแฮมสเตอร์' => 'hamster',
                default => 'other',
            };
            $expected = "/images/sample/{$key}.svg";
            if ($photo->path !== $expected) {
                $updates['path'] = $expected;
            }

            if ($updates !== []) {
                $photo->update($updates);
            }
        });
    }
}

