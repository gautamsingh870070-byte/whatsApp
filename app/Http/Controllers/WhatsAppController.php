<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppLog;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class WhatsAppController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Show the Send WhatsApp Message Form
     */
    public function showSendForm()
    {
        $user = Auth::user();
        
        $todaysCount = WhatsAppLog::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->count();
            
        $allocatedToClients = 0;
        if ($user->isAdmin()) {
            $allocatedToClients = \App\Models\User::where('created_by', $user->id)->sum('daily_limit');
        }
        
        $remainingQuota = max(0, $user->daily_limit - $allocatedToClients - $todaysCount);
        if ($user->isSuperAdmin()) {
            $remainingQuota = PHP_INT_MAX; // Super Admin has no practical limit
        }

        $recentLogsQuery = WhatsAppLog::query();
        if (!$user->isSuperAdmin()) {
            if ($user->isAdmin()) {
                $clientIds = \App\Models\User::where('created_by', $user->id)->pluck('id')->toArray();
                $userIds = array_merge([$user->id], $clientIds);
                $recentLogsQuery->whereIn('user_id', $userIds);
            } else {
                $recentLogsQuery->where('user_id', $user->id);
            }
        }
        $recentLogs = $recentLogsQuery->latest()->take(5)->get();

        $metaTemplates = $this->whatsAppService->getMetaTemplates();

        return view('send-wa', compact('remainingQuota', 'recentLogs', 'metaTemplates'));
    }

    /**
     * Process bulk messages
     */
    public function sendBulk(Request $request)
    {
        $user = Auth::user();

        $todaysCount = WhatsAppLog::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $allocatedToClients = 0;
        if ($user->isAdmin()) {
            $allocatedToClients = \App\Models\User::where('created_by', $user->id)->sum('daily_limit');
        }

        $remainingQuota = max(0, $user->daily_limit - $allocatedToClients - $todaysCount);
        if ($user->isSuperAdmin()) {
            $remainingQuota = PHP_INT_MAX;
        }

        $rules = [
            'phone_numbers' => 'required|string',
            'campaign_name' => 'required|string',
            'scheduled_at' => 'nullable|date',
        ];

        if ($request->input('campaign_name') === 'custom_campaign') {
            $rules['message_text'] = 'nullable|string|max:300';
            $rules['media_files'] = 'nullable|array';
            $rules['media_files.*'] = 'file|mimes:jpg,jpeg,png,gif,pdf,mp4,doc,docx,xls,xlsx,cdr,pmd,psd';
            $rules['visit_url'] = 'nullable|url';
            $rules['call_number'] = 'nullable|string';
        }

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            if ($request->input('campaign_name') !== 'custom_campaign') return;
            if (!$request->hasFile('media_files')) {
                return;
            }

            foreach ($request->file('media_files') as $file) {
                if (!$file || !$file->isValid()) {
                    $validator->errors()->add(
                        'media_files',
                        'One of the uploaded files is invalid.'
                    );
                    continue;
                }

                $extension = strtolower($file->getClientOriginalExtension());
                $sizeKb = $file->getSize() / 1024;

                if (
                    in_array($extension, ['jpg', 'jpeg', 'png', 'gif']) &&
                    $sizeKb > 2048
                ) {
                    $validator->errors()->add(
                        'media_files',
                        $file->getClientOriginalName() . ' must not exceed 2MB.'
                    );
                }

                if (
                    in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'cdr', 'pmd', 'psd']) &&
                    $sizeKb > 2048
                ) {
                    $validator->errors()->add(
                        'media_files',
                        $file->getClientOriginalName() . ' must not exceed 2MB.'
                    );
                }

                if ($extension === 'mp4' && $sizeKb > 3072) {
                    $validator->errors()->add(
                        'media_files',
                        $file->getClientOriginalName() . ' must not exceed 3MB.'
                    );
                }
            }
        });

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $rawNumbers = $request->input('phone_numbers');
        $messageText = $request->input('message_text');
        $campaignName = $request->input('campaign_name');
        $visitUrl = $request->input('visit_url');
        $callNumber = $request->input('call_number');

        if ($campaignName !== 'custom_campaign') {
            $metaTemplates = $this->whatsAppService->getMetaTemplates();

            $templateBody = 'Campaign template message sent (' . $campaignName . ')';

            foreach ($metaTemplates as $tpl) {
                if (($tpl['name'] ?? '') !== $campaignName) {
                    continue;
                }

                $headerText = '';
                $bodyText = '';
                $footerText = '';

                foreach (($tpl['components'] ?? []) as $component) {
                    $type = strtoupper($component['type'] ?? '');

                    if ($type === 'HEADER' && isset($component['text'])) {
                        $headerText = '[HEADER]' . $component['text'] . '[/HEADER]';
                    }

                    if ($type === 'BODY' && isset($component['text'])) {
                        $bodyText = $component['text'];
                    }

                    if ($type === 'FOOTER' && isset($component['text'])) {
                        $footerText = '[FOOTER]' . $component['text'] . '[/FOOTER]';
                    }
                }

                if ($bodyText !== '') {
                    $templateBody = $headerText . $bodyText . $footerText;
                }

                break;
            }

            $messageText = $templateBody;
        }

        $numbers = preg_split(
            '/[\s,]+/',
            $rawNumbers,
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        $numbers = array_map(function ($number) {
            $number = preg_replace('/[^0-9]/', '', $number);

            if (strlen($number) === 10) {
                $number = '91' . $number;
            }

            return $number;
        }, $numbers);

        $numbers = array_values(array_unique(array_filter($numbers)));

        $totalToProcess = count($numbers);

        if ($totalToProcess === 0) {
            return redirect()
                ->back()
                ->withErrors([
                    'phone_numbers' => 'Please enter at least one valid phone number.'
                ])
                ->withInput();
        }

        if ($totalToProcess > $remainingQuota) {
            return redirect()
                ->back()
                ->withErrors([
                    'phone_numbers' =>
                        "You are trying to send {$totalToProcess} messages, " .
                        "but you only have {$remainingQuota} remaining today."
                ])
                ->withInput();
        }

        $uploadedFiles = [];

        if ($request->hasFile('media_files')) {
            foreach ($request->file('media_files') as $file) {
                if ($file && $file->isValid()) {
                    $uploadedFiles[] = $file;
                }
            }
        }

        if ($request->filled('scheduled_at') || $request->has('is_scheduled')) {
            $timezone = config('app.timezone', 'Asia/Kolkata');

            $scheduledAt = $request->filled('scheduled_at')
                ? Carbon::parse($request->scheduled_at, $timezone)
                : Carbon::now($timezone);

            $scheduledMedia = [];

            foreach ($uploadedFiles as $file) {
                $extension = strtolower($file->getClientOriginalExtension());
                $mimeType = $file->getMimeType() ?: 'application/pdf';

                if (in_array($extension, ['cdr', 'pmd', 'psd'])) {
                    $mimeType = 'application/pdf';
                }

                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']) && !in_array($extension, ['cdr', 'pmd', 'psd'])) {
                    $mediaType = 'image';
                } elseif ($extension === 'mp4') {
                    $mediaType = 'video';
                } else {
                    $mediaType = 'document';
                }

                $mediaId = $this->whatsAppService->uploadMedia(
                    $file,
                    $mimeType
                );

                if ($mediaId) {
                    $scheduledMedia[] = [
                        'media_id' => $mediaId,
                        'media_type' => $mediaType,
                        'file_name' => $file->getClientOriginalName(),
                    ];
                }
            }

            \App\Models\ScheduledMessage::create([
                'user_id' => $user->id,
                'campaign_name' => $campaignName,
                'phone_numbers' => $rawNumbers,
                'message_text' => $messageText ?? '',
                'media_id' => !empty($scheduledMedia)
                    ? json_encode($scheduledMedia)
                    : null,
                'media_type' => !empty($scheduledMedia)
                    ? 'multiple'
                    : 'text',
                'visit_url' => $visitUrl,
                'call_number' => $callNumber,
                'scheduled_at' => $scheduledAt,
                'status' => 'pending',
            ]);

            return redirect()
                ->route('schedule.log')
                ->with('success', 'Campaign scheduled successfully!');
        }

        $mediaItems = [];

        foreach ($uploadedFiles as $file) {
            $extension = strtolower($file->getClientOriginalExtension());

            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                $mediaType = 'image';
                $mimeType = $extension === 'jpg'
                    ? 'image/jpeg'
                    : 'image/' . $extension;
            } elseif ($extension === 'mp4') {
                $mediaType = 'video';
                $mimeType = 'video/mp4';
            } else {
                $mediaType = 'document';
                $mimeType = $file->getMimeType();
            }

            try {
                $mediaId = $this->whatsAppService->uploadMedia(
                    $file,
                    $mimeType
                );

                if ($mediaId) {
                    $mediaItems[] = [
                        'media_id' => $mediaId,
                        'media_type' => $mediaType,
                        'file_name' => $file->getClientOriginalName(),
                    ];
                }
            } catch (\Throwable $e) {
                \Log::error('WhatsApp media upload exception', [
                    'file' => $file->getClientOriginalName(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (!empty($uploadedFiles) && empty($mediaItems)) {
            return redirect()
                ->back()
                ->withErrors([
                    'media_error' => 'Failed to upload media to WhatsApp API.'
                ])
                ->withInput();
        }

        $successCount = 0;
        $failCount = 0;
        $lastError = null;

        foreach ($numbers as $number) {
            $mainResponse = null;

            try {
                if ($campaignName === 'jaspers_market_plain_text_v1') {
                    $mainResponse = $this->whatsAppService
                        ->sendJasperTemplate(
                            $number,
                            $campaignName
                        );
                } elseif ($campaignName === 'stm_digital') {
                    $mainResponse = $this->whatsAppService
                        ->sendTicketTemplate(
                            $number,
                            'stm_digital',
                            'en',
                            ''
                        );
                } elseif ($campaignName === 'stm_customer_support') {
                    $mainResponse = $this->whatsAppService
                        ->sendTicketTemplate(
                            $number,
                            'stm_customer_support',
                            'en_US',
                            ''
                        );
                } elseif ($campaignName === '3p_direct_integration_test_template') {
                    $mainResponse = $this->whatsAppService
                        ->sendDirectIntegrationTemplate($number);
                } elseif (
                    $campaignName !== 'custom_campaign' &&
                    !empty($campaignName)
                ) {
                    // Extract language code and verify header from meta templates cache
                    $languageCode = 'en_US'; // default
                    $hasMediaHeader = false;
                    $metaTemplates = $this->whatsAppService->getMetaTemplates();
                    foreach ($metaTemplates as $tpl) {
                        if (($tpl['name'] ?? '') === $campaignName) {
                            $languageCode = $tpl['language'] ?? 'en_US';
                            if (!empty($tpl['components'])) {
                                foreach ($tpl['components'] as $comp) {
                                    if (strtoupper($comp['type']) === 'HEADER' && in_array(strtoupper($comp['format'] ?? ''), ['IMAGE', 'VIDEO', 'DOCUMENT'])) {
                                        $hasMediaHeader = true;
                                    }
                                }
                            }
                            break;
                        }
                    }

                    // Parse variables from message_text (comma separated)
                    $bodyVariables = [];
                    $originalMessageText = $request->input('message_text');
                    if (!empty($originalMessageText)) {
                        $bodyVariables = array_map('trim', explode(',', $originalMessageText));
                    }

                    // Get header media ID if uploaded, or fetch from saved template
                    $mediaId = null;
                    if ($hasMediaHeader) {
                        if (!empty($mediaItems) && in_array($mediaItems[0]['media_type'], ['image', 'video', 'document'])) {
                            $mediaId = $mediaItems[0]['media_id'];
                        } else {
                            try {
                                if (\Illuminate\Support\Facades\Schema::hasTable('user_templates')) {
                                    $userTemplate = \App\Models\UserTemplate::where('template_name', $campaignName)->first();
                                    if ($userTemplate) {
                                        $componentsData = json_decode($userTemplate->components, true);
                                        \Log::info('Auto-upload found user template', ['local_image_path' => $componentsData['local_image_path'] ?? null]);
                                        if (!empty($componentsData['local_image_path'])) {
                                            $filePath = storage_path('app/public/' . $componentsData['local_image_path']);
                                            \Log::info('Checking file path: ' . $filePath . ' exists: ' . (file_exists($filePath) ? 'yes' : 'no'));
                                            if (file_exists($filePath)) {
                                                $detectedMime = mime_content_type($filePath);
                                                if (!$detectedMime) $detectedMime = 'image/jpeg';
                                                
                                                $uploadedMediaId = $this->whatsAppService->uploadMedia($filePath, $detectedMime);
                                                \Log::info('Auto-uploaded media ID: ' . $uploadedMediaId);
                                                if ($uploadedMediaId) {
                                                    $mediaId = $uploadedMediaId;
                                                }
                                            }
                                        }
                                    }
                                }
                            } catch (\Exception $e) {
                                \Log::error('Auto-upload template image failed: ' . $e->getMessage());
                            }
                        }
                    }

                    $mainResponse = $this->whatsAppService
                        ->sendMarketingTemplate(
                            $number,
                            $campaignName,
                            $languageCode,
                            $bodyVariables,
                            $mediaId
                        );
                } else {
                    if (
                        !empty($messageText) ||
                        !empty($visitUrl) ||
                        !empty($callNumber)
                    ) {
                        $mainResponse = $this->whatsAppService
                            ->sendCustomMessage(
                                $number,
                                $messageText ?? '',
                                null,
                                'text',
                                $visitUrl,
                                $callNumber
                            );
                    } else {
                        $mainResponse = [
                            'success' => true,
                            'response' => []
                        ];
                    }

                    foreach ($mediaItems as $media) {
                        try {
                            $mediaResponse = $this->whatsAppService
                                ->sendCustomMessage(
                                    $number,
                                    '',
                                    $media['media_id'],
                                    $media['media_type'],
                                    null,
                                    null,
                                    $media['file_name'] ?? null
                                );

                            $mediaSuccess = is_array($mediaResponse)
                                && !empty($mediaResponse['success']);

                            $mediaApiResponse = is_array($mediaResponse)
                                ? ($mediaResponse['response'] ?? [])
                                : [];

                            $mediaApiResponse['file_name'] =
                                $media['file_name'];

                            WhatsAppLog::create([
                                'user_id' => $user->id,
                                'phone_number' => $number,
                                'template_name' => $campaignName,
                                'message_type' => $media['media_type'],
                                'status' => $mediaSuccess
                                    ? 'success'
                                    : 'failed',
                                'api_response' => $mediaApiResponse,
                            ]);
                        } catch (\Throwable $e) {
                            \Log::error('WhatsApp media send exception', [
                                'phone' => $number,
                                'file' => $media['file_name'],
                                'error' => $e->getMessage(),
                            ]);

                            WhatsAppLog::create([
                                'user_id' => $user->id,
                                'phone_number' => $number,
                                'template_name' => $campaignName,
                                'message_type' => $media['media_type'],
                                'status' => 'failed',
                                'api_response' => [
                                    'error' => $e->getMessage(),
                                    'file_name' => $media['file_name'],
                                ],
                            ]);
                        }
                    }
                }

                if (!is_array($mainResponse)) {
                    $mainResponse = [
                        'success' => false,
                        'response' => [
                            'error' => 'Invalid response returned from WhatsApp service.',
                            'raw_response' => $mainResponse,
                        ],
                    ];
                }

                $mainResponse['success'] =
                    $mainResponse['success'] ?? false;

                $mainResponse['response'] =
                    $mainResponse['response'] ?? [];
            } catch (\Throwable $e) {
                \Log::error('WhatsApp send exception', [
                    'phone' => $number,
                    'campaign' => $campaignName,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);

                $mainResponse = [
                    'success' => false,
                    'response' => [
                        'error' => $e->getMessage(),
                    ],
                ];
            }

            if (
                $campaignName !== 'custom_campaign' ||
                !empty($messageText)
            ) {
                $apiResponseToSave =
                    $mainResponse['response'] ?? [];

                if (!empty($messageText)) {
                    $apiResponseToSave['sent_text'] = $messageText;
                }

                WhatsAppLog::create([
                    'user_id' => $user->id,
                    'phone_number' => $number,
                    'template_name' => $campaignName,
                    'message_type' => $campaignName !== 'custom_campaign'
                        ? 'template'
                        : 'text',
                    'status' => !empty($mainResponse['success'])
                        ? 'success'
                        : 'failed',
                    'api_response' => $apiResponseToSave,
                ]);
            }

            if (!empty($mainResponse['success'])) {
                $successCount++;
            } else {
                $failCount++;

                $apiData = $mainResponse['response'] ?? [];

                if (
                    isset($apiData['error']['code']) &&
                    $apiData['error']['code'] == 131030
                ) {
                    $lastError =
                        "Facebook Test Mode Restriction: You MUST add the number " .
                        $number .
                        " to your Allowed List in Meta Developer Dashboard.";
                } elseif (
                    isset($apiData['error']['code']) &&
                    $apiData['error']['code'] == 100
                ) {
                    $lastError =
                        "Meta API Phone ID / Permission Error (Code 100): " .
                        "The Phone Number ID in .env does not exist or access " .
                        "token lacks asset permission.";
                } else {
                    $lastError = is_array($apiData)
                        ? json_encode($apiData)
                        : $apiData;
                }
            }
        }

        $message = "Campaign completed. Successful: {$successCount}, Failed: {$failCount}.";

        if ($lastError) {
            $message .= " Last error: {$lastError}";
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }
}
