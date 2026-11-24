<?php

namespace App\Http\Controllers;

use App\Models\RcsMessage;
use App\Models\RcsAgent;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RcsDashboardController extends Controller
{
    public function index()
    {
        $user_id = auth()->id();
        
        // Basic Stats
        $totalMessages = RcsMessage::count();
        $sent = RcsMessage::where('status', 'sent')->count();
        $delivered = RcsMessage::where('status', 'delivered')->count();
        $read = RcsMessage::where('status', 'read')->count();
        $failed = RcsMessage::where('status', 'failed')->count();
        
        $todayMessages = RcsMessage::whereDate('created_at', Carbon::today())->count();
        $thisMonthMessages = RcsMessage::whereMonth('created_at', Carbon::now()->month)->count();

        // Check if agent configured
        $agentsConfigured = RcsAgent::count();

        return view('rcs.dashboard', compact(
            'totalMessages', 'sent', 'delivered', 'read', 'failed',
            'todayMessages', 'thisMonthMessages', 'agentsConfigured'
        ));
    }
}
