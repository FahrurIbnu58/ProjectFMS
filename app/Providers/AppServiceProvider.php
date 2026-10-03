<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\File;
use App\Models\Folder;
use App\Policies\ActivityLogPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\FilePolicy;
use App\Policies\FolderPolicy;
use App\Repositories\ActivityLogRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\FileRepository;
use App\Repositories\FolderRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FolderRepository::class, fn ($app) => new FolderRepository(new Folder));
        $this->app->singleton(DepartmentRepository::class, fn ($app) => new DepartmentRepository(new Department));
        $this->app->singleton(FileRepository::class, fn ($app) => new FileRepository(new File));
        $this->app->singleton(ActivityLogRepository::class, fn ($app) => new ActivityLogRepository(new ActivityLog));
    }

    public function boot(): void
    {
        Gate::policy(Folder::class, FolderPolicy::class);
        Gate::policy(File::class, FilePolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(ActivityLog::class, ActivityLogPolicy::class);
    }
}
