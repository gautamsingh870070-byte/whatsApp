<?php

namespace App\Http\Controllers;

use App\Models\ScheduledMessage;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        // Auto-process any pending schedules that are due
        try {
            \Illuminate\Support\Facades\Artisan::call('app:process-schedules');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Auto process schedules failed: ' . $e->getMessage());
        }

        $user = auth()->user();
        $query = ScheduledMessage::with('user');
        
        if (!$user->isSuperAdmin()) {
            if ($user->isAdmin()) {
                $clientIds = \App\Models\User::where('created_by', $user->id)->pluck('id')->toArray();
                $userIds = array_merge([$user->id], $clientIds);
                $query->whereIn('user_id', $userIds);
            } else {
                $query->where('user_id', $user->id);
            }
        }
        
        $schedules = $query->orderBy('scheduled_at', 'desc')->get();
        return view('schedule-log', compact('schedules'));
    }
}
