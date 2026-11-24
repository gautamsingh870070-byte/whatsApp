<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    // ══════════════════════════════════════════════════════════════════════
    // DIRECTION 1:  Business → Customer  (handled in WhatsAppController)
    //   - User fills form → sendBulk() → sendTicketTemplate() per number
    //   - Sends "stm_developments" template to ANY number dynamically
    //
    // DIRECTION 2:  Customer → Business  (handled HERE via Webhook)
    //   - Customer replies to our WhatsApp number
    //   - Meta POSTs the message to /api/whatsapp/webhook
    //   - We receive it, log it, and auto-reply with a text message
    // ══════════════════════════════════════════════════════════════════════

    /**
     * STEP 1: Meta Webhook Verification (GET)
     *
     * Meta calls this once when you save the webhook URL in the dashboard.
     * We must echo back hub_challenge to confirm ownership.
     */
    public function verify(Request $request)
    {
        $mode      = $request->query('hub_mode');
        $token     = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $verifyToken = env('WHATSAPP_VERIFY_TOKEN', env('WHATSAPP_WEBHOOK_VERIFY_TOKEN', 'stm_digital_webhook_2026'));

        // 1. Meta Webhook Verification Request
        if ($mode === 'subscribe' && $token === $verifyToken) {
            Log::info('✅ WhatsApp Webhook Verified successfully.');
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        // 2. Direct Browser Visit (Convenience status response for developer)
        if (empty($mode) && empty($token)) {
            return response()->json([
                'status'       => 'active',
                'service'      => 'WhatsApp Webhook Listener',
                'verify_token' => $verifyToken,
                'message'      => 'Webhook endpoint is active & ready to receive Meta Webhook requests.',
                'test_url'     => url('/api/whatsapp/webhook?hub_mode=subscribe&hub_verify_token=' . $verifyToken . '&hub_challenge=123456')
            ], 200, [], JSON_PRETTY_PRINT);
        }

        Log::warning('❌ WhatsApp Webhook Verification Failed.', [
            'mode'  => $mode,
            'token' => $token,
        ]);

        return response('Forbidden - Invalid Verify Token or Mode', 403);
    }

    /**
     * STEP 2: Receive Incoming Events (POST)
     *
     * Meta POSTs here for:
     *   - Incoming messages from customers  (Customer → Business)
     *   - Message status updates (sent, delivered, read, failed)
     */
    public function receive(Request $request)
    {
        $payload = $request->all();
        Log::info('📥 WhatsApp Webhook Event Received', $payload);

        $entries = $payload['entry'] ?? [];

        foreach ($entries as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $value = $change['value'] ?? [];

                // ── Incoming Messages (Customer → Business) ────────────────
                foreach ($value['messages'] ?? [] as $message) {
                    $this->handleIncomingMessage($message, $value);
                }

                // ── Delivery / Read Status Updates ─────────────────────────
                foreach ($value['statuses'] ?? [] as $status) {
                    $statusId = $status['id'] ?? null;
                    $statusName = $status['status'] ?? null;
                    
                    if ($statusId && $statusName) {
                        \App\Models\WhatsAppLog::where('api_response', 'LIKE', '%' . $statusId . '%')
                            ->update(['status' => $statusName]);
                    }

                    Log::info('📬 WhatsApp Status Update', [
                        'id'        => $statusId,
                        'status'    => $statusName,
                        'to'        => $status['recipient_id'] ?? null,
                        'timestamp' => $status['timestamp']   ?? null,
                    ]);
                }
            }
        }

        // Meta REQUIRES a quick 200 OK — do NOT delay this response
        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * Handle a single incoming message from a customer.
     *
     * Customer → Business flow:
     *   1. Extract sender's phone number & their message text
     *   2. Log it for records
     *   3. Auto-reply with a helpful text message
     */
    /**
     * Handle a single incoming message from a customer.
     *
     * Customer → Business flow:
     *   1. Extract Customer WhatsApp number, Customer name, Message text, Message type, Message ID, Timestamp, & Media info
     *   2. Log & Save to Database (whatsapp_logs & contacts)
     *   3. Send Auto-Reply (Business → Customer)
     */
    protected function handleIncomingMessage(array $message, array $value): void
    {
        // 1. Extract Core Metadata
        $from        = $message['from']        ?? null;  // Customer WhatsApp number
        $type        = $message['type']        ?? 'text'; // Message type: text, image, video, document, etc.
        $messageId   = $message['id']          ?? null;  // WhatsApp message ID
        $timestamp   = $message['timestamp']   ?? null;  // Meta Timestamp

        // Get sender's profile name from contacts array
        $contacts    = $value['contacts'] ?? [];
        $senderName  = $contacts[0]['profile']['name'] ?? 'Customer';

        if (!$from) {
            Log::warning('⚠️ Incoming WhatsApp message has no sender phone number — skipping.');
            return;
        }

        // Check if contact is blocked
        try {
            $contact = \App\Models\Contact::where('phone', $from)->first();
            if ($contact && $contact->is_blocked) {
                Log::info("🚫 Ignoring webhook from blocked contact: {$from}");
                return;
            }
        } catch (\Exception $e) {}

        // 2. Extract Message Content & Media Information
        $textBody   = null;
        $mediaInfo  = null;

        switch ($type) {
            case 'text':
                $textBody = $message['text']['body'] ?? '';
                break;

            case 'image':
                $imageData = $message['image'] ?? [];
                $mediaInfo = [
                    'media_id'  => $imageData['id'] ?? null,
                    'mime_type' => $imageData['mime_type'] ?? null,
                    'caption'   => $imageData['caption'] ?? '',
                    'sha256'    => $imageData['sha256'] ?? null,
                ];
                $textBody = !empty($imageData['caption']) 
                    ? "📷 Image: {$imageData['caption']}" 
                    : '📷 Image message';
                break;

            case 'video':
                $videoData = $message['video'] ?? [];
                $mediaInfo = [
                    'media_id'  => $videoData['id'] ?? null,
                    'mime_type' => $videoData['mime_type'] ?? null,
                    'caption'   => $videoData['caption'] ?? '',
                    'sha256'    => $videoData['sha256'] ?? null,
                ];
                $textBody = !empty($videoData['caption']) 
                    ? "🎥 Video: {$videoData['caption']}" 
                    : '🎥 Video message';
                break;

            case 'document':
                $docData = $message['document'] ?? [];
                $mediaInfo = [
                    'media_id'  => $docData['id'] ?? null,
                    'mime_type' => $docData['mime_type'] ?? null,
                    'filename'  => $docData['filename'] ?? null,
                    'caption'   => $docData['caption'] ?? '',
                    'sha256'    => $docData['sha256'] ?? null,
                ];
                $docName = $docData['filename'] ?? 'document';
                $textBody = !empty($docData['caption'])
                    ? "📄 Document ({$docName}): {$docData['caption']}"
                    : "📄 Document: {$docName}";
                break;

            case 'audio':
                $audioData = $message['audio'] ?? [];
                $mediaInfo = [
                    'media_id'  => $audioData['id'] ?? null,
                    'mime_type' => $audioData['mime_type'] ?? null,
                    'voice'     => $audioData['voice'] ?? false,
                ];
                $textBody = ($audioData['voice'] ?? false) ? '🎙️ Voice Note' : '🎵 Audio file';
                break;

            case 'sticker':
                $stickerData = $message['sticker'] ?? [];
                $mediaInfo = [
                    'media_id'  => $stickerData['id'] ?? null,
                    'mime_type' => $stickerData['mime_type'] ?? null,
                ];
                $textBody = '🎨 Sticker';
                break;

            case 'location':
                $locData = $message['location'] ?? [];
                $mediaInfo = [
                    'latitude'  => $locData['latitude'] ?? null,
                    'longitude' => $locData['longitude'] ?? null,
                    'name'      => $locData['name'] ?? null,
                    'address'   => $locData['address'] ?? null,
                ];
                $locTitle = $locData['name'] ?? ($locData['latitude'] . ', ' . $locData['longitude']);
                $textBody = "📍 Location: {$locTitle}";
                break;

            case 'interactive':
                $interactive = $message['interactive'] ?? [];
                $btnTitle = $interactive['button_reply']['title'] ?? $interactive['list_reply']['title'] ?? 'Option selected';
                $textBody = "🔘 Button Click: {$btnTitle}";
                break;

            case 'button':
                $textBody = '🔘 ' . ($message['button']['text'] ?? 'Quick Reply');
                break;

            default:
                $textBody = ucfirst($type) . ' message received';
                break;
        }

        Log::info('💬 Incoming Customer Message Processed', [
            'from'        => $from,
            'name'        => $senderName,
            'type'        => $type,
            'text'        => $textBody,
            'message_id'  => $messageId,
            'media_info'  => $mediaInfo
        ]);

        // If it's a media message, attempt to download it
        $localMediaUrl = null;
        if (isset($mediaInfo['media_id'])) {
            $filename = $mediaInfo['filename'] ?? null;
            $localMediaUrl = $this->downloadMedia($mediaInfo['media_id'], $mediaInfo['mime_type'] ?? null, $filename);
            if ($localMediaUrl) {
                $mediaInfo['local_url'] = $localMediaUrl;
                // Append link to text body for easy reading
                $textBody .= "\n[Media Attachment: " . asset($localMediaUrl) . "]";
            }
        }

        // 3. Save incoming message to database
        $log = \App\Models\WhatsAppLog::create([
            'user_id'       => null,
            'phone_number'  => $from,
            'template_name' => 'customer_incoming',
            'message_type'  => $type,
            'status'        => 'received',
            'api_response'  => [
                'text'                => $textBody,
                'sender'              => 'incoming',
                'sender_name'         => $senderName,
                'customer_phone'      => $from,
                'message_id'          => $messageId,
                'timestamp'           => $timestamp,
                'timestamp_formatted' => $timestamp ? date('Y-m-d H:i:s', (int)$timestamp) : now()->toDateTimeString(),
                'message_type'        => $type,
                'media'               => $mediaInfo,
            ],
        ]);

        // 4. Auto-Register / Update Contact in Database
        try {
            $contact = \App\Models\Contact::firstOrNew(['phone' => $from]);
            if (!$contact->exists || !$contact->is_manually_saved) {
                $contact->name = $senderName;
                $contact->status = 'active';
                $contact->save();
            }
        } catch (\Exception $e) {
            Log::warning('Contact update exception: ' . $e->getMessage());
        }

        // 5. Send Auto-Reply (Throttled to once per 24 hours)
        $lastAutoReply = \App\Models\WhatsAppLog::where('phone_number', $from)
            ->where('template_name', 'auto_reply')
            ->where('created_at', '>=', now()->subHours(24))
            ->first();

        if (!$lastAutoReply) {
            $autoReply = env(
                'WHATSAPP_AUTO_REPLY_TEXT',
                "Hello {name}! Thank you for contacting STM Developments. Our team will get back to you shortly."
            );

            $autoReply = str_replace('{name}', $senderName, $autoReply);

            $result = $this->whatsAppService->sendTextReply($from, $autoReply);

            if ($result['success']) {
                $metaResponse = $result['response'] ?? [];
                $metaResponse['text'] = $autoReply;
                $metaResponse['sender'] = 'outgoing';
                $metaResponse['timestamp'] = time();

                \App\Models\WhatsAppLog::create([
                    'user_id'       => null,
                    'phone_number'  => $from,
                    'template_name' => 'auto_reply',
                    'message_type'  => 'text',
                    'status'        => 'sent', // Initially sent
                    'api_response'  => $metaResponse,
                ]);
                Log::info("✅ Auto-reply sent to {$from} ({$senderName})");
            } else {
                Log::error("❌ Auto-reply FAILED to {$from}", $result['response']);
            }
        } else {
            Log::info("⏭️ Auto-reply skipped for {$from} (already sent within 24 hours).");
        }
    }

    /**
     * Download media from WhatsApp Meta API.
     */
    private function downloadMedia($mediaId, $mimeType, $filename = null)
    {
        if (!$mediaId) return null;

        try {
            $token = env('WHATSAPP_TOKEN');
            
            // 1. Get media URL from Meta
            $response = \Illuminate\Support\Facades\Http::withToken($token)
                ->get("https://graph.facebook.com/v20.0/{$mediaId}");
            
            if (!$response->successful()) {
                Log::error("Failed to get media URL for {$mediaId}: " . $response->body());
                return null;
            }
            
            $mediaUrl = $response->json()['url'] ?? null;
            if (!$mediaUrl) return null;

            // 2. Download media bytes
            $mediaResponse = \Illuminate\Support\Facades\Http::withToken($token)
                ->get($mediaUrl);
                
            if (!$mediaResponse->successful()) {
                Log::error("Failed to download media bytes for {$mediaUrl}");
                return null;
            }

            // 3. Determine extension
            $ext = 'bin';
            if ($mimeType) {
                $ext = explode('/', $mimeType)[1] ?? 'bin';
                $ext = explode(';', $ext)[0]; // strip codecs if present
            }
            
            $safeName = $filename ? preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filename) : uniqid();
            $finalName = time() . '_' . $safeName . ($filename ? '' : '.' . $ext);
            $path = 'whatsapp_media/' . $finalName;

            if (!file_exists(public_path('whatsapp_media'))) {
                mkdir(public_path('whatsapp_media'), 0755, true);
            }
            
            file_put_contents(public_path('whatsapp_media/' . $finalName), $mediaResponse->body());

            return '/public/whatsapp_media/' . $finalName;
        } catch (\Exception $e) {
            Log::error('Exception downloading media: ' . $e->getMessage());
            return null;
        }
    }
}
