<?php

namespace App\Helpers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditHelper
{
    /**
     * Log an action to the audit log
     */
    public static function log(
        string $action,
        string $description,
        $model = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): AuditLog {
        $modelType = null;
        $modelId = null;

        if ($model) {
            $modelType = get_class($model);
            $modelId = $model->id ?? null;
        }

        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $modelType,
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
     * Log login attempt
     */
    public static function logLogin($user, bool $success = true): void
    {
        AuditLog::create([
            'user_id' => $success ? $user->id : null,
            'action' => $success ? 'login' : 'login_failed',
            'description' => $success 
                ? "User {$user->name} ({$user->email}) logged in successfully"
                : "Failed login attempt for email: " . request('email'),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'route' => Request::path(),
            'method' => Request::method(),
        ]);
    }

    /**
     * Log logout
     */
    public static function logLogout($user): void
    {
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'logout',
            'description' => "User {$user->name} ({$user->email}) logged out",
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'route' => Request::path(),
            'method' => Request::method(),
        ]);
    }
}
