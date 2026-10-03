<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Requests\UpdateFolderRequest;
use App\Http\Resources\FolderResource;
use App\Models\Folder;
use App\Services\FolderService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FolderController extends Controller
{
    use ApiResponse;

    public function __construct(protected FolderService $service)
    {
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Folder::class);
        $perPage = (int) $request->integer('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        $query = Folder::with(['creator'])->withCount(['children', 'files'])->latest();
        if ($request->has('parent_id')) {
            $parentId = $request->input('parent_id');
            if ($parentId === 'null' || is_null($parentId) || $parentId === '') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $parentId);
            }
        }
        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->input('q').'%');
        }

        $paginator = $query->paginate($perPage);

        return response()->json([
            'message' => 'Folders retrieved.',
            'data' => FolderResource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function tree()
    {
        Gate::authorize('viewAny', Folder::class);

        $build = function ($parentId = null) use (&$build) {
            return Folder::withCount(['children', 'files'])
                ->where('parent_id', $parentId)
                ->latest()->get()
                ->map(function ($folder) use (&$build) {
                    $arr = (new FolderResource($folder->load('creator')))->toArray(request());
                    $arr['children'] = $build($folder->id);

                    return $arr;
                });
        };

        return $this->success($build(null), 'Folder tree.');
    }

    public function store(StoreFolderRequest $request)
    {
        Gate::authorize('create', Folder::class);
        $folder = $this->service->create($request->validated(), $request->user());

        return $this->success(new FolderResource($folder), 'Folder created.', 201);
    }

    public function show(Folder $folder)
    {
        Gate::authorize('view', $folder);
        $folder->load(['parent', 'children', 'creator', 'files'])->loadCount(['children', 'files']);

        return $this->success(new FolderResource($folder), 'Folder detail.');
    }

    public function breadcrumbs(Folder $folder)
    {
        Gate::authorize('view', $folder);

        return $this->success($folder->breadcrumbs(), 'Breadcrumbs.');
    }

    public function update(UpdateFolderRequest $request, Folder $folder)
    {
        Gate::authorize('update', $folder);
        $folder = $this->service->rename($folder, $request->validated(), $request->user());

        return $this->success(new FolderResource($folder), 'Folder updated.');
    }

    public function destroy(Request $request, Folder $folder)
    {
        Gate::authorize('delete', $folder);
        $this->service->delete($folder, $request->user());

        return $this->success(null, 'Folder deleted.');
    }
}
