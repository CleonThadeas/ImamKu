<?php

namespace App\Notifications;

use App\Models\SwapRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SwapRequested extends Notification
{
    use Queueable;

    public $swapRequest;

    public function __construct(SwapRequest $swapRequest)
    {
        $this->swapRequest = $swapRequest;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // In-app notification only
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'swap_requested',
            'message' => 'Imam ' . $this->swapRequest->requester->name . ' menawarkan pertukaran untuk jadwal ' . $this->swapRequest->schedule->prayerType->name . ' pada ' . $this->swapRequest->schedule->date->format('d M Y') . '.',
            'swap_request_id' => $this->swapRequest->id,
        ];
    }
}
