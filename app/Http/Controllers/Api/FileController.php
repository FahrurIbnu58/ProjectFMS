<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\UpdateFileRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Services\FileService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    use ApiResponse;

    public function __construct(protected FileService $service)
    {
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', File::class);
        $perPage = max(1, min((int) $request->integer('per_page', 15), 100));
        $query = File::with(['folder', 'department', 'uploader'])->latest();

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")->orWhere('file_name', 'like', "%{$q}%");
            });
        }
        if ($request->filled('folder_id')) {
            $query->where('folder_id', $request->input('folder_id'));
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        $paginator = $query->paginate($perPage);

        return response()->json([
            'message' => 'Files retrieved.',
            'data' => FileResource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(StoreFileRequest $request)
    {
        Gate::authorize('create', File::class);
        $file = $this->service->store($request->validated(), $request->file('file'), $request->user());

        return $this->success(new FileResource($file), 'File uploaded.', 201);
    }

    public function show(File $file)
    {
        Gate::authorize('view', $file);
        $file->load(['folder', 'department', 'uploader']);

        return $this->success(new FileResource($file), 'File detail.');
    }

    public function update(UpdateFileRequest $request, File $file)
    {
        Gate::authorize('update', $file);
        $file = $this->service->updateMeta($file, $request->validated(), $request->file('file'), $request->user());

        return $this->success(new FileResource($file), 'File updated.');
    }

    public function destroy(Request $request, File $file)
    {
        Gate::authorize('delete', $file);
        $this->service->delete($file, $request->user());

        return $this->success(null, 'File deleted.');
    }

    public function download(File $file)
    {
        Gate::authorize('view', $file);
        if (! $file->file_path || ! Storage::disk('public')->exists($file->file_path)) {
            return response()->json(['message' => 'File not found on disk.'], 404);
        }

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    public function preview(File $file)
    {
        Gate::authorize('view', $file);
        if (! $file->file_path || ! Storage::disk('public')->exists($file->file_path)) {
            return response()->json(['message' => 'File not found on disk.'], 404);
        }
        $path = Storage::disk('public')->path($file->file_path);

        return response()->file($path, ['Content-Type' => $file->mime_type ?? mime_content_type($path)]);
    }
}
