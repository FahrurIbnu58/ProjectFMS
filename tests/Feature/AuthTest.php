<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_success(): void
    {
        User::factory()->create(['email' => 'admin@example.com', 'password' => bcrypt('password'), 'role' => Role::Administrator->value]);

        $res = $this->postJson('/api/login', ['email' => 'admin@example.com', 'password' => 'password']);
        $res->assertOk()->assertJsonStructure(['data' => ['user', 'token']]);
    }

    public function test_login_invalid(): void
    {
        $res = $this->postJson('/api/login', ['email' => 'nope@example.com', 'password' => 'wrong']);
        $res->assertStatus(422);
    }

    public function test_me_and_logout(): void
    {
        $user = User::factory()->create(['role' => Role::Administrator->value]);
        $token = $user->createToken('api')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/me')->assertOk();
        $this->withHeader('Authorization', "Bearer {$token}")->postJson('/api/logout')->assertOk();
    }
}
