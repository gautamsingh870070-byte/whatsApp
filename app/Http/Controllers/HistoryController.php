<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = WhatsAppLog::query();
        
        if (!$user->isSuperAdmin()) {
            if ($user->isAdmin()) {
                $clientIds = \App\Models\User::where('created_by', $user->id)->pluck('id')->toArray();
                $userIds = array_merge([$user->id], $clientIds);
                $query->whereIn('user_id', $userIds);
            } else {
                $query->where('user_id', $user->id);
            }
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00', 
                $request->end_date . ' 23:59:59'
            ]);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('history', compact('logs'));
    }
}
