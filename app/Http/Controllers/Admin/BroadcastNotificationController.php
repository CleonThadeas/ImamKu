<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\BroadcastMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BroadcastNotificationController extends Controller
{
    public function index()
    {
        $config = \App\Models\NotificationConfig::firstOrCreate(
            ['id' => 1],
            ['channels' => 'database', 'reminder_1_minutes' => 90, 'enable_reminder_2' => false, 'reminder_2_minutes' => 30]
        );

        return view('admin.broadcast.index', compact('config'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'channels' => 'required|array',
            'channels.*' => 'string|in:database,whatsapp,mail'
        ]);

        $channels = $request->channels;
        $message = $request->message;
        $imams = User::where('role', 'imam')->where('is_active', true)->get();

        $totalSent = 0;
        $totalFailed = 0;
        $failedChannels = [];

        foreach ($imams as $imam) {
            // Send each channel independently so one failure doesn't block others
            foreach ($channels as $channel) {
                try {
                    // Send notification for this specific channel only
                    $imam->notify(new BroadcastMessage($message, [$channel]));

                    \App\Models\NotificationLog::create([
                        'user_id' => $imam->id,
                        'channel' => strtolower($channel),
                        'type' => 'broadcast',
                        'payload' => ['message' => $message],
                        'status' => 'sent',
                        'sent_at' => now(),
                    ]);

                    $totalSent++;
                } catch (\Exception $e) {
                    Log::warning("Broadcast [{$channel}] failed for imam {$imam->id}: " . $e->getMessage());

                    \App\Models\NotificationLog::create([
                        'user_id' => $imam->id,
                        'channel' => strtolower($channel),
                        'type' => 'broadcast',
                        'payload' => ['message' => $message],
                        'status' => 'failed',
                        'error_message' => substr($e->getMessage(), 0, 255),
                        'sent_at' => now(),
                    ]);

                    $totalFailed++;
                    if (!in_array($channel, $failedChannels)) {
                        $failedChannels[] = $channel;
                    }
                }
            }
        }

        if ($totalFailed > 0 && $totalSent > 0) {
            $failedStr = implode(', ', $failedChannels);
            return back()->with('warning', "Pesan berhasil di-broadcast, namun {$totalFailed} pengiriman gagal pada channel: {$failedStr}. Periksa log untuk detail.");
        }

        if ($totalFailed > 0 && $totalSent === 0) {
            return back()->with('error', 'Semua pengiriman broadcast gagal. Periksa konfigurasi channel (SMTP/Fonnte API Key).');
        }

        return back()->with('success', "Pesan berhasil di-broadcast ke {$imams->count()} imam melalui " . implode(', ', $channels) . '.');
    }
}

