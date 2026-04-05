<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class FonnteChannel
{
    public function send($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toWhatsapp')) {
            $data = $notification->toWhatsapp($notifiable);
            
            if (!$data) {
                return;
            }

            Http::withHeaders([
                'Authorization' => env('FONNTE_API_KEY')
            ])->post('https://api.fonnte.com/send', [
                'target' => $data['target'],
                'message' => $data['message'],
            ]);
        }
    }
}
