<?php

namespace App\Services;

use App\Models\Department;
use App\Models\User;
use App\Repositories\DepartmentRepository;
use App\Traits\LogsActivity;

class DepartmentService
{
    use LogsActivity;

    public function __construct(protected DepartmentRepository $departments)
    {
    }

    public function create(array $data, User $user): Department
    {
        $dept = $this->departments->create($data);
        static::log($user, 'department.created', $dept, "Created department {$dept->name}");

        return $dept;
    }

    public function update(Department $department, array $data, User $user): Department
    {
        $department->update($data);
        static::log($user, 'department.updated', $department, "Updated department {$department->name}");

        return $department->fresh();
    }

    public function delete(Department $department, User $user): void
    {
        static::log($user, 'department.deleted', $department, "Deleted department {$department->name}");
        $department->delete();
    }
}
