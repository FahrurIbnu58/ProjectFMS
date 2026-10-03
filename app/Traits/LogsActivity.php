<?php

namespace App\Traits;

use App\Models\ActivityLog;
use App\Models\User;

trait LogsActivity
{
    public static function log(User $user, string $action, mixed $model = null, ?string $description = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'model_type' => is_object($model) ? get_class($model) : null,
            'model_id' => is_object($model) && isset($model->id) ? $model->id : null,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }
}
