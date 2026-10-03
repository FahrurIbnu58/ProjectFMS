<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class FileSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first() ?? User::first();
        $folders = Folder::all();
        $departments = Department::all();
        if (! $admin || $folders->isEmpty() || $departments->isEmpty()) {
            return;
        }

        Storage::disk('public')->makeDirectory('documents');

        for ($i = 1; $i <= 12; $i++) {
            $content = "Dummy file #{$i}\nGenerated for seeding.\n";
            $fileName = "seed-doc-{$i}.txt";
            $path = "documents/{$fileName}";
            Storage::disk('public')->put($path, $content);

            $folder = $folders[($i - 1) % $folders->count()];
            $dept = $departments[($i - 1) % $departments->count()];

            File::firstOrCreate(
                ['title' => "Seed Document {$i}"],
                [
                    'folder_id' => $folder->id,
                    'department_id' => $dept->id,
                    'file_name' => $fileName,
                    'file_path' => $path,
                    'mime_type' => 'text/plain',
                    'size' => strlen($content),
                    'uploaded_by' => $admin->id,
                ]
            );
        }
    }
}
