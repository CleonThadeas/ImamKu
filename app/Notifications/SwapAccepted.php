<?php

namespace App\Notifications;

use App\Models\SwapRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SwapAccepted extends Notification
{
    use Queueable;

    public $swapRequest;
    public $isAcceptor;

    public function __construct(SwapRequest $swapRequest, $isAcceptor = false)
    {
        $this->swapRequest = $swapRequest;
        $this->isAcceptor = $isAcceptor;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        if ($this->isAcceptor) {
            $msg = 'Anda berhasil menerima pertukaran jadwal ' . $this->swapRequest->schedule->prayerType->name . ' dengan Imam ' . $this->swapRequest->requester->name . '.';
        } else {
            // Karena jadwal asli sudah berpindah tangan, "schedule->user" saat ini adalah sang Acceptor.
            $acceptorName = $this->swapRequest->schedule->user->name;
            $msg = 'Permintaan tukar jadwal Anda pada ' . $this->swapRequest->schedule->date->format('d M Y') . ' telah diterima oleh Imam ' . $acceptorName . '.';
        }
        
        return [
            'type' => 'swap_accepted',
            'message' => $msg,
            'swap_request_id' => $this->swapRequest->id,
        ];
    }
}
