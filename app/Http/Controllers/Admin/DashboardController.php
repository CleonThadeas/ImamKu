<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RamadanSeason;
use App\Models\Schedule;
use App\Models\User;
use App\Models\NotificationLog;

class DashboardController extends Controller
{
    public function index()
    {
        $season = RamadanSeason::where('is_active', true)->first();
        $today = now()->toDateString();

        $stats = [
            'total_imams' => User::where('role', 'imam')->where('is_active', true)->count(),
            'total_schedules' => $season ? Schedule::where('season_id', $season->id)->whereNotNull('user_id')->count() : 0,
            'empty_slots' => $season ? Schedule::where('season_id', $season->id)->whereNull('user_id')->count() : 0,
            'today_schedules' => $season ? Schedule::with(['prayerType', 'user'])
                ->where('season_id', $season->id)
                ->where('date', $today)
                ->join('prayer_types', 'schedules.prayer_type_id', '=', 'prayer_types.id')
                ->orderBy('prayer_types.sort_order')
                ->select('schedules.*')
                ->get() : collect(),
            'notifications_sent' => NotificationLog::where('status', 'sent')->count(),
        ];

        return view('admin.dashboard', compact('season', 'stats'));
    }
}
