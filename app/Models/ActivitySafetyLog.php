<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivitySafetyLog extends Model
{
    protected $fillable = [
        'activity_booking_id',
        'user_id',
        'activity_id',
        'activity_type',
        'activity_date',
        'participants',
        'safety_checks',
        'safety_notes',
        'supervisor_name',
        'weather_conditions',
        'equipment_status',
        'incident_report',
        'incident_occurred',
        'status',
        'logged_by',
    ];

    protected $casts = [
        'activity_date' => 'datetime',
        'participants' => 'integer',
        'safety_checks' => 'array',
        'incident_occurred' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function activityBooking(): BelongsTo
    {
        return $this->belongsTo(ActivityBooking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function loggedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by');
    }
}
