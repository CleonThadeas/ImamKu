<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use Illuminate\Http\Request;

class NotificationLogController extends Controller
{
    public function index(Request $request)
    {
        $query = NotificationLog::with(['user', 'schedule.prayerType'])
            ->latest();

        if ($request->filled('channel')) {
            $query->where('channel', 'like', '%' . $request->channel . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->paginate(20);

        return view('admin.notification-logs.index', compact('logs'));
    }
}
