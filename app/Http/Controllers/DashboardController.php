<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $userIds = [];
        if ($user->isSuperAdmin()) {
            // Superadmin sees all (no user_id filter applied later)
        } elseif ($user->isAdmin()) {
            // Admin sees self + clients they created
            $clientIds = \App\Models\User::where('created_by', $user->id)->pluck('id')->toArray();
            $userIds = array_merge([$user->id], $clientIds);
        } else {
            // Client sees only themselves
            $userIds = [$user->id];
        }

        $cachePrefix = 'dashboard_' . $user->id . '_';
        
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        $todaysCampaign = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'todays_campaign', 60, function () use ($todayStart, $todayEnd, $userIds, $user) {
            $query = WhatsAppLog::whereBetween('created_at', [$todayStart, $todayEnd]);
            if (!$user->isSuperAdmin()) {
                $query->whereIn('user_id', $userIds);
            }
            return $query->count();
        });

        $totalWapp = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'total_wapp', 60, function () use ($userIds, $user) {
            $query = WhatsAppLog::query();
            if (!$user->isSuperAdmin()) {
                $query->whereIn('user_id', $userIds);
            }
            return $query->count();
        });
        
        $buttonWapp = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'unique_contacts', 60, function () use ($userIds, $user) {
            $query = WhatsAppLog::distinct('phone_number');
            if (!$user->isSuperAdmin()) {
                $query->whereIn('user_id', $userIds);
            }
            return $query->count('phone_number');
        });

        $stats = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'wa_stats', 60, function () use ($userIds, $user) {
            $query = WhatsAppLog::where('template_name', '!=', 'customer_incoming');
            if (!$user->isSuperAdmin()) {
                $query->whereIn('user_id', $userIds);
            }
            
            $aggregated = $query->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');
                
            return [
                'success' => ($aggregated['success'] ?? 0) + ($aggregated['delivered'] ?? 0) + ($aggregated['sent'] ?? 0),
                'pending' => ($aggregated['pending'] ?? 0) + ($aggregated['queued'] ?? 0),
                'failed' => $aggregated['failed'] ?? 0,
                'total' => $aggregated->sum()
            ];
        });

        $waSuccess = $stats['success'];
        $waPending = $stats['pending'];
        $waFailed  = $stats['failed'];
        $waTotal   = $stats['total'];

        $roleStats = [];
        if ($user->isSuperAdmin()) {
            $roleStats['total_users'] = \App\Models\User::count();
            $roleStats['total_admins'] = \App\Models\User::where('role', 'admin')->count();
            $roleStats['total_clients'] = \App\Models\User::where('role', 'client')->count();
        } elseif ($user->isAdmin()) {
            $roleStats['my_clients'] = \App\Models\User::where('created_by', $user->id)->count();
            $roleStats['total_credits'] = $user->daily_limit;
            $roleStats['credits_assigned'] = \App\Models\User::where('created_by', $user->id)->sum('daily_limit');
        } else {
            $roleStats['daily_limit'] = $user->daily_limit;
            $roleStats['credits_used_today'] = $todaysCampaign;
        }

        return view('dashboard', compact(
            'todaysCampaign', 'totalWapp', 'buttonWapp',
            'waTotal', 'waSuccess', 'waPending', 'waFailed',
            'roleStats'
        ));
    }
}
