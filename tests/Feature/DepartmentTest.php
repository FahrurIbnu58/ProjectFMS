<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_crud_and_viewer_read_only(): void
    {
        $admin = User::factory()->create(['role' => Role::Administrator->value]);
        $viewer = User::factory()->create(['role' => Role::Viewer->value]);

        Sanctum::actingAs($admin);
        $res = $this->postJson('/api/departments', ['name' => 'HR', 'description' => 'Human Resources']);
        $res->assertCreated();
        $id = $res->json('data.id');

        $this->getJson('/api/departments')->assertOk();

        Sanctum::actingAs($viewer);
        $this->getJson('/api/departments')->assertOk();
        $this->postJson('/api/departments', ['name' => 'IT'])->assertForbidden();
        $this->putJson("/api/departments/{$id}", ['name' => 'HR2'])->assertForbidden();
        $this->deleteJson("/api/departments/{$id}")->assertForbidden();
    }
}
