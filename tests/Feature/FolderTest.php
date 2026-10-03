<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FolderTest extends TestCase
{
    use RefreshDatabase;

    public function test_hierarchy_and_cycle_prevention(): void
    {
        $admin = User::factory()->create(['role' => Role::Administrator->value]);
        Sanctum::actingAs($admin);

        $root = $this->postJson('/api/folders', ['name' => 'Root'])->assertCreated()->json('data.id');
        $child = $this->postJson('/api/folders', ['name' => 'Child', 'parent_id' => $root])->assertCreated()->json('data.id');

        // cycle: set root parent to child
        $this->putJson("/api/folders/{$root}", ['parent_id' => $child])->assertStatus(422);
        // self parent
        $this->putJson("/api/folders/{$root}", ['parent_id' => $root])->assertStatus(422);

        // show includes breadcrumbs
        $this->getJson("/api/folders/{$child}")->assertOk()->assertJsonPath('data.parent_id', $root);

        // tree
        $this->getJson('/api/folders-tree')->assertOk();

        // viewer cannot create
        $viewer = User::factory()->create(['role' => Role::Viewer->value]);
        Sanctum::actingAs($viewer);
        $this->postJson('/api/folders', ['name' => 'Nope'])->assertForbidden();
    }
}
