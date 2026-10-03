<?php

namespace App\Services;

use App\Models\Folder;
use App\Models\User;
use App\Repositories\FolderRepository;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class FolderService
{
    use LogsActivity;

    public function __construct(protected FolderRepository $folders)
    {
    }

    public function list(?int $parentId, int $perPage = 15)
    {
        $query = Folder::with(['creator'])->withCount(['children', 'files'])->latest();
        if (is_null($parentId)) {
            // no filter when param absent? caller decides; support explicit filter via 'filter' flag
        }

        return $query;
    }

    public function create(array $data, User $user): Folder
    {
        if (! empty($data['parent_id'])) {
            Folder::findOrFail($data['parent_id']);
        }
        $data['created_by'] = $user->id;
        $folder = $this->folders->create($data);
        static::log($user, 'folder.created', $folder, "Created folder {$folder->name}");

        return $folder->load(['creator'])->loadCount(['children', 'files']);
    }

    public function rename(Folder $folder, array $data, User $user): Folder
    {
        if (array_key_exists('parent_id', $data)) {
            $this->assertNoCycle($folder, $data['parent_id']);
            if (! is_null($data['parent_id'])) {
                Folder::findOrFail($data['parent_id']);
            }
        }
        $folder->update($data);
        static::log($user, 'folder.updated', $folder, "Updated folder {$folder->name}");

        return $folder->fresh(['parent', 'creator'])->loadCount(['children', 'files']);
    }

    public function delete(Folder $folder, User $user): void
    {
        $this->deleteRecursive($folder);
        static::log($user, 'folder.deleted', $folder, "Deleted folder {$folder->name}");
    }

    protected function deleteRecursive(Folder $folder): void
    {
        $folder->loadMissing(['children', 'files']);
        foreach ($folder->children as $child) {
            $this->deleteRecursive($child->fresh(['children', 'files']));
        }
        foreach ($folder->files()->withTrashed()->get() as $file) {
            if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            $file->delete();
        }
        $folder->delete();
    }

    public function assertNoCycle(Folder $folder, mixed $newParentId): void
    {
        if (is_null($newParentId)) {
            return;
        }
        if ((int) $newParentId === (int) $folder->id) {
            throw ValidationException::withMessages(['parent_id' => 'Folder cannot be its own parent.']);
        }
        $parent = Folder::find($newParentId);
        if ($parent && $parent->isDescendantOf($folder)) {
            throw ValidationException::withMessages(['parent_id' => 'Folder cannot be moved under its own descendant.']);
        }
    }

    public function tree()
    {
        return Folder::with(['children', 'creator'])->whereNull('parent_id')->latest()->get();
    }
}
