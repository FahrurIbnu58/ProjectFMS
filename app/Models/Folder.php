<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Folder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'parent_id', 'created_by'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id')->withTrashed();
    }

    public function children(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    /** @return array<int, array{id:int,name:string}> ancestors root -> self */
    public function breadcrumbs(): array
    {
        $trail = [];
        $current = $this;
        $guard = 0;
        while ($current && $guard < 100) {
            $trail[] = ['id' => $current->id, 'name' => $current->name];
            $current = $current->parent;
            $guard++;
        }

        return array_reverse($trail);
    }

    public function isDescendantOf(Folder $folder): bool
    {
        $current = $this->parent;
        $guard = 0;
        while ($current && $guard < 100) {
            if ($current->id === $folder->id) {
                return true;
            }
            $current = $current->parent;
            $guard++;
        }

        return false;
    }
}
