<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use RuntimeException;

abstract class GeoJsonSeeder extends Seeder
{
    protected function rows(string $filename, array $requiredKeys): array
    {
        $path = database_path('data/'.$filename);
        if (!is_file($path)) {
            throw new RuntimeException("Geographic JSON file not found: {$path}");
        }

        $json = file_get_contents($path);
        if ($json === false) {
            throw new RuntimeException("Unable to read geographic JSON file: {$path}");
        }

        $rows = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($rows)) {
            throw new RuntimeException("Geographic JSON must contain an array: {$path}");
        }

        foreach ($rows as $index => $row) {
            if (!is_array($row)) {
                throw new RuntimeException("Invalid row {$index} in geographic JSON: {$path}");
            }

            foreach ($requiredKeys as $key) {
                if (!array_key_exists($key, $row)) {
                    throw new RuntimeException("Missing {$key} at row {$index} in geographic JSON: {$path}");
                }
            }
        }

        return $rows;
    }
}
