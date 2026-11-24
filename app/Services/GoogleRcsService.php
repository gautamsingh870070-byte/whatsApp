<?php

namespace App\Services;

use App\Models\RcsAgent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GoogleRcsService
{
    protected $agent;

    public function __construct(RcsAgent $agent)
    {
        $this->agent = $agent;
    }

    /**
     * Get OAuth 2.0 access token using Service Account credentials.
     * Note: In production, it is recommended to use the google/apiclient package.
     */
    protected function getAccessToken()
    {
        $credentials = json_decode($this->agent->service_account_json, true);
        
        if (!$credentials || !isset($credentials['private_key'])) {
            throw new Exception("Invalid service account JSON for agent.");
        }

        $header = json_encode(['typ' => 'JWT', 'alg' => 'RS256']);
        
        $now = time();
        $payload = json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/rcsbusinessmessaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = '';
        openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if ($response->successful()) {
            return $response->json('access_token');
        }

        Log::error('Google RCS Token Error: ' . $response->body());
        throw new Exception("Failed to obtain Google access token: " . $response->body());
    }

    /**
     * Send an RCS Message.
     */
    public function sendMessage($phoneNumber, $content, $messageId = null)
    {
        $accessToken = $this->getAccessToken();
        
        if (!$messageId) {
            $messageId = uniqid('rcs_', true);
        }

        // Format phone number according to Google requirements (E.164 without '+')
        // Actually Google API requires `phones/+1234567890` or `phones/1234567890`? It requires + format but url encoded or just in the payload.
        // Documentation says the name should be 'phones/PHONE_NUMBER'.
        $formattedPhone = preg_replace('/[^0-9+]/', '', $phoneNumber);
        if (substr($formattedPhone, 0, 1) !== '+') {
            $formattedPhone = '+' . $formattedPhone;
        }
        
        // Ensure + is URL encoded in the URL, or let Http facade handle it?
        // Actually, the endpoint is POST https://rcsbusinessmessaging.googleapis.com/v1/phones/{phoneNumber}/agentMessages
        
        $endpoint = "https://rcsbusinessmessaging.googleapis.com/v1/phones/" . urlencode($formattedPhone) . "/agentMessages";

        $payload = [];
        
        // If content is an array, it might be a rich card or a structured message
        if (is_array($content)) {
             $payload = $content;
        } else {
             // Simple text
             $payload = [
                 'contentMessage' => [
                     'text' => $content
                 ]
             ];
        }
        
        $payload['messageId'] = $messageId;

        $response = Http::withToken($accessToken)
            ->post($endpoint, $payload);

        if ($response->successful()) {
            return [
                'success' => true,
                'message_id' => $messageId,
                'response' => $response->json()
            ];
        }

        Log::error('Google RCS Send Error: ' . $response->body());
        
        return [
            'success' => false,
            'error' => $response->json('error.message') ?? 'Unknown error',
            'details' => $response->json()
        ];
    }
}
