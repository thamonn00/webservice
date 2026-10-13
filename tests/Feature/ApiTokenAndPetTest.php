<?php

namespace Tests\Feature;

use App\Models\Breed;
use App\Models\Pet;
use App\Models\Report;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class ApiTokenAndPetTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_token_can_access_api_and_logout_revokes_it()
    {
        $user = User::factory()->create([
            'email' => 'token-test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertUnauthorized()
            ->assertJsonStructure(['status', 'message', 'data']);
        $this->postJson('/api/pet', [])->assertUnauthorized();

        $token = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertOk()->json('token');

        $this->assertNotEmpty($token);
        $this->withToken($token)->getJson('/api/me')
            ->assertOk()
            ->assertJsonStructure(['status', 'message', 'data']);
        $this->withToken($token)->postJson('/api/logout')->assertOk();
        $this->assertDatabaseMissing('personal_access_tokens', [
            'token' => hash('sha256', $token),
        ]);
        $this->app['auth']->forgetGuards();
        $this->withToken($token)->getJson('/api/me')->assertUnauthorized();
    }

    public function test_all_non_public_api_routes_require_a_token_and_return_json()
    {
        $routes = [
            ['POST', '/api/logout'],
            ['GET', '/api/me'],
            ['GET', '/api/pet'],
            ['GET', '/api/pet/1'],
            ['POST', '/api/pet'],
            ['PUT', '/api/pet/1'],
            ['DELETE', '/api/pet/1'],
            ['GET', '/api/species'],
            ['GET', '/api/species/1'],
            ['GET', '/api/breeds'],
            ['GET', '/api/breeds/1'],
            ['GET', '/api/provinces'],
            ['GET', '/api/provinces/1'],
            ['GET', '/api/districts'],
            ['GET', '/api/districts/1'],
            ['GET', '/api/subdistricts'],
            ['GET', '/api/subdistricts/1'],
        ];

        foreach ($routes as [$method, $uri]) {
            $response = $this->call($method, $uri);

            $response->assertUnauthorized()
                ->assertJsonStructure(['status', 'message', 'data'])
                ->assertHeader('Content-Type', 'application/json');
        }
    }

    public function test_expired_token_cannot_access_api()
    {
        config(['sanctum.expiration' => 1]);

        $user = User::factory()->create();
        $token = $user->createToken('expired-test')->plainTextToken;
        $user->tokens()->firstOrFail()->forceFill([
            'created_at' => now()->subMinutes(2),
        ])->save();

        $this->withToken($token)->getJson('/api/me')->assertUnauthorized();
    }

    public function test_api_allows_cross_origin_preflight_requests_for_web_apps()
    {
        $this->call('OPTIONS', '/api/pet', [], [], [], [
            'HTTP_ORIGIN' => 'http://webapp.example',
            'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'GET',
        ])->assertNoContent()
            ->assertHeader('Access-Control-Allow-Origin', '*');
    }

    public function test_user_can_update_own_pet_but_not_another_users_pet()
    {
        $species = Species::create(['name' => 'ทดสอบสัตว์']);
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $pet = Pet::create([
            'user_id' => $owner->id,
            'species_id' => $species->id,
            'name' => 'ชื่อเดิม',
        ]);
        $token = $otherUser->createToken('pet-test')->plainTextToken;

        $this->withToken($token)
            ->patchJson('/api/pet/'.$pet->id, ['name' => 'แก้ชื่อ'])
            ->assertForbidden()
            ->assertJsonStructure(['status', 'message', 'data']);

        $this->withToken($token)->deleteJson('/api/pet/'.$pet->id)
            ->assertForbidden()
            ->assertJsonStructure(['status', 'message', 'data']);
        $this->assertDatabaseHas('pets', ['id' => $pet->id, 'name' => 'ชื่อเดิม']);

        $this->app['auth']->forgetGuards();
        $ownerToken = $owner->createToken('owner-test')->plainTextToken;
        $this->withToken($ownerToken)
            ->patchJson('/api/pet/'.$pet->id, ['name' => 'ชื่อใหม่'])
            ->assertOk()
            ->assertJsonPath('data.name', 'ชื่อใหม่');
    }

    public function test_pet_cannot_use_a_breed_from_another_species()
    {
        $species = Species::create(['name' => 'ชนิดสัตว์ A']);
        $otherSpecies = Species::create(['name' => 'ชนิดสัตว์ B']);
        $breed = Breed::create(['species_id' => $species->id, 'name' => 'พันธุ์ A']);
        $user = User::factory()->create();
        $token = $user->createToken('breed-test')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/pet', [
                'species_id' => $otherSpecies->id,
                'breed_id' => $breed->id,
                'name' => 'ทดสอบ',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('breed_id');
    }

    public function test_admin_can_delete_another_users_pet()
    {
        $species = Species::create(['name' => 'ชนิดสัตว์ admin']);
        $owner = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $pet = Pet::create([
            'user_id' => $owner->id,
            'species_id' => $species->id,
            'name' => 'สัตว์ของผู้ใช้อื่น',
        ]);
        $token = $admin->createToken('admin-test')->plainTextToken;

        $this->withToken($token)
            ->deleteJson('/api/pet/'.$pet->id)
            ->assertOk()
            ->assertJsonPath('data', null);

        $this->assertDatabaseMissing('pets', ['id' => $pet->id]);
    }

    public function test_authenticated_user_owns_new_pets_automatically()
    {
        $species = Species::create(['name' => 'ชนิดสัตว์ของฉัน']);
        $user = User::factory()->create();
        $token = $user->createToken('create-pet-test')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/pet', [
                'species_id' => $species->id,
                'name' => 'สัตว์ใหม่',
                'user_id' => 999999,
            ])
            ->assertCreated()
            ->assertJsonPath('data.user_id', $user->id);
    }

    public function test_null_pet_gender_is_normalized_to_unknown()
    {
        $species = Species::create(['name' => 'ชนิดสัตว์ไม่ระบุเพศ']);
        $user = User::factory()->create();
        $token = $user->createToken('null-gender-test')->plainTextToken;

        $petId = $this->withToken($token)
            ->postJson('/api/pet', [
                'species_id' => $species->id,
                'name' => 'ทดสอบเพศ',
                'gender' => null,
            ])
            ->assertCreated()
            ->assertJsonPath('data.gender', 'unknown')
            ->json('data.id');

        $this->withToken($token)
            ->patchJson('/api/pet/'.$petId, ['gender' => null])
            ->assertOk()
            ->assertJsonPath('data.gender', 'unknown');
    }

    public function test_deleting_user_removes_pet_and_report_photos_and_files()
    {
        Storage::fake('public');
        $species = Species::create(['name' => 'ชนิดสัตว์ลบผู้ใช้']);
        $user = User::factory()->create();
        $pet = Pet::create([
            'user_id' => $user->id,
            'species_id' => $species->id,
            'name' => 'สัตว์ที่จะลบ',
        ]);
        $report = Report::create([
            'user_id' => $user->id,
            'pet_id' => $pet->id,
            'species_id' => $species->id,
            'type' => 'lost',
            'title' => 'ประกาศที่จะลบ',
            'event_at' => now(),
            'contact_phone' => '0812345678',
        ]);

        Storage::disk('public')->put('pets/test.jpg', 'pet image');
        Storage::disk('public')->put('reports/test.jpg', 'report image');
        $pet->photos()->create(['path' => 'pets/test.jpg', 'is_primary' => true]);
        $report->photos()->create(['path' => 'reports/test.jpg', 'is_primary' => true]);

        $user->delete();

        $this->assertDatabaseMissing('pets', ['id' => $pet->id]);
        $this->assertDatabaseMissing('reports', ['id' => $report->id]);
        $this->assertDatabaseMissing('photos', ['photoable_id' => $pet->id, 'photoable_type' => Pet::class]);
        $this->assertDatabaseMissing('photos', ['photoable_id' => $report->id, 'photoable_type' => Report::class]);
        Storage::disk('public')->assertMissing('pets/test.jpg');
        Storage::disk('public')->assertMissing('reports/test.jpg');
    }

    public function test_api_not_found_and_validation_errors_use_the_standard_json_envelope()
    {
        $user = User::factory()->create();
        $token = $user->createToken('error-test')->plainTextToken;

        $this->withToken($token)->get('/api/pet/999999')
            ->assertNotFound()
            ->assertJsonStructure(['status', 'message', 'data']);

        $this->withToken($token)->post('/api/pet', [])
            ->assertUnprocessable()
            ->assertJsonStructure(['status', 'message', 'data', 'errors']);
    }

    public function test_api_docs_describe_token_requirements_and_sequential_examples()
    {
        $response = $this->get('/api-docs')
            ->assertOk()
            ->assertSee('Step 1')
            ->assertSee('Step 4')
            ->assertSee('/pet')
            ->assertSee('/api/subdistricts/1')
            ->assertSee('ต้องมี token')
            ->assertSee('somchai@example.com')
            ->assertSee('ถ้าแก้/ลบได้ 403')
            ->assertSee('curl.exe')
            ->assertSee('JSON ตัวอย่างจากข้อมูลสัตว์ในฐานข้อมูล');

        $this->assertSame(19, substr_count($response->getContent(), 'คัดลอก URL'));
    }

    public function test_logged_in_web_user_can_create_a_token_for_thunder_client()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/api-docs')
            ->assertOk()
            ->assertSee('สร้าง Token สำหรับ Thunder');

        $response = $this->postJson('/api-token')
            ->assertCreated()
            ->assertJsonStructure(['status', 'message', 'data', 'token'])
            ->assertJsonPath('status', true);

        $plainTextToken = $response->json('token');
        $tokenSecret = explode('|', $plainTextToken, 2)[1] ?? '';
        $this->assertNotEmpty($plainTextToken);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'Thunder Client',
            'token' => hash('sha256', $tokenSecret),
        ]);
    }

    public function test_guest_cannot_create_a_thunder_client_token()
    {
        $this->postJson('/api-token')->assertUnauthorized();
    }
}
