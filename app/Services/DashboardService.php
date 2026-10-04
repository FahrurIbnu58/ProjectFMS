<?php

namespace App\Services;

use App\Models\File;
use App\Models\Folder;
use App\Models\Department;
use App\Models\ActivityLog;

class DashboardService
{
    public function stats()
    {
        $user = auth()->user();
        $role = is_object($user->role) ? ($user->role->value ?? $user->role->name ?? '') : $user->role;
        $isAdmin = in_array(strtolower($role), ['administrator', 'admin']);

        if ($isAdmin) {
            return [
                'is_admin' => true,
                'total_folders' => Folder::count(),
                'total_files' => File::count(),
                'total_departments' => Department::count(),
                'latest_files' => File::with('folder')->latest()->take(7)->get(),
            ];
        }

        // Statistik Khusus User/Viewer
        $myFilesCount = File::where('uploaded_by', $user->id)->count();

        // Menghitung log download milik user ini
        $totalDownloads = ActivityLog::where('user_id', $user->id)
            ->where('action', 'download')
            ->count();

        // Menghitung total kapasitas penyimpanan dari berkas yang diunggah user
        $totalBytes = File::where('uploaded_by', $user->id)->sum('size');
        $storageFormatted = $this->formatBytes($totalBytes);

        return [
            'is_admin' => false,
            'my_files' => $myFilesCount,
            'total_downloads' => $totalDownloads,
            'storage_used' => $storageFormatted,
            'latest_files' => File::with('folder')->where('uploaded_by', $user->id)->latest()->take(5)->get(),
        ];
    }

    private function formatBytes($bytes, $precision = 2)
    {
        if ($bytes <= 0) return '0 KB';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
