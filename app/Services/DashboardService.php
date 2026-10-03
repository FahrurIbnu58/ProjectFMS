<?php

namespace App\Services;

use App\Models\Department;
use App\Models\File;
use App\Models\Folder;

class DashboardService
{
    public function stats(): array
    {
        $latest = File::with(['folder', 'department', 'uploader'])->latest()->limit(10)->get();

        return [
            'total_folders' => Folder::count(),
            'total_files' => File::count(),
            'total_departments' => Department::count(),
            'latest_files' => $latest,
        ];
    }
}
