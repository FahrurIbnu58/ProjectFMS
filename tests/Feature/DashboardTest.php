<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_stats(): void
    {
        $user = User::factory()->create(['role' => Role::Viewer->value]);
        Sanctum::actingAs($user);
        $this->getJson('/api/dashboard')->assertOk()->assertJsonStructure([
            'data' => ['total_folders', 'total_files', 'total_departments', 'latest_files'],
        ]);
    }

    public function test_activity_logs_admin_only(): void
    {
        $viewer = User::factory()->create(['role' => Role::Viewer->value]);
        Sanctum::actingAs($viewer);
        $this->getJson('/api/activity-logs')->assertForbidden();

        $admin = User::factory()->create(['role' => Role::Administrator->value]);
        Sanctum::actingAs($admin);
        $this->getJson('/api/activity-logs')->assertOk();
    }
}
