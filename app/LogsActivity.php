<?php

namespace App;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    /**
     * Log an action to the audit log
     */
    public static function logActivity(
        string $action,
        string $description,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $modelType ?? static::class,
            'model_id' => $modelId,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'route' => Request::path(),
            'method' => Request::method(),
            'request_data' => Request::except(['password', '_token', 'password_confirmation']),
        ]);
    }

    /**
     * Helper function to log activity from anywhere
     */
    public static function auditLog(
        string $action,
        string $description,
        $model = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        $modelType = null;
        $modelId = null;

        if ($model) {
            $modelType = get_class($model);
            $modelId = $model->id ?? null;
        }

        static::logActivity($action, $description, $modelType, $modelId, $oldValues, $newValues);
    }
}
