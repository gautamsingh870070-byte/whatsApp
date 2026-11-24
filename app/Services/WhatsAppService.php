<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiUrl;
    protected $token;
    protected $wabaId;
    protected $ticketTemplateName;

    public function __construct()
    {
        // Typically these should be in config/services.php
        // For simplicity we fetch from env directly here
        $this->apiUrl = env('WHATSAPP_API_URL', 'https://graph.facebook.com/v25.0/1234153499790530/messages');
        $this->token = env('WHATSAPP_TOKEN', '');
        $this->wabaId = env('WHATSAPP_WABA_ID', '1097753929357110');
        $this->ticketTemplateName = env('WHATSAPP_TICKET_TEMPLATE', 'stm_customer_support');
    }

    /**
     * Send a template message to a phone number
     *
     * @param string $toPhoneNumber
     * @param string $templateName
     * @return array
     */
    public function sendTemplateMessage(string $toPhoneNumber, string $templateName = 'hello_world')
    {
        try {
            $response = Http::withToken($this->token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl, [
                    'messaging_product' => 'whatsapp',
                    'to' => $toPhoneNumber,
                    'type' => 'template',
                    'template' => [
                        'name' => $templateName,
                        'language' => [
                            'code' => 'en_US'
                        ]
                    ]
                ]);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp API Error: ' . $e->getMessage());
            return [
                'success' => false,
                'status' => 500,
                'response' => ['error' => $e->getMessage()],
            ];
        }
    }

    /**
     * Send a Marketing Template Message with Dynamic Variables and Image Header
     */
    public function sendMarketingTemplate(string $toPhoneNumber, string $templateName, string $languageCode = 'en_US', array $bodyVariables = [], $mediaId = null): array
    {
        $components = [];

        // 1. Image Header Component
        if ($mediaId) {
            $components[] = [
                'type' => 'header',
                'parameters' => [
                    [
                        'type' => 'image',
                        'image' => [
                            'id' => $mediaId
                        ]
                    ]
                ]
            ];
        }

        // 2. Dynamic Text Variables for Body
        if (!empty($bodyVariables)) {
            $bodyParams = [];
            foreach ($bodyVariables as $var) {
                // Ignore empty commas
                if (trim($var) === '') continue; 
                
                $bodyParams[] = [
                    'type' => 'text',
                    'text' => trim($var)
                ];
            }
            if (!empty($bodyParams)) {
                $components[] = [
                    'type' => 'body',
                    'parameters' => $bodyParams
                ];
            }
        }

        // Base Payload
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $toPhoneNumber,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $languageCode
                ]
            ]
        ];

        if (!empty($components)) {
            $payload['template']['components'] = $components;
        }

        try {
            $response = Http::withToken($this->token)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrl, $payload);

            Log::info('Marketing Template Sent', [
                'to' => $toPhoneNumber,
                'template' => $templateName,
                'media_id' => $mediaId,
                'status' => $response->status()
            ]);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('Marketing Template Error: ' . $e->getMessage());
            return [
                'success' => false,
                'status' => 500,
                'response' => ['error' => $e->getMessage()],
            ];
        }
    }

    /**
     * Send Jasper's Market Template
     */
    public function sendJasperTemplate(string $toPhoneNumber, string $templateName = 'jaspers_market_plain_text_v1')
    {
        try {
            $response = Http::withToken($this->token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl, [
                    'messaging_product' => 'whatsapp',
                    'to' => $toPhoneNumber,
                    'type' => 'template',
                    'template' => [
                        'name' => $templateName,
                        'language' => [
                            'code' => 'en_US'
                        ]
                    ]
                ]);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp API Error: ' . $e->getMessage());
            return [
                'success' => false,
                'status' => 500,
                'response' => ['error' => $e->getMessage()],
            ];
        }
    }

    /**
     * Send a Meta-Approved Template Message (e.g. 'stm_special_offer')
     *
     * Supports single OR multiple numbers — call once per number in a loop.
     * Template must be approved in Meta Business Manager.
     *
     * @param string      $toPhoneNumber  Phone number with country code (e.g. )
     * @param string|null $templateName   Template name — defaults to WHATSAPP_TICKET_TEMPLATE in .env
     * @param string      $languageCode   Language code of the approved template (default: 'en_US' for stm_special)
     * @return array
     */
    public function sendTicketTemplate(string $toPhoneNumber, string $templateName = null, string $languageCode = 'en_US', string $bodyParam = 'Lalit Sharma'): array
    {
        $templateName = $templateName ?? $this->ticketTemplateName;

        // Build components only if a body parameter is needed (template has {{1}})
        $components = [];
        if ($bodyParam !== '') {
            $components[] = [
                'type'       => 'body',
                'parameters' => [
                    [
                        'type' => 'text',
                        'text' => $bodyParam,
                    ]
                ],
            ];
        }

        $templatePayload = [
            'name'     => $templateName,
            'language' => [
                'code' => $languageCode,
            ],
        ];

        if (!empty($components)) {
            $templatePayload['components'] = $components;
        }

        try {
            $response = Http::withToken($this->token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl, [
                    'messaging_product' => 'whatsapp',
                    'to'               => $toPhoneNumber,
                    'type'             => 'template',
                    'template'         => $templatePayload,
                ]);

            Log::info('WhatsApp Template Sent', [
                'to'       => $toPhoneNumber,
                'template' => $templateName,
                'language' => $languageCode,
                'param'    => $bodyParam,
                'status'   => $response->status(),
            ]);

            return [
                'success'  => $response->successful(),
                'status'   => $response->status(),
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp Template Error: ' . $e->getMessage());
            return [
                'success'  => false,
                'status'   => 500,
                'response' => ['error' => $e->getMessage()],
            ];
        }
    }


    /**
     * Send a plain text reply to a customer (Customer → Business response).
     *
     * Called by the Webhook controller when a customer sends us a message.
     * This is the Business → Customer reply in the two-way flow.
     *
     * @param string $toPhoneNumber  Customer's phone with country code (e.g. )
     * @param string $text           The reply text to send
     * @return array
     */
    public function sendTextReply(string $toPhoneNumber, string $text): array
    {
        try {
            $response = Http::withToken($this->token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl, [
                    'messaging_product' => 'whatsapp',
                    'to'               => $toPhoneNumber,
                    'type'             => 'text',
                    'text'             => [
                        'preview_url' => false,
                        'body'        => $text,
                    ],
                ]);

            Log::info('📤 WhatsApp Text Reply Sent', [
                'to'     => $toPhoneNumber,
                'status' => $response->status(),
            ]);

            return [
                'success'  => $response->successful(),
                'status'   => $response->status(),
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp Text Reply Error: ' . $e->getMessage());
            return [
                'success'  => false,
                'status'   => 500,
                'response' => ['error' => $e->getMessage()],
            ];
        }
    }

    /**
     * Upload media to WhatsApp API
     */
    /**
     * Upload Media for Template Creation (Requires WHATSAPP_APP_ID)
     * Returns a Resumable Upload Handle (e.g. 4:ZAA...)
     */
    public function uploadTemplateMedia($file, $mimeType)
    {
        $appId = env('WHATSAPP_APP_ID');
        if (empty($appId)) {
            \Illuminate\Support\Facades\Log::error('WHATSAPP_APP_ID is missing in .env');
            return null;
        }

        try {
            $fileSize = $file->getSize();
            $url = "https://graph.facebook.com/v25.0/{$appId}/uploads?file_length={$fileSize}&file_type={$mimeType}";
            
            $sessionResponse = Http::withToken($this->token)->post($url);
            
            if (!$sessionResponse->successful()) {
                \Illuminate\Support\Facades\Log::error('Meta Upload Session Error: ' . $sessionResponse->body());
                return null;
            }
            
            $sessionId = $sessionResponse->json('id');
            $uploadUrl = "https://graph.facebook.com/v25.0/{$sessionId}";
            $fileContent = file_get_contents($file->getRealPath());

            $uploadResponse = Http::withToken($this->token)
                ->withHeaders(['file_offset' => '0'])
                ->withBody($fileContent, $mimeType)
                ->post($uploadUrl);

            if ($uploadResponse->successful()) {
                return $uploadResponse->json('h'); // This is the header_handle
            }

            \Illuminate\Support\Facades\Log::error('Meta File Upload Error: ' . $uploadResponse->body());
            return null;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Template Media Upload Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function uploadMedia($file, $mimeType)
    {
        try {
            // Extract Phone ID from the API URL
            preg_match('/v\d+\.\d+\/(\d+)\/messages/', $this->apiUrl, $matches);
            $phoneId = $matches[1] ?? '1325938567267906';
            $uploadUrl = "https://graph.facebook.com/v25.0/{$phoneId}/media";

            $isFilePath = is_string($file);
            $filePath = $isFilePath ? $file : $file->getRealPath();
            $fileNameForUpload = $isFilePath ? basename($file) : $file->getClientOriginalName();
            
            if ($mimeType === 'application/pdf' && !str_ends_with(strtolower($fileNameForUpload), '.pdf')) {
                $fileNameForUpload = 'document.pdf'; // Force Guzzle to use application/pdf Content-Type
            }
            
            // Force image extension if missing, otherwise Meta marks it as UNKNOWN
            if (str_starts_with($mimeType, 'image/') && !preg_match('/\.(jpe?g|png|gif|webp)$/i', $fileNameForUpload)) {
                $fileNameForUpload .= '.jpeg';
            }

            $response = Http::withToken($this->token)
                ->attach('file', file_get_contents($filePath), $fileNameForUpload, ['Content-Type' => $mimeType])
                ->post($uploadUrl, [
                    'messaging_product' => 'whatsapp',
                ]);

            if ($response->successful()) {
                return $response->json('id');
            }
            Log::error('WhatsApp Media Upload Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('WhatsApp Media Upload Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Send a Contact Card (vCard)
     */
    public function sendContact(string $toPhoneNumber, string $name, string $phone)
    {
        try {
            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $toPhoneNumber,
                'type' => 'contacts',
                'contacts' => [
                    [
                        'name' => [
                            'formatted_name' => $name,
                            'first_name' => $name
                        ],
                        'phones' => [
                            [
                                'phone' => preg_replace('/[^0-9+]/', '', $phone),
                                'type' => 'WORK'
                            ]
                        ]
                    ]
                ]
            ];

            $response = Http::withToken($this->token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl, $payload);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp Send Contact Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'status' => 500,
                'response' => ['error' => $e->getMessage()],
            ];
        }
    }

    /**
     * Send a custom text or media message
     */
    public function sendCustomMessage(string $toPhoneNumber, string $text, $mediaId = null, string $mediaType = null, $visitUrl = null, $callNumber = null, string $fileName = null)
    {
        try {
            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $toPhoneNumber,
            ];

            if ($visitUrl || $callNumber) {
                // If buttons are present, use 'interactive' message type
                $payload['type'] = 'interactive';
                $payload['interactive'] = [
                    'type' => 'button',
                    'body' => ['text' => $text],
                    'action' => ['buttons' => []]
                ];
                
                if ($mediaId) {
                    $payload['interactive']['header'] = [
                        'type' => $mediaType,
                        $mediaType => ['id' => $mediaId]
                    ];
                    if ($mediaType === 'document' && !empty($fileName)) {
                        $payload['interactive']['header'][$mediaType]['filename'] = $fileName;
                    }
                }

                if ($visitUrl) {
                    // Note: interactive messages natively only support 'reply' buttons.
                    // For URL/Call buttons, Meta requires a 'template'.
                    // We are mocking this as a reply button just to prevent API rejection for standard tests,
                    // in reality, templates must be used for URL/CALL buttons.
                    $payload['interactive']['action']['buttons'][] = [
                        'type' => 'reply',
                        'reply' => ['id' => 'btn_1', 'title' => 'Visit']
                    ];
                }
                if ($callNumber) {
                    $payload['interactive']['action']['buttons'][] = [
                        'type' => 'reply',
                        'reply' => ['id' => 'btn_2', 'title' => 'Call']
                    ];
                }
            } else {
                // Standard Text or Media message
                $payload['type'] = $mediaId ? $mediaType : 'text';

                if ($mediaId) {
                    $payload[$mediaType] = [
                        'id' => $mediaId
                    ];
                    if ($mediaType === 'document' && !empty($fileName)) {
                        $payload[$mediaType]['filename'] = $fileName;
                    }
                    // Stickers and Audio do not support captions in WhatsApp API
                    if ($mediaType !== 'sticker' && $mediaType !== 'audio' && !empty($text)) {
                        $payload[$mediaType]['caption'] = $text;
                    }
                } else {
                    $payload['text'] = [
                        'preview_url' => false,
                        'body' => $text
                    ];
                }
            }

            $response = Http::withToken($this->token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl, $payload);

            $resultSuccess = $response->successful();
            $resultJson = $response->json();

            return [
                'success' => $resultSuccess,
                'status' => $response->status(),
                'response' => $resultJson,
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp API Error: ' . $e->getMessage());
            return [
                'success' => false,
                'status' => 500,
                'response' => ['error' => $e->getMessage()],
            ];
        }
    }

    /**
     * Send 3P Direct Integration Test Template
     *
     * This sends the Meta-approved template '3p_direct_integration_test_template'.
     * The template must be approved in Meta Business Manager before use.
     *
     * @param string $toPhoneNumber  Phone number with country code (e.g. )
     * @param string $languageCode   Language code (default: 'en_US')
     * @return array
     */
    public function sendDirectIntegrationTemplate(string $toPhoneNumber, string $languageCode = 'en_US'): array
    {
        $templateName = '3p_direct_integration_test_template';

        try {
            $response = Http::withToken($this->token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl, [
                    'messaging_product' => 'whatsapp',
                    'to'               => $toPhoneNumber,
                    'type'             => 'template',
                    'template'         => [
                        'name'     => $templateName,
                        'language' => [
                            'code' => $languageCode,
                        ],
                    ],
                ]);

            Log::info('WhatsApp 3P Direct Integration Template Sent', [
                'to'       => $toPhoneNumber,
                'template' => $templateName,
                'language' => $languageCode,
                'status'   => $response->status(),
            ]);

            return [
                'success'  => $response->successful(),
                'status'   => $response->status(),
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp 3P Direct Integration Template Error: ' . $e->getMessage());
            return [
                'success'  => false,
                'status'   => 500,
                'response' => ['error' => $e->getMessage()],
            ];
        }
    }

    /**
     * Send a media template message (to bypass the 24-hour window rule)
     * 
     * To use this, you must first create a Template in Meta Business Manager
     * with a Header of type 'Media' (Image/Video/Document).
     *
     * @param string $toPhoneNumber
     * @param string $templateName
     * @param string $mediaId ID of the uploaded media
     * @param string $mediaType 'image', 'video', or 'document'
     * @return array
     */
    public function sendMediaTemplateMessage(string $toPhoneNumber, string $templateName, string $mediaId, string $mediaType = 'image')
    {
        try {
            $response = Http::withToken($this->token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl, [
                    'messaging_product' => 'whatsapp',
                    'to' => $toPhoneNumber,
                    'type' => 'template',
                    'template' => [
                        'name' => $templateName,
                        'language' => [
                            'code' => 'en_US' // Change language code if your template is in a different language (e.g., 'hi' for Hindi)
                        ],
                        'components' => [
                            [
                                'type' => 'header',
                                'parameters' => [
                                    [
                                        'type' => $mediaType,
                                        $mediaType => [
                                            'id' => $mediaId
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp API Error: ' . $e->getMessage());
            return [
                'success' => false,
                'status' => 500,
                'response' => ['error' => $e->getMessage()],
            ];
        }
    }

    /**
     * Fetch live Message Templates from Meta Graph API
     */
    public function getMetaTemplates()
    {
        try {
            $url = "https://graph.facebook.com/v25.0/{$this->wabaId}/message_templates";
            $response = Http::withToken($this->token)->get($url);

            if ($response->successful()) {
                $templates = $response->json('data', []);
                
                $user = auth()->user();
                if ($user && !$user->isSuperAdmin()) {
                    try {
                        if (\Illuminate\Support\Facades\Schema::hasTable('user_templates')) {
                            if ($user->isAdmin()) {
                                $clientIds = \App\Models\User::where('created_by', $user->id)->pluck('id')->toArray();
                                $allowedUserIds = array_merge([$user->id], $clientIds);
                            } else {
                                $allowedUserIds = [$user->id, $user->created_by];
                            }
                            
                            $allowedTemplateNames = \App\Models\UserTemplate::whereIn('user_id', $allowedUserIds)->pluck('template_name')->toArray();
                            
                            $filteredTemplates = [];
                            foreach ($templates as $tpl) {
                                if (in_array($tpl['name'], $allowedTemplateNames)) {
                                    $filteredTemplates[] = $tpl;
                                }
                            }
                            return $filteredTemplates;
                        }
                    } catch (\Exception $e) {
                        // fallback to all if DB error
                    }
                }
                
                return $templates;
            }
            Log::error('Meta Fetch Templates Error: ' . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error('Meta Fetch Templates Exception: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Create a new Message Template directly on Meta WhatsApp Account
     */
    public function createMetaTemplate(array $data)
    {
        try {
            $url = "https://graph.facebook.com/v25.0/{$this->wabaId}/message_templates";

            $components = [];

            if (isset($data['header_type']) && $data['header_type'] === 'IMAGE' && !empty($data['header_handle'])) {
                $components[] = [
                    'type' => 'HEADER',
                    'format' => 'IMAGE',
                    'example' => [
                        'header_handle' => [$data['header_handle']]
                    ]
                ];
            } elseif (!empty($data['header_text'])) {
                $components[] = [
                    'type' => 'HEADER',
                    'format' => 'TEXT',
                    'text' => $data['header_text']
                ];
            }

            $bodyComponent = [
                'type' => 'BODY',
                'text' => $data['body_text']
            ];

            preg_match_all('/\{\{(\d+)\}\}/', $data['body_text'], $matches);
            if (!empty($matches[1])) {
                $maxVar = max($matches[1]);
                $examples = [];
                for ($i = 1; $i <= $maxVar; $i++) {
                    // Get explicit example value if provided, else generic text
                    $examples[] = !empty($data['example_values'][$i]) ? $data['example_values'][$i] : ("ExampleText" . $i);
                }
                $bodyComponent['example'] = [
                    'body_text' => [$examples]
                ];
            }

            $components[] = $bodyComponent;

            if (!empty($data['footer_text'])) {
                $components[] = [
                    'type' => 'FOOTER',
                    'text' => $data['footer_text']
                ];
            }

            if (isset($data['button_type']) && $data['button_type'] === 'URL' && !empty($data['button_text']) && !empty($data['button_url'])) {
                $components[] = [
                    'type' => 'BUTTONS',
                    'buttons' => [
                        [
                            'type' => 'URL',
                            'text' => $data['button_text'],
                            'url' => $data['button_url']
                        ]
                    ]
                ];
            }

            $nameFormatted = strtolower(preg_replace('/[^a-z0-9_]/', '_', $data['name']));

            $payload = [
                'name' => $nameFormatted,
                'language' => $data['language'] ?? 'en_US',
                'category' => strtoupper($data['category'] ?? 'UTILITY'),
                'components' => $components
            ];

            $response = Http::withToken($this->token)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            \Illuminate\Support\Facades\Log::info('Meta Template Payload:', $payload);
            \Illuminate\Support\Facades\Log::info('Meta Template Response:', $response->json() ?? []);

            return [
                'success' => $response->successful(),
                'status'  => $response->status(),
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('Meta Create Template Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'status'  => 500,
                'response' => ['error' => $e->getMessage()],
            ];
        }
    }
}
