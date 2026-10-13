<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiRegistrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_form_encoded_registration_errors_are_returned_as_json_without_accept_header()
    {
        $response = $this->call('POST', '/api/register', [
            'name' => 'Test User',
            'email' => 'registration-errors@example.com',
            'password' => '1111',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => false,
                'message' => 'ข้อมูลที่ส่งมาไม่ถูกต้อง',
            ])
            ->assertJsonStructure(['errors' => ['password']]);
    }

    public function test_form_encoded_registration_creates_user_and_returns_token()
    {
        $response = $this->call('POST', '/api/register', [
            'name' => 'Test User',
            'email' => 'registration-success@example.com',
            'phone' => '0812345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.name', 'Test User')
            ->assertJsonStructure(['token']);

        $this->assertDatabaseHas('users', [
            'email' => 'registration-success@example.com',
            'phone' => '0812345678',
        ]);

        $user = User::where('email', 'registration-success@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('password123', $user->password));
    }
}
