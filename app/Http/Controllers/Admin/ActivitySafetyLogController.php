<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivitySafetyLog;
use App\Models\ActivityBooking;
use App\Helpers\AuditHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivitySafetyLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivitySafetyLog::with(['user', 'activity', 'activityBooking', 'loggedBy'])
            ->orderBy('activity_date', 'desc');

        // Filter by activity type
        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by incident
        if ($request->filled('incident')) {
            $query->where('incident_occurred', $request->incident === 'yes');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('activity_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('activity_date', '<=', $request->date_to);
        }

        $safetyLogs = $query->paginate(20);

        $activityTypes = ActivitySafetyLog::distinct()->pluck('activity_type');
        
        return view('admin.activity-safety.index', compact('safetyLogs', 'activityTypes'));
    }

    public function create(ActivityBooking $activityBooking)
    {
        if ($activityBooking->status !== 'completed') {
            return back()->with('error', 'Can only create safety log for completed activities.');
        }

        return view('admin.activity-safety.create', compact('activityBooking'));
    }

    public function store(Request $request, ActivityBooking $activityBooking)
    {
        $validated = $request->validate([
            'safety_checks' => 'nullable|array',
            'safety_notes' => 'nullable|string',
            'supervisor_name' => 'nullable|string|max:255',
            'weather_conditions' => 'nullable|string|max:255',
            'equipment_status' => 'required|in:good,needs_repair,replaced',
            'incident_occurred' => 'boolean',
            'incident_report' => 'nullable|string|required_if:incident_occurred,1',
            'status' => 'required|in:completed,cancelled,incident',
        ]);

        $safetyLog = ActivitySafetyLog::create([
            'activity_booking_id' => $activityBooking->id,
            'user_id' => $activityBooking->user_id,
            'activity_id' => $activityBooking->activity_id,
            'activity_type' => $activityBooking->activity->type ?? 'unknown',
            'activity_date' => $activityBooking->scheduled_time,
            'participants' => $activityBooking->participants,
            'safety_checks' => $validated['safety_checks'] ?? [],
            'safety_notes' => $validated['safety_notes'] ?? null,
            'supervisor_name' => $validated['supervisor_name'] ?? null,
            'weather_conditions' => $validated['weather_conditions'] ?? null,
            'equipment_status' => $validated['equipment_status'],
            'incident_occurred' => $validated['incident_occurred'] ?? false,
            'incident_report' => $validated['incident_report'] ?? null,
            'status' => $validated['status'],
            'logged_by' => Auth::id(),
        ]);

        AuditHelper::log('create', "Activity safety log created for booking #{$activityBooking->id}", $safetyLog);

        return redirect()->route('admin.activity-safety.show', $safetyLog)
            ->with('success', 'Safety log created successfully.');
    }

    public function show(ActivitySafetyLog $activitySafety)
    {
        $activitySafety->load(['user', 'activity', 'activityBooking', 'loggedBy']);
        
        return view('admin.activity-safety.show', compact('activitySafety'));
    }

    public function edit(ActivitySafetyLog $activitySafety)
    {
        return view('admin.activity-safety.edit', compact('activitySafety'));
    }

    public function update(Request $request, ActivitySafetyLog $activitySafety)
    {
        $validated = $request->validate([
            'safety_checks' => 'nullable|array',
            'safety_notes' => 'nullable|string',
            'supervisor_name' => 'nullable|string|max:255',
            'weather_conditions' => 'nullable|string|max:255',
            'equipment_status' => 'required|in:good,needs_repair,replaced',
            'incident_occurred' => 'boolean',
            'incident_report' => 'nullable|string|required_if:incident_occurred,1',
            'status' => 'required|in:completed,cancelled,incident',
        ]);

        $oldValues = $activitySafety->toArray();
        
        $activitySafety->update($validated);

        AuditHelper::log('update', "Activity safety log updated for booking #{$activitySafety->activity_booking_id}", 
            $activitySafety, $oldValues, $activitySafety->toArray());

        return redirect()->route('admin.activity-safety.show', $activitySafety)
            ->with('success', 'Safety log updated successfully.');
    }
}
