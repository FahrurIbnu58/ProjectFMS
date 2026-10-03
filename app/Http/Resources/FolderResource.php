<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FolderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'parent_id' => $this->parent_id,
            'parent' => $this->whenLoaded('parent', fn () => $this->parent ? ['id' => $this->parent->id, 'name' => $this->parent->name] : null),
            'children_count' => $this->whenCounted('children'),
            'files_count' => $this->whenCounted('files'),
            'children' => FolderResource::collection($this->whenLoaded('children')),
            'files' => $this->whenLoaded('files'),
            'breadcrumbs' => $this->when($this->relationLoaded('parent') || true, function () {
                try {
                    return $this->resource->breadcrumbs();
                } catch (\Throwable) {
                    return [];
                }
            }),
            'creator' => $this->whenLoaded('creator', fn () => $this->creator ? ['id' => $this->creator->id, 'name' => $this->creator->name] : null),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
