<?php

namespace App\Http\Controllers;

use App\Models\RcsTemplate;
use App\Models\RcsAgent;
use Illuminate\Http\Request;

class RcsTemplateController extends Controller
{
    public function index()
    {
        $templates = RcsTemplate::with('agent')->latest()->paginate(20);
        $agents = RcsAgent::where('is_active', true)->get();
        return view('rcs.templates', compact('templates', 'agents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'rcs_agent_id' => 'required|exists:rcs_agents,id',
            'type' => 'required|string',
            'content' => 'required|string',
        ]);

        RcsTemplate::create([
            'user_id' => auth()->id(),
            'rcs_agent_id' => $request->rcs_agent_id,
            'name' => $request->name,
            'type' => $request->type,
            'content' => $request->content,
            'media_url' => $request->media_url,
            'suggestions' => $request->suggestions ? json_decode($request->suggestions, true) : null,
        ]);

        return back()->with('success', 'Template created successfully.');
    }

    public function destroy($id)
    {
        $template = RcsTemplate::findOrFail($id);
        $template->delete();
        return back()->with('success', 'Template deleted successfully.');
    }
}
