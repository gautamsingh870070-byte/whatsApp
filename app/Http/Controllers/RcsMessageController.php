<?php

namespace App\Http\Controllers;

use App\Models\RcsMessage;
use App\Models\RcsAgent;
use App\Models\RcsTemplate;
use App\Models\RcsContact;
use App\Services\GoogleRcsService;
use Illuminate\Http\Request;

class RcsMessageController extends Controller
{
    public function history()
    {
        $messages = RcsMessage::with('agent')->latest()->paginate(20);
        return view('rcs.history', compact('messages'));
    }

    public function create()
    {
        $agents = RcsAgent::where('is_active', true)->get();
        $templates = RcsTemplate::where('is_approved', true)->get();
        return view('rcs.send', compact('agents', 'templates'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'country_code' => 'required|string',
            'agent_id' => 'required|exists:rcs_agents,id',
            'message_type' => 'required|in:text,template,rich_card',
            'text_message' => 'required_if:message_type,text',
        ]);

        $agent = RcsAgent::findOrFail($request->agent_id);
        
        $fullNumber = $request->country_code . ltrim($request->phone_number, '0');
        
        $content = [];
        if ($request->message_type === 'text') {
            $content = $request->text_message;
        } elseif ($request->message_type === 'template') {
            $template = RcsTemplate::find($request->template_id);
            if (!$template) {
                return back()->with('error', 'Template not found.');
            }
            $content = json_decode($template->content, true) ?? $template->content;
        }

        try {
            $service = new GoogleRcsService($agent);
            $messageId = uniqid('rcs_'.time().'_');
            
            $response = $service->sendMessage($fullNumber, $content, $messageId);
            
            $status = $response['success'] ? 'sent' : 'failed';
            
            RcsMessage::create([
                'user_id' => auth()->id(),
                'rcs_agent_id' => $agent->id,
                'google_message_id' => $messageId,
                'customer_number' => $fullNumber,
                'content' => is_array($content) ? json_encode($content) : $content,
                'status' => $status,
                'failure_reason' => $response['success'] ? null : ($response['error'] ?? 'Unknown Error'),
                'sent_at' => $response['success'] ? now() : null,
            ]);

            if ($response['success']) {
                return back()->with('success', 'RCS Message sent successfully!');
            } else {
                return back()->with('error', 'Failed to send: ' . ($response['error'] ?? 'Unknown Error'));
            }

        } catch (\Exception $e) {
            \Log::error("RCS Send Exception: " . $e->getMessage());
            return back()->with('error', 'Exception: ' . $e->getMessage());
        }
    }
}
