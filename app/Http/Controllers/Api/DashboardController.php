<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FileResource;
use App\Services\DashboardService;
use App\Traits\ApiResponse;

class DashboardController extends Controller
{
    use ApiResponse;

    public function __construct(protected DashboardService $service)
    {
    }

    public function index()
    {
        $stats = $this->service->stats();

        return $this->success([
            'total_folders' => $stats['total_folders'],
            'total_files' => $stats['total_files'],
            'total_departments' => $stats['total_departments'],
            'latest_files' => FileResource::collection($stats['latest_files']),
        ], 'Dashboard stats.');
    }
}
