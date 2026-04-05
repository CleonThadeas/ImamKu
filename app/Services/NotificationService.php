<?php

namespace App\Services;

use App\Models\NotificationLog;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Send reminder notification to an imam for their upcoming schedule.
     */
    public function sendReminder(Schedule $schedule, int $minutesBefore): void
    {
        if (!$schedule->user) return;

        $user = $schedule->user;
        $type = "reminder_{$minutesBefore}min";

        // Check if notification already sent (prevent duplicates)
        $alreadySent = NotificationLog::where('user_id', $user->id)
            ->where('schedule_id', $schedule->id)
            ->where('type', $type)
            ->where('status', 'sent')
            ->exists();

        if ($alreadySent) return;

        $prayerName = $schedule->prayerType->name ?? 'Sholat';
        $dateStr = $schedule->date->translatedFormat('l, d F Y');
        $message = "Assalamu'alaikum {$user->name},\n\n"
            . "Pengingat: Anda bertugas sebagai imam {$prayerName} "
            . "pada {$dateStr} dalam {$minutesBefore} menit lagi.\n\n"
            . "Jazakallahu khairan.\n"
            . "— ImamKu System";

        $payload = [
            'prayer' => $prayerName,
            'date' => $schedule->date->toDateString(),
            'minutes_before' => $minutesBefore,
            'message' => $message,
        ];

        $config = \App\Models\NotificationConfig::first();
        $channels = $config ? explode(',', $config->channels) : [];

        // Send database (In-App)
        if (in_array('database', $channels)) {
            $user->notify(new \App\Notifications\BroadcastMessage($message, ['database']));
            
            NotificationLog::create([
                'user_id' => $user->id,
                'schedule_id' => $schedule->id,
                'channel' => 'database',
                'type' => $type,
                'payload' => $payload,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }

        // Send email
        if (in_array('mail', $channels)) {
            $this->sendEmail($user, "Pengingat Imam {$prayerName}", $message, $schedule, $type, $payload);
        }

        // Send WhatsApp if phone number available
        if (in_array('whatsapp', $channels) && $user->phone) {
            $this->sendWhatsApp($user, $message, $schedule, $type, $payload);
        }
    }

    /**
     * Send email notification.
     */
    public function sendEmail(User $user, string $subject, string $body, ?Schedule $schedule = null, ?string $type = null, ?array $payload = null): void
    {
        $log = NotificationLog::create([
            'user_id' => $user->id,
            'schedule_id' => $schedule?->id,
            'channel' => 'email',
            'type' => $type ?? 'general',
            'payload' => $payload,
            'status' => 'pending',
        ]);

        try {
            Mail::raw($body, function ($message) use ($user, $subject) {
                $message->to($user->email)
                    ->subject($subject);
            });

            $log->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Email notification failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send WhatsApp notification via Fonnte API.
     */
    public function sendWhatsApp(User $user, string $message, ?Schedule $schedule = null, ?string $type = null, ?array $payload = null): void
    {
        $apiKey = config('imamku.fonnte.api_key');

        if (empty($apiKey) || empty($user->phone)) {
            return;
        }

        $log = NotificationLog::create([
            'user_id' => $user->id,
            'schedule_id' => $schedule?->id,
            'channel' => 'whatsapp',
            'type' => $type ?? 'general',
            'payload' => $payload,
            'status' => 'pending',
        ]);

        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
            ])->post(config('imamku.fonnte.api_url'), [
                'target' => $user->phone,
                'message' => $message,
            ]);

            if ($response->successful() && $response->json('status')) {
                $log->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
            } else {
                $log->update([
                    'status' => 'failed',
                    'error_message' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp notification failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }


}
