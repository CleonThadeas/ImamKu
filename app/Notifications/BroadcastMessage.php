<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class BroadcastMessage extends Notification
{
    use Queueable;

    public $message;
    public $channels;

    public function __construct($message, $channels = ['database'])
    {
        $this->message = $message;
        $this->channels = $channels;
    }

    public function via(object $notifiable): array
    {
        $via = [];
        
        if (in_array('database', $this->channels)) {
            $via[] = 'database';
        }
        
        if (in_array('mail', $this->channels)) {
            $via[] = 'mail';
        }
        
        if (in_array('telegram', $this->channels)) {
            $chatId = $notifiable->routeNotificationForTelegram();
            if (!empty($chatId) && $chatId !== 'DUMMY_CHAT_ID') {
                $via[] = TelegramChannel::class;
            }
        }
        
        if (in_array('whatsapp', $this->channels)) {
            $via[] = \App\Notifications\Channels\FonnteChannel::class;
        }

        return $via;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Informasi dari Admin ImamKu')
            ->line($this->message)
            ->action('Lihat Dashboard', url('/'))
            ->line('Terima kasih telah menggunakan layanan kami!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'broadcast',
            'message' => $this->message,
        ];
    }

    public function toTelegram(object $notifiable)
    {
        return TelegramMessage::create()
            ->to($notifiable->routeNotificationForTelegram())
            ->content("*Pesan Admin ImamKu*\n\n" . $this->message);
    }
    
    public function toWhatsapp(object $notifiable)
    {
        if (empty($notifiable->phone)) {
            return null;
        }
        return [
            'target' => $notifiable->phone,
            'message' => "*Pesan Admin ImamKu*\n\n" . $this->message,
        ];
    }
}
