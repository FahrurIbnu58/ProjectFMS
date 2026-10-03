<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'file_name' => $this->file_name,
            'file_path' => $this->file_path,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'folder_id' => $this->folder_id,
            'department_id' => $this->department_id,
            'folder' => $this->whenLoaded('folder', fn () => $this->folder ? ['id' => $this->folder->id, 'name' => $this->folder->name] : null),
            'department' => $this->whenLoaded('department', fn () => $this->department ? ['id' => $this->department->id, 'name' => $this->department->name] : null),
            'uploader' => $this->whenLoaded('uploader', fn () => $this->uploader ? ['id' => $this->uploader->id, 'name' => $this->uploader->name] : null),
            'file_url' => $this->file_path ? Storage::disk('public')->url($this->file_path) : null,
            'download_url' => url("/api/files/{$this->id}/download"),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
