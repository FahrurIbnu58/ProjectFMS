<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends Factory<File>
 */
class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition(): array
    {
        return [
            'folder_id' => Folder::factory(),
            'department_id' => Department::factory(),
            'title' => fake()->sentence(3),
            'file_name' => 'dummy.txt',
            'file_path' => 'documents/dummy.txt',
            'mime_type' => 'text/plain',
            'size' => 100,
            'uploaded_by' => User::factory(),
        ];
    }
}
