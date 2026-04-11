<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteChannel
{
    public function send($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toWhatsapp')) {
            $data = $notification->toWhatsapp($notifiable);
            
            if (!$data) {
                return;
            }

            $apiKey = config('imamku.fonnte.api_key', env('FONNTE_API_KEY'));

            if (empty($apiKey)) {
                Log::warning('FonnteChannel: FONNTE_API_KEY is not set.');
                return;
            }

            try {
                /** @var \Illuminate\Http\Client\Response $response */
                $response = Http::withHeaders([
                    'Authorization' => $apiKey,
                ])->post('https://api.fonnte.com/send', [
                    'target' => $data['target'],
                    'message' => $data['message'],
                ]);

                if (!$response->successful()) {
                    Log::warning('FonnteChannel: API returned error', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('FonnteChannel: Failed to send WhatsApp', [
                    'target' => $data['target'] ?? 'unknown',
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}

