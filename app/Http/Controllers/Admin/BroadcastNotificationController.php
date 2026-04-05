<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\BroadcastMessage;
use Illuminate\Http\Request;

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

        $errors = false;
        $imams = User::where('role', 'imam')->where('is_active', true)->get();
        foreach ($imams as $imam) {
            try {
                $imam->notify(new BroadcastMessage($request->message, $request->channels));
                
                foreach ($request->channels as $channel) {
                    \App\Models\NotificationLog::create([
                        'user_id' => $imam->id,
                        'channel' => strtolower($channel),
                        'type' => 'broadcast',
                        'payload' => ['message' => $request->message],
                        'status' => 'sent',
                        'sent_at' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Broadcast failed for imam ' . $imam->id . ': ' . $e->getMessage());
                
                foreach ($request->channels as $channel) {
                    \App\Models\NotificationLog::create([
                        'user_id' => $imam->id,
                        'channel' => strtolower($channel),
                        'type' => 'broadcast',
                        'payload' => ['message' => $request->message],
                        'status' => 'failed',
                        'error_message' => substr($e->getMessage(), 0, 255),
                        'sent_at' => now(),
                    ]);
                }
                $errors = true;
            }
        }

        if ($errors) {
            return back()->with('success', 'Pesan berhasil di-broadcast ke beberapa imam, namun ada beberapa perangkat yang gagal dikirim (misal nomor atau chat_id tidak valid).');
        }
        
        return back()->with('success', 'Pesan berhasil di-broadcast ke seluruh imam.');
    }
}
