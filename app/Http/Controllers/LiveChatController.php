<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppLog;
use App\Models\Contact;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LiveChatController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Build structured conversations from Database logs & contacts
     */
    private function buildConversationsData()
    {
        $user = Auth::user();
        $userIds = [];

        if (!$user->isSuperAdmin()) {
            if ($user->isAdmin()) {
                $clientIds = \App\Models\User::where('created_by', $user->id)->pluck('id')->toArray();
                $userIds = array_merge([$user->id], $clientIds);
            } else {
                $userIds = [$user->id];
            }
        }

        // Fetch Contacts safely
        $contacts = collect();
        $allowedPhones = [];
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('contacts')) {
                $contactsQuery = Contact::query();
                if (!$user->isSuperAdmin()) {
                    $contactsQuery->whereIn('user_id', $userIds);
                }
                $contactsList = $contactsQuery->get();
                $contacts = $contactsList->keyBy('phone');
                $allowedPhones = $contactsList->pluck('phone')->toArray();
            }
        } catch (\Exception $e) {
            $contacts = collect();
        }

        // Fetch all WhatsApp Logs
        $logsQuery = WhatsAppLog::latest();
        if (!$user->isSuperAdmin()) {
            $logsQuery->where(function($q) use ($userIds, $allowedPhones) {
                $q->whereIn('user_id', $userIds)
                  ->orWhere(function($subQ) use ($allowedPhones) {
                      $subQ->whereNull('user_id');
                      if (!empty($allowedPhones)) {
                          $subQ->whereIn('phone_number', $allowedPhones);
                      } else {
                          // If no contacts, don't show any incoming messages
                          $subQ->whereRaw('1 = 0');
                      }
                  });
            });
        }
        $logs = $logsQuery->get();

        $conversations = [];
        $avatarColors = ['#6366f1', '#06b6d4', '#ec4899', '#f59e0b', '#10b981', '#8b5cf6', '#3b82f6', '#ef4444', '#14b8a6'];
        $colorIndex = 0;

        $activeCount = 0;
        $requestingCount = 0;
        $intervenedCount = 0;

        foreach ($logs as $log) {
            $phone = $log->phone_number;
            
            if (!isset($conversations[$phone])) {
                $contactModel = $contacts->get($phone) ?? $contacts->get('+' . $phone);
                
                $displayName = '+' . $phone;
                $isSaved = false;
                
                if ($contactModel && $contactModel->name) {
                    if (!$contactModel->is_manually_saved && str_starts_with($contactModel->name, 'Contact ')) {
                        $displayName = '+' . $phone;
                        $isSaved = false;
                    } else {
                        $displayName = $contactModel->name;
                        $isSaved = $contactModel->is_manually_saved;
                    }
                } else {
                    // Display the full phone number with + sign
                    $displayName = '+' . $phone;
                }

                $initial = strtoupper(substr($displayName, 0, 1));
                if (!preg_match('/[A-Za-z0-9]/', $initial)) {
                    $initial = 'C';
                }

                $color = $avatarColors[$colorIndex % count($avatarColors)];
                $colorIndex++;

                // Determine Category: active, requesting, intervened
                $category = 'active';
                if ($log->template_name === 'live_chat_reply') {
                    $category = 'intervened';
                } elseif ($log->status === 'failed' || $log->status === 'pending') {
                    $category = 'requesting';
                }

                $conversations[$phone] = [
                    'phone' => $phone,
                    'display_name' => $displayName,
                    'is_saved' => $isSaved,
                    'contact_model' => $contactModel ? $contactModel->toArray() : null,
                    'is_blocked' => $contactModel ? (bool)$contactModel->is_blocked : false,
                    'initial' => $initial,
                    'color' => $color,
                    'category' => $category,
                    'last_time' => $log->created_at ? $log->created_at->format('h:i A') : 'Recently',
                    'last_message' => $this->cleanSnippetText($this->extractMessageContent($log)),
                    'status' => $log->status,
                    'messages' => [],
                    'unread_count' => 0,
                    'last_incoming_at' => null
                ];
            }

            $messageText = $this->extractMessageContent($log);
            $isOutgoing = ($log->template_name === 'live_chat_reply' || $log->status === 'success' || $log->status === 'failed' || str_starts_with($log->template_name ?? '', 'stm_') || str_starts_with($log->template_name ?? '', 'live_') || $log->template_name === '3p_direct_integration_test_template');

            if (!$isOutgoing && $log->status !== 'read') {
                $conversations[$phone]['unread_count']++;
            }

            if (!$isOutgoing && $conversations[$phone]['last_incoming_at'] === null) {
                $conversations[$phone]['last_incoming_at'] = $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : null;
            }

            $conversations[$phone]['messages'][] = [
                'id' => $log->id,
                'sender' => $isOutgoing ? 'outgoing' : 'incoming',
                'text' => $messageText,
                'status' => $log->status,
                'time' => $log->created_at ? $log->created_at->format('h:i A') : 'Just now',
                'created_at' => $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s')
            ];
        }

        // Add any remaining contacts that don't have WhatsApp logs yet
        foreach ($contacts as $phone => $contactModel) {
            $cleanPhone = ltrim($phone, '+');
            if (!isset($conversations[$cleanPhone])) {
                if ($contactModel->name && (!$contactModel->is_manually_saved && str_starts_with($contactModel->name, 'Contact '))) {
                    $displayName = '+' . $cleanPhone;
                    $isSaved = false;
                } else {
                    $displayName = $contactModel->name ?: '+' . $cleanPhone;
                    $isSaved = $contactModel->is_manually_saved;
                }
                $initial = strtoupper(substr($displayName, 0, 1));
                if (!preg_match('/[A-Za-z0-9]/', $initial)) {
                    $initial = 'C';
                }
                $color = $avatarColors[$colorIndex % count($avatarColors)];
                $colorIndex++;
                
                $conversations[$cleanPhone] = [
                    'phone' => $cleanPhone,
                    'display_name' => $displayName,
                    'is_saved' => $isSaved,
                    'contact_model' => $contactModel->toArray(),
                    'is_blocked' => (bool)$contactModel->is_blocked,
                    'initial' => $initial,
                    'color' => $color,
                    'category' => 'active', // default for UI
                    'last_time' => $contactModel->created_at ? $contactModel->created_at->format('h:i A') : '',
                    'last_message' => 'Click to start conversation',
                    'status' => '',
                    'messages' => [],
                    'unread_count' => 0,
                    'last_incoming_at' => null
                ];
            }
        }

        // Compute exact category counts
        foreach ($conversations as $phone => &$conv) {
            $conv['messages'] = array_reverse($conv['messages']);
            if ($conv['category'] === 'intervened') {
                $intervenedCount++;
            } elseif ($conv['category'] === 'requesting') {
                $requestingCount++;
            } else {
                $activeCount++;
            }
        }

        // Top horizontal avatars (latest 10 conversations)
        $topAvatars = array_slice($conversations, 0, 10, true);

        return [
            'conversations'   => $conversations,
            'topAvatars'      => $topAvatars,
            'orderedPhones'   => array_keys($conversations),
            'activeCount'     => $activeCount,
            'requestingCount' => $requestingCount,
            'intervenedCount' => $intervenedCount,
        ];
    }

    /**
     * Display the Live Chat interface
     */
    public function index()
    {
        $data = $this->buildConversationsData();
        
        $user = Auth::user();
        $contactsQuery = \App\Models\Contact::orderBy('name', 'asc');
        
        if (!$user->isSuperAdmin()) {
            if ($user->isAdmin()) {
                $clientIds = \App\Models\User::where('created_by', $user->id)->pluck('id')->toArray();
                $userIds = array_merge([$user->id], $clientIds);
                $contactsQuery->whereIn('user_id', $userIds);
            } else {
                $contactsQuery->where('user_id', $user->id);
            }
        }
        
        $data['allContacts'] = $contactsQuery->get();
        return view('live-chat', $data);
    }

    /**
     * API to fetch fresh live chat data dynamically for auto-polling
     */
    public function fetchData()
    {
        $data = $this->buildConversationsData();
        return response()->json($data);
    }

    /**
     * Send a Live WhatsApp Message Reply & store in Database
     */
    public function sendReply(Request $request)
    {
        $messageText = trim($request->input('message_text', $request->input('text', $request->input('message', ''))));
        $rawPhone = trim($request->input('phone_number', $request->input('phone', '')));

        $mediaFiles = $request->file('media_files', []);
        if (!is_array($mediaFiles)) {
            $mediaFiles = $mediaFiles ? [$mediaFiles] : [];
        }

        // Fallback for single file input if any
        if ($request->hasFile('media_file') && count($mediaFiles) === 0) {
            $mediaFiles[] = $request->file('media_file');
        }

        $type = $request->input('type');
        $contactName = $request->input('contact_name');
        $contactPhone = $request->input('contact_phone');
        $templateName = $request->input('template_name');

        if (empty($messageText) && count($mediaFiles) === 0 && $type !== 'contact' && $type !== 'sticker' && empty($templateName)) {
            return response()->json([
                'success' => false,
                'message' => 'Please type a message text, attach a file, or select a template before sending.'
            ], 200);
        }

        // If phone_number is empty, fallback to latest contact phone from database
        if (empty($rawPhone)) {
            $latestLog = WhatsAppLog::latest()->first();
            $rawPhone = $latestLog ? $latestLog->phone_number : '';
        }

        $phoneNumber = preg_replace('/[^0-9]/', '', $rawPhone);

        if (strlen($phoneNumber) === 10) {
            $phoneNumber = '91' . $phoneNumber;
        }

        $templateName = $request->input('template_name');
        
        $returnedMessages = [];
        $hasError = false;
        $lastErrorMessage = 'Failed to send message.';

        // --- 1. SEND TEMPLATE (if selected) ---
        if (!empty($templateName) && $templateName !== 'custom_chat' && $templateName !== 'live_chat_reply') {
            if ($templateName === '3p_direct_integration_test_template') {
                $apiResult = $this->whatsAppService->sendDirectIntegrationTemplate($phoneNumber);
            } else {
                $apiResult = $this->whatsAppService->sendTicketTemplate($phoneNumber, $templateName, 'en_US', '');
            }

            $status = ($apiResult['success'] ?? false) ? 'success' : 'failed';
            $apiResponse = $apiResult['response'] ?? [];
            if ($status === 'failed') {
                $hasError = true;
                $lastErrorMessage = $apiResult['response']['error']['message'] ?? 'Failed to send template.';
            }

            $log = WhatsAppLog::create([
                'user_id'       => Auth::id(),
                'phone_number'  => $phoneNumber,
                'template_name' => $templateName,
                'message_type'  => 'template',
                'status'        => $status,
                'api_response'  => $apiResponse,
            ]);

            $returnedMessages[] = [
                'id' => $log->id,
                'phone_number' => $phoneNumber,
                'text' => $this->extractMessageContent($log),
                'sender' => 'outgoing',
                'status' => $status,
                'time' => $log->created_at ? $log->created_at->format('h:i A') : 'Just now',
                'created_at' => $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s')
            ];
        }

        // --- 2. MULTIPLE MEDIA FILES LOGIC ---
        if (count($mediaFiles) > 0) {
            foreach ($mediaFiles as $index => $mediaFile) {
                $mediaId = null;
                $mediaType = null;
                $localMediaUrl = null;
                $mediaUrlText = "";
                
                $extension = strtolower($mediaFile->getClientOriginalExtension());
                $mimeType = $mediaFile->getMimeType() ?: 'application/pdf';

                if (in_array($extension, ['cdr', 'pmd', 'psd'])) {
                    $mimeType = 'application/pdf';
                }

                if (str_starts_with($mimeType, 'image/') && !in_array($extension, ['cdr', 'pmd', 'psd'])) {
                    $mediaType = 'image';
                } elseif (str_starts_with($mimeType, 'video/')) {
                    $mediaType = 'video';
                } else {
                    $mediaType = 'document';
                }

                $mediaId = $this->whatsAppService->uploadMedia($mediaFile, $mimeType);

                if ($mediaId) {
                    $safeName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $mediaFile->getClientOriginalName());
                    $finalName = time() . '_' . $index . '_' . $safeName;
                    
                    $mediaFile->move(public_path('whatsapp_media'), $finalName);
                    
                    // Use relative path to avoid mixed content issues if accessed via a different host
                    $mediaUrl = '/public/whatsapp_media/' . $finalName;
                    $mediaUrlText = "\n[Media Attachment: " . $mediaUrl . "]";
                }

                // First file gets the caption text, the rest get empty string
                $textForThisMessage = ($index === 0) ? $messageText : "";

                $apiResult = $this->whatsAppService->sendCustomMessage($phoneNumber, $textForThisMessage, $mediaId, $mediaType, null, null, $mediaFile->getClientOriginalName());
                
                $status = ($apiResult['success'] ?? false) ? 'success' : 'failed';
                $apiResponse = $apiResult['response'] ?? [];
                
                if ($status === 'failed') {
                    $hasError = true;
                    $lastErrorMessage = $apiResult['response']['error']['message'] ?? 'Failed to send media via Meta API.';
                }

                $apiResponse['sent_text'] = trim($textForThisMessage . $mediaUrlText);

                $log = WhatsAppLog::create([
                    'user_id'       => Auth::id(),
                    'phone_number'  => $phoneNumber,
                    'template_name' => 'live_chat_reply',
                    'message_type'  => 'text',
                    'status'        => $status,
                    'api_response'  => $apiResponse,
                ]);

                $returnedMessages[] = [
                    'id' => $log->id,
                    'phone_number' => $phoneNumber,
                    'text' => $this->extractMessageContent($log),
                    'sender' => 'outgoing',
                    'status' => $status,
                    'time' => $log->created_at ? $log->created_at->format('h:i A') : 'Just now',
                    'created_at' => $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s')
                ];
            }
        } 
        // --- 3. TEXT / CONTACT / STICKER MESSAGE LOGIC ---
        elseif (!empty($messageText) || $type === 'contact' || $type === 'sticker') {
            if ($type === 'contact' && $contactName && $contactPhone) {
                $apiResult = $this->whatsAppService->sendContact($phoneNumber, $contactName, $contactPhone);
                $messageText = "📱 *Contact Card*\n*Name:* {$contactName}\n*Phone:* +{$contactPhone}";
            } elseif ($type === 'sticker' && $request->input('sticker_url')) {
                // Download sticker, upload to Meta, and send as sticker
                $stickerUrl = $request->input('sticker_url');
                $tempPath = sys_get_temp_dir() . '/' . uniqid('sticker_') . '.webp';
                file_put_contents($tempPath, file_get_contents($stickerUrl));
                
                $uploadedFile = new \Illuminate\Http\UploadedFile($tempPath, 'sticker.webp', 'image/webp', null, true);
                $mediaId = $this->whatsAppService->uploadMedia($uploadedFile, 'image/webp');
                
                $apiResult = $this->whatsAppService->sendCustomMessage($phoneNumber, "", $mediaId, 'sticker');
                
                $finalName = time() . '_sticker.webp';
                
                if (!file_exists(public_path('whatsapp_media'))) {
                    mkdir(public_path('whatsapp_media'), 0755, true);
                }
                copy($tempPath, public_path('whatsapp_media/' . $finalName));
                $mediaUrl = '/public/whatsapp_media/' . $finalName;
                
                $messageText = "\n[Media Attachment: " . $mediaUrl . "]";
                @unlink($tempPath);
            } else {
                $apiResult = $this->whatsAppService->sendCustomMessage($phoneNumber, $messageText, null, null);
            }

            $status = ($apiResult['success'] ?? false) ? 'success' : 'failed';
            $apiResponse = $apiResult['response'] ?? [];
            
            if ($status === 'failed') {
                $hasError = true;
                $lastErrorMessage = $apiResult['response']['error']['message'] ?? 'Failed to send text message.';
            }

            $apiResponse['sent_text'] = trim($messageText);

            $log = WhatsAppLog::create([
                'user_id'       => Auth::id(),
                'phone_number'  => $phoneNumber,
                'template_name' => 'live_chat_reply',
                'message_type'  => 'text',
                'status'        => $status,
                'api_response'  => $apiResponse,
            ]);

            $returnedMessages[] = [
                'id' => $log->id,
                'phone_number' => $phoneNumber,
                'text' => $this->extractMessageContent($log),
                'sender' => 'outgoing',
                'status' => $status,
                'time' => $log->created_at ? $log->created_at->format('h:i A') : 'Just now',
                'created_at' => $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s')
            ];
        }

        // Auto-register contact if missing
        if (Auth::check()) {
            try {
                Contact::firstOrCreate(
                    ['user_id' => Auth::id(), 'phone' => $phoneNumber],
                    ['name' => null, 'status' => 'active']
                );
            } catch (\Exception $e) {}
        }

        if ($hasError && count($returnedMessages) === 1 && $returnedMessages[0]['status'] === 'failed') {
            return response()->json([
                'success' => false,
                'message' => $lastErrorMessage,
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Message(s) processed successfully!',
            'data' => $returnedMessages
        ], 200);
    }

    /**
     * Forward a message to one or multiple contacts
     */
    public function forwardMessage(Request $request)
    {
        $phones = $request->input('phones', []);
        $messageId = $request->input('message_id');
        
        $log = WhatsAppLog::find($messageId);
        if (!$log) return response()->json(['success' => false, 'message' => 'Message not found']);
        
        $text = $this->extractMessageContent($log);
        
        $mediaUrl = null;
        if (preg_match('/\[Media Attachment:\s*([^\s\]]+)\]/i', $text, $matches)) {
            $mediaUrl = $matches[1];
            $text = trim(str_replace($matches[0], '', $text));
        }
        
        $hasError = false;
        
        foreach ($phones as $phone) {
            if (empty($phone)) continue;
            
            $mediaId = null;
            $mediaType = null;
            
            if ($mediaUrl) {
                // Determine type
                $ext = strtolower(pathinfo($mediaUrl, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','webp'])) $mediaType = 'image';
                elseif (in_array($ext, ['mp4'])) $mediaType = 'video';
                else $mediaType = 'document';
                
                // Resolve local path
                $localPath = null;
                if (str_starts_with($mediaUrl, '/public/')) {
                    $localPath = base_path(ltrim($mediaUrl, '/'));
                } else {
                    $localPath = public_path(ltrim($mediaUrl, '/'));
                }
                
                if (file_exists($localPath)) {
                    $mimeType = mime_content_type($localPath);
                    $uploadedFile = new \Illuminate\Http\UploadedFile($localPath, basename($localPath), $mimeType, null, true);
                    $mediaId = $this->whatsAppService->uploadMedia($uploadedFile, $mimeType);
                } else {
                    // Try downloading if it's external url
                    try {
                        $tempPath = sys_get_temp_dir() . '/' . uniqid() . '.' . $ext;
                        file_put_contents($tempPath, file_get_contents($mediaUrl));
                        $mimeType = mime_content_type($tempPath);
                        $uploadedFile = new \Illuminate\Http\UploadedFile($tempPath, basename($mediaUrl), $mimeType, null, true);
                        $mediaId = $this->whatsAppService->uploadMedia($uploadedFile, $mimeType);
                        @unlink($tempPath);
                    } catch (\Exception $e) {}
                }
            }
            
            $apiResult = $this->whatsAppService->sendCustomMessage($phone, $text, $mediaId, $mediaType);
            $status = ($apiResult['success'] ?? false) ? 'success' : 'failed';
            $apiResponse = $apiResult['response'] ?? [];
            $apiResponse['sent_text'] = trim($text . ($mediaUrl ? "\n[Media Attachment: $mediaUrl]" : ""));
            
            WhatsAppLog::create([
                'user_id' => Auth::id(),
                'phone_number' => $phone,
                'template_name' => 'live_chat_reply',
                'message_type' => 'text',
                'status' => $status,
                'api_response' => $apiResponse,
            ]);
            
            if ($status === 'failed') {
                $hasError = true;
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => $hasError ? 'Message forwarded with some errors.' : 'Message forwarded successfully!'
        ]);
    }

    /**
     * Helper to extract human readable text from WhatsAppLog
     */
    private function extractMessageContent($log)
    {
        if (is_array($log->api_response)) {
            if (isset($log->api_response['sent_text'])) {
                return $log->api_response['sent_text'];
            }
            if (isset($log->api_response['text'])) {
                return $log->api_response['text'];
            }
            if (isset($log->api_response['messages'][0]['id'])) {
                return 'Campaign template message sent (' . ($log->template_name ?? 'Bulk WhatsApp') . ')';
            }
        }

        if ($log->template_name) {
            return 'Campaign: ' . $log->template_name;
        }

        return 'Message';
    }

    /**
     * Clean template tags from text for the sidebar snippet preview
     */
    private function cleanSnippetText($text)
    {
        // Optionally keep the header text followed by a dash, or just strip it.
        // Let's strip the tags but keep the header text so it looks like "Header: Body"
        $text = preg_replace('/\[HEADER\](.*?)\[\/HEADER\]/s', '$1 - ', $text);
        // Remove footer completely from snippet to keep it concise
        $text = preg_replace('/\[FOOTER\](.*?)\[\/FOOTER\]/s', '', $text);
        
        // Remove Media tags for clean text if needed, or just leave it
        $text = str_replace("\n", " ", $text);
        return trim($text);
    }

    /**
     * Delete an entire conversation (all logs for a specific phone number).
     */
    public function destroyChat($phone)
    {
        try {
            $user = Auth::user();
            $userIds = [];
            
            if (!$user->isSuperAdmin()) {
                if ($user->isAdmin()) {
                    $clientIds = \App\Models\User::where('created_by', $user->id)->pluck('id')->toArray();
                    $userIds = array_merge([$user->id], $clientIds);
                } else {
                    $userIds = [$user->id];
                }
            }
            
            $query = WhatsAppLog::where('phone_number', $phone);
            
            if (!$user->isSuperAdmin()) {
                $allowedPhones = \App\Models\Contact::whereIn('user_id', $userIds)->pluck('phone')->toArray();
                $query->where(function($q) use ($userIds, $allowedPhones) {
                    $q->whereIn('user_id', $userIds)
                      ->orWhere(function($subQ) use ($allowedPhones) {
                          $subQ->whereNull('user_id');
                          if (!empty($allowedPhones)) {
                              $subQ->whereIn('phone_number', $allowedPhones);
                          } else {
                              $subQ->whereRaw('1 = 0');
                          }
                      });
                });
            }
            
            $query->delete();

            return response()->json([
                'success' => true,
                'message' => 'Chat deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting chat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete chat.'
            ], 500);
        }
    }

    /**
     * Mark all incoming messages for a phone number as read.
     */
    public function markRead($phone)
    {
        try {
            $user = Auth::user();
            $userIds = [];
            
            if (!$user->isSuperAdmin()) {
                if ($user->isAdmin()) {
                    $clientIds = \App\Models\User::where('created_by', $user->id)->pluck('id')->toArray();
                    $userIds = array_merge([$user->id], $clientIds);
                } else {
                    $userIds = [$user->id];
                }
            }

            $query = WhatsAppLog::where('phone_number', $phone)->where('status', 'received');
            
            if (!$user->isSuperAdmin()) {
                $allowedPhones = \App\Models\Contact::whereIn('user_id', $userIds)->pluck('phone')->toArray();
                $query->where(function($q) use ($userIds, $allowedPhones) {
                    $q->whereIn('user_id', $userIds)
                      ->orWhere(function($subQ) use ($allowedPhones) {
                          $subQ->whereNull('user_id');
                          if (!empty($allowedPhones)) {
                              $subQ->whereIn('phone_number', $allowedPhones);
                          } else {
                              $subQ->whereRaw('1 = 0');
                          }
                      });
                });
            }
            
            $query->update(['status' => 'read']);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mark the latest message for a phone number as unread.
     */
    public function markUnread($phone)
    {
        try {
            $user = Auth::user();
            $userIds = [];
            
            if (!$user->isSuperAdmin()) {
                if ($user->isAdmin()) {
                    $clientIds = \App\Models\User::where('created_by', $user->id)->pluck('id')->toArray();
                    $userIds = array_merge([$user->id], $clientIds);
                } else {
                    $userIds = [$user->id];
                }
            }

            // Find the latest batch of "read" incoming messages
            $query = WhatsAppLog::where('phone_number', $phone)
                ->whereNull('template_name')
                ->where('status', 'read');
                
            if (!$user->isSuperAdmin()) {
                $allowedPhones = \App\Models\Contact::whereIn('user_id', $userIds)->pluck('phone')->toArray();
                $query->where(function($q) use ($userIds, $allowedPhones) {
                    $q->whereIn('user_id', $userIds)
                      ->orWhere(function($subQ) use ($allowedPhones) {
                          $subQ->whereNull('user_id');
                          if (!empty($allowedPhones)) {
                              $subQ->whereIn('phone_number', $allowedPhones);
                          } else {
                              $subQ->whereRaw('1 = 0');
                          }
                      });
                });
            }

            $latestReadMsg = $query->orderBy('updated_at', 'desc')->first();

            if ($latestReadMsg) {
                $timeWindowStart = $latestReadMsg->updated_at->copy()->subSeconds(2);
                $timeWindowEnd = $latestReadMsg->updated_at->copy()->addSeconds(2);
                
                $updateQuery = WhatsAppLog::where('phone_number', $phone)
                    ->whereNull('template_name')
                    ->where('status', 'read')
                    ->whereBetween('updated_at', [$timeWindowStart, $timeWindowEnd]);
                    
                if (!$user->isSuperAdmin()) {
                    $updateQuery->where(function($q) use ($userIds, $allowedPhones) {
                        $q->whereIn('user_id', $userIds)
                          ->orWhere(function($subQ) use ($allowedPhones) {
                              $subQ->whereNull('user_id');
                              if (!empty($allowedPhones)) {
                                  $subQ->whereIn('phone_number', $allowedPhones);
                              } else {
                                  $subQ->whereRaw('1 = 0');
                              }
                          });
                    });
                }
                
                $affected = $updateQuery->update(['status' => 'received']);
                \Log::info("Mark Unread updated {$affected} messages for {$phone}");
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a specific message locally (and attempt Meta API if supported).
     */
    public function destroyMessage($id)
    {
        try {
            $log = WhatsAppLog::findOrFail($id);
            $log->delete();
            return response()->json(['success' => true, 'message' => 'Message deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete message: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update a specific message text locally.
     */
    public function updateMessage(Request $request, $id)
    {
        try {
            $log = WhatsAppLog::findOrFail($id);
            
            // Send the updated text as a new message to the user via WhatsApp API
            $apiResult = $this->whatsAppService->sendCustomMessage($log->phone_number, $request->text, null, null);
            
            if (!isset($apiResult['success']) || !$apiResult['success']) {
                $errorMsg = $apiResult['response']['error']['message'] ?? 'Failed to send updated message via Meta API.';
                return response()->json(['success' => false, 'message' => $errorMsg], 500);
            }

            $apiResponse = is_array($log->api_response) ? $log->api_response : (json_decode($log->api_response, true) ?? []);
            $apiResponse['sent_text'] = $request->text;
            
            $log->api_response = $apiResponse;
            $log->save();

            return response()->json(['success' => true, 'message' => 'Message updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update message: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Toggle Block/Unblock status for a contact.
     */
    public function toggleBlock($phone)
    {
        try {
            $contact = Contact::firstOrCreate(['phone' => $phone]);
            $contact->is_blocked = !$contact->is_blocked;
            $contact->save();

            return response()->json([
                'success' => true, 
                'is_blocked' => $contact->is_blocked,
                'message' => $contact->is_blocked ? 'Contact blocked' : 'Contact unblocked'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Export chat history to a text file.
     */
    public function exportChat($phone)
    {
        $user = Auth::user();
        $userIds = [];
        
        if (!$user->isSuperAdmin()) {
            if ($user->isAdmin()) {
                $clientIds = \App\Models\User::where('created_by', $user->id)->pluck('id')->toArray();
                $userIds = array_merge([$user->id], $clientIds);
            } else {
                $userIds = [$user->id];
            }
        }

        $query = WhatsAppLog::where('phone_number', $phone);
        
        if (!$user->isSuperAdmin()) {
            $allowedPhones = \App\Models\Contact::whereIn('user_id', $userIds)->pluck('phone')->toArray();
            $query->where(function($q) use ($userIds, $allowedPhones) {
                $q->whereIn('user_id', $userIds)
                  ->orWhere(function($subQ) use ($allowedPhones) {
                      $subQ->whereNull('user_id');
                      if (!empty($allowedPhones)) {
                          $subQ->whereIn('phone_number', $allowedPhones);
                      } else {
                          $subQ->whereRaw('1 = 0');
                      }
                  });
            });
        }
        
        $logs = $query->orderBy('created_at', 'asc')->get();

        $contact = \App\Models\Contact::where('phone', $phone)->first();
        $customerName = $contact && $contact->name ? $contact->name : '+' . $phone;

        $content = "WhatsApp Chat History with {$customerName}\n";
        $content .= "Exported on: " . now()->format('Y-m-d H:i:s') . "\n";
        $content .= str_repeat("-", 50) . "\n\n";

        foreach ($logs as $log) {
            $date = $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : 'Unknown Date';
            
            $businessName = 'Business';
            if ($log->user) {
                // Fetch the role if exists, otherwise fallback to name
                $businessName = $log->user->role ?? $log->user->name ?? 'Admin';
            }
            
            $sender = ($log->template_name === 'customer_incoming' || $log->status === 'received') ? $customerName : $businessName;
            $text = $this->extractMessageContent($log);
            
            // Clean up text
            $text = str_replace("\n", " ", $text);
            $content .= "[{$date}] {$sender}: {$text}\n";
        }

        $filename = "Chat_Export_{$phone}_" . date('Ymd_His') . ".txt";

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
