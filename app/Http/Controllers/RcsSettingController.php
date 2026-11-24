<?php

namespace App\Http\Controllers;

use App\Models\RcsAgent;
use Illuminate\Http\Request;

class RcsSettingController extends Controller
{
    public function index()
    {
        $agents = RcsAgent::latest()->get();
        return view('rcs.settings', compact('agents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'agent_id' => 'required|string|unique:rcs_agents',
            'brand_id' => 'required|string',
            'service_account_json' => 'required|json',
            'environment' => 'required|in:sandbox,production',
        ]);

        RcsAgent::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'agent_id' => $request->agent_id,
            'brand_id' => $request->brand_id,
            'service_account_json' => $request->service_account_json,
            'environment' => $request->environment,
            'webhook_token' => \Illuminate\Support\Str::random(40),
            'is_active' => true,
        ]);

        return back()->with('success', 'RCS Agent configured successfully.');
    }

    public function destroy($id)
    {
        $agent = RcsAgent::findOrFail($id);
        $agent->delete();
        return back()->with('success', 'Agent deleted successfully.');
    }
}
