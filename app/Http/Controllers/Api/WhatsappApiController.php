<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WhatsappApiController extends Controller
{
    public function whatsappApi()
    {
        $accessToken = env('WHATSAPP_TOKEN');
        $url = env('WHATSAPP_API_URL', 'https://graph.facebook.com/v25.0/1234153499790530/messages');

        $data = [
            'messaging_product' => 'whatsapp',
            'to'                => '',
            'type'              => 'text',
            'text'              => [
                'body' => 'What can I help you with today?'
            ]
        ];

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return response()->json([
            'status_code' => $httpCode,
            'response' => json_decode($response, true),
        ]);
    }
}
