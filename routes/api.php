<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;



Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Folders — read open to any authenticated user
    Route::get('/folders-tree', [FolderController::class, 'tree']);
    Route::get('/folders/{folder}/breadcrumbs', [FolderController::class, 'breadcrumbs']);
    Route::get('/folders', [FolderController::class, 'index']);
    Route::get('/folders/{folder}', [FolderController::class, 'show']);

    // Departments — read open
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::get('/departments/{department}', [DepartmentController::class, 'show']);

    // Files — read/download open
    Route::get('/files', [FileController::class, 'index']);
    Route::get('/files/{file}', [FileController::class, 'show']);
    Route::get('/files/{file}/download', [FileController::class, 'download']);
    Route::get('/files/{file}/preview', [FileController::class, 'preview']);

    // Write routes — administrator only
    Route::middleware('role:administrator')->group(function () {
        Route::post('/folders', [FolderController::class, 'store']);
        Route::match(['put', 'patch'], '/folders/{folder}', [FolderController::class, 'update']);
        Route::delete('/folders/{folder}', [FolderController::class, 'destroy']);

        Route::post('/departments', [DepartmentController::class, 'store']);
        Route::match(['put', 'patch'], '/departments/{department}', [DepartmentController::class, 'update']);
        Route::delete('/departments/{department}', [DepartmentController::class, 'destroy']);

        Route::post('/files', [FileController::class, 'store']);
        Route::match(['put', 'patch'], '/files/{file}', [FileController::class, 'update']);
        Route::delete('/files/{file}', [FileController::class, 'destroy']);

        Route::get('/activity-logs', [ActivityLogController::class, 'index']);

        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::match(['put', 'patch'], '/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });
});
