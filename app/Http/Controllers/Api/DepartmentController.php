<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Services\DepartmentService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DepartmentController extends Controller
{
    use ApiResponse;

    public function __construct(protected DepartmentService $service)
    {
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Department::class);
        $perPage = max(1, min((int) $request->integer('per_page', 15), 100));
        $query = Department::latest();
        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->input('q').'%');
        }
        $paginator = $query->paginate($perPage);

        return response()->json([
            'message' => 'Departments retrieved.',
            'data' => DepartmentResource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(StoreDepartmentRequest $request)
    {
        Gate::authorize('create', Department::class);
        $dept = $this->service->create($request->validated(), $request->user());

        return $this->success(new DepartmentResource($dept), 'Department created.', 201);
    }

    public function show(Department $department)
    {
        Gate::authorize('view', $department);

        return $this->success(new DepartmentResource($department), 'Department detail.');
    }

    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        Gate::authorize('update', $department);
        $department = $this->service->update($department, $request->validated(), $request->user());

        return $this->success(new DepartmentResource($department), 'Department updated.');
    }

    public function destroy(Request $request, Department $department)
    {
        Gate::authorize('delete', $department);
        $this->service->delete($department, $request->user());

        return $this->success(null, 'Department deleted.');
    }
}
