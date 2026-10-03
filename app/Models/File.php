<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'files';

    protected $fillable = [
        'folder_id',
        'department_id',
        'title',
        'file_name',
        'file_path',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class)->withTrashed();
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class)->withTrashed();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->file_path ? Storage::disk('public')->url($this->file_path) : null,
        );
    }

    protected function downloadUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->exists ? url("/api/files/{$this->id}/download") : null,
        );
    }
}
