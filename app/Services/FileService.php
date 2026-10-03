<?php

namespace App\Services;

use App\Models\File;
use App\Models\User;
use App\Repositories\FileRepository;
use App\Traits\LogsActivity;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    use LogsActivity;

    public function __construct(protected FileRepository $files)
    {
    }

    public function store(array $data, UploadedFile $uploaded, User $user): File
    {
        $path = $uploaded->store('documents', 'public');

        $file = $this->files->create([
            'folder_id' => $data['folder_id'],
            'department_id' => $data['department_id'],
            'title' => $data['title'],
            'file_name' => $uploaded->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $uploaded->getClientMimeType(),
            'size' => $uploaded->getSize() ?? 0,
            'uploaded_by' => $user->id,
        ]);

        static::log($user, 'file.uploaded', $file, "Uploaded file {$file->title}");

        return $file->load(['folder', 'department', 'uploader']);
    }

    public function updateMeta(File $file, array $data, ?UploadedFile $uploaded, User $user): File
    {
        if ($uploaded) {
            if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            $path = $uploaded->store('documents', 'public');
            $data['file_name'] = $uploaded->getClientOriginalName();
            $data['file_path'] = $path;
            $data['mime_type'] = $uploaded->getClientMimeType();
            $data['size'] = $uploaded->getSize() ?? 0;
        }
        $file->update($data);
        static::log($user, 'file.updated', $file, "Updated file {$file->title}");

        return $file->fresh(['folder', 'department', 'uploader']);
    }

    public function delete(File $file, User $user): void
    {
        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }
        static::log($user, 'file.deleted', $file, "Deleted file {$file->title}");
        $file->delete();
    }
}
