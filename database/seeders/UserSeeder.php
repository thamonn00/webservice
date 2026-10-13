<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /** สร้าง admin จาก .env และบัญชีทดสอบเฉพาะ local/testing */
    public function run()
    {
        $adminEmail = env('ADMIN_EMAIL');
        $adminPassword = env('ADMIN_PASSWORD');

        if ($adminEmail && $adminPassword) {
            User::updateOrCreate(['email' => $adminEmail], [
                'name' => env('ADMIN_NAME', 'ผู้ดูแลระบบ'),
                'role' => 'admin',
                'password' => Hash::make($adminPassword),
            ]);
        } else {
            $this->command->warn('ข้ามสร้าง admin: ตั้ง ADMIN_EMAIL และ ADMIN_PASSWORD ใน .env');
        }

        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        foreach (
            [
                ['name' => 'สมชาย ใจดี', 'email' => 'somchai@example.com', 'phone' => '0811111111'],
                ['name' => 'สมหญิง รักสัตว์', 'email' => 'somying@example.com', 'phone' => '0822222222'],
            ] as $account
        ) {
            User::updateOrCreate(['email' => $account['email']], $account + [
                'role' => 'user',
                'password' => Hash::make('password123'),
            ]);
        }
    }
}
