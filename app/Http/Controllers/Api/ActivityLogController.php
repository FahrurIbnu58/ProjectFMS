<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityLogResource;
use App\Models\ActivityLog;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ActivityLogController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        Gate::authorize('viewAny', ActivityLog::class);
        $perPage = max(1, min((int) $request->integer('per_page', 15), 100));
        $paginator = ActivityLog::with('user')->latest()->paginate($perPage);

        return response()->json([
            'message' => 'Activity logs retrieved.',
            'data' => ActivityLogResource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }
}
