<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CreditHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreditController extends Controller
{
    public function manageIndex()
    {
        $user = auth()->user();

        if ($user->isClient()) {
            abort(403, 'Unauthorized access.');
        }

        if ($user->isSuperAdmin()) {
            $users = User::where('id', '!=', $user->id)->get();
            $totalDailyLimits = User::sum('daily_limit');
            $totalCreditsAssigned = CreditHistory::where('action', 'add')->sum('amount');
            $remainingLimit = null;
        } else {
            $users = User::where('created_by', $user->id)->get();
            $usedLimit = $users->sum('daily_limit');
            $totalDailyLimits = $usedLimit;
            $totalCreditsAssigned = CreditHistory::where('assigned_by', $user->name)->where('action', 'add')->sum('amount');
            $remainingLimit = max(0, $user->daily_limit - $usedLimit);
        }

        return view('credit-manage', compact('users', 'totalDailyLimits', 'totalCreditsAssigned', 'remainingLimit'));
    }

    public function storeCredit(Request $request)
    {
        if (auth()->user()->isClient()) {
            abort(403, 'Unauthorized action.');
        }

        $maxLimitRule = '';
        if (auth()->user()->isAdmin()) {
            $usedLimit = User::where('created_by', auth()->id())->sum('daily_limit');
            $remainingLimit = max(0, auth()->user()->daily_limit - $usedLimit);
            $maxLimitRule = '|max:' . $remainingLimit;
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|integer|min:1' . $maxLimitRule,
        ], [
            'amount.max' => 'You only have ' . ($remainingLimit ?? 'unlimited') . ' limits remaining to assign.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::findOrFail($request->user_id);
        
        if (auth()->user()->isAdmin() && $user->created_by !== auth()->id()) {
            return redirect()->back()->withErrors(['user_id' => 'You do not have permission to assign credits to this user.'])->withInput();
        }

        // Add to their daily limit
        $user->daily_limit += $request->amount;
        $user->save();

        // Log the history
        CreditHistory::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'action' => 'add',
            'assigned_by' => auth()->user()->name,
        ]);

        return redirect()->route('credit.manage')->with('success', 'Credits assigned successfully.');
    }

    public function historyIndex()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $histories = CreditHistory::with('user')->orderBy('created_at', 'desc')->get();
        } elseif ($user->isAdmin()) {
            $clientIds = User::where('created_by', $user->id)->pluck('id')->toArray();
            $userIds = array_merge([$user->id], $clientIds);
            $histories = CreditHistory::with('user')->whereIn('user_id', $userIds)->orderBy('created_at', 'desc')->get();
        } else {
            $histories = CreditHistory::with('user')->where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        }

        return view('credit-history', compact('histories'));
    }
}
