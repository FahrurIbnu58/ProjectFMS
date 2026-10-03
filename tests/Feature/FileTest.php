<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Department;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FileTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_search_filter_download(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => Role::Administrator->value]);
        $viewer = User::factory()->create(['role' => Role::Viewer->value]);
        $folder = Folder::factory()->create(['created_by' => $admin->id]);
        $dept = Department::factory()->create();

        Sanctum::actingAs($admin);
        $res = $this->postJson('/api/files', [
            'folder_id' => $folder->id,
            'department_id' => $dept->id,
            'title' => 'My Report',
            'file' => UploadedFile::fake()->create('report.pdf', 100, 'application/pdf'),
        ]);
        $res->assertCreated();
        $id = $res->json('data.id');

        // search
        $this->getJson('/api/files?q=Report')->assertOk()->assertJsonFragment(['title' => 'My Report']);
        // filter
        $this->getJson("/api/files?folder_id={$folder->id}")->assertOk();
        $this->getJson("/api/files?department_id={$dept->id}")->assertOk();

        // viewer can download/preview
        Sanctum::actingAs($viewer);
        $this->getJson("/api/files/{$id}")->assertOk();
        $this->get("/api/files/{$id}/download")->assertOk();
        $this->get("/api/files/{$id}/preview")->assertOk();

        // viewer cannot upload
        $this->postJson('/api/files', [
            'folder_id' => $folder->id,
            'department_id' => $dept->id,
            'title' => 'X',
            'file' => UploadedFile::fake()->create('x.pdf', 10, 'application/pdf'),
        ])->assertForbidden();
    }
}
