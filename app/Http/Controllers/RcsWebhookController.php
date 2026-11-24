<?php

namespace App\Http\Controllers;

use App\Models\RcsMessage;
use App\Models\RcsAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RcsWebhookController extends Controller
{
    public function handle(Request $request, $agent_id)
    {
        // Google sends the event details in the body.
        Log::info('RCS Webhook Received for Agent ' . $agent_id . ': ' . json_encode($request->all()));

        $agent = RcsAgent::where('agent_id', $agent_id)->first();
        if (!$agent) {
            return response()->json(['error' => 'Agent not found'], 404);
        }

        // Parse event (Simplified based on Google RBM documentation)
        $messageEvents = $request->input('messageEvents', []);
        
        foreach ($messageEvents as $event) {
            if (isset($event['messageId'])) {
                $message = RcsMessage::where('google_message_id', $event['messageId'])->first();
                if ($message) {
                    if (isset($event['eventType'])) {
                        switch ($event['eventType']) {
                            case 'DELIVERED':
                                $message->status = 'delivered';
                                $message->delivered_at = now();
                                break;
                            case 'READ':
                                $message->status = 'read';
                                $message->read_at = now();
                                break;
                            case 'FAILED':
                                $message->status = 'failed';
                                // failure details if any
                                break;
                        }
                        $message->save();
                    }
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
