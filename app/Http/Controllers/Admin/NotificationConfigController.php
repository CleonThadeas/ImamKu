<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationConfig;
use Illuminate\Http\Request;

class NotificationConfigController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'channels' => 'array',
            'channels.*' => 'string|in:database,whatsapp,mail',
            'reminder_1_minutes' => 'required|integer|min:1',
            'enable_reminder_2' => 'required|boolean',
            'reminder_2_minutes' => 'nullable|integer|min:1',
        ]);

        $config = NotificationConfig::firstOrCreate(
            ['id' => 1],
            ['channels' => 'database', 'reminder_1_minutes' => 90, 'enable_reminder_2' => false, 'reminder_2_minutes' => 30]
        );

        $channels = $request->has('channels') ? implode(',', $request->channels) : '';

        $config->update([
            'channels' => $channels,
            'reminder_1_minutes' => $request->reminder_1_minutes,
            'enable_reminder_2' => $request->enable_reminder_2,
            'reminder_2_minutes' => $request->reminder_2_minutes ?? 30,
        ]);

        return back()->with('success_config', 'Pengaturan notifikasi otomatis berhasil diperbarui.');
    }
}
