<?php

namespace App\Http\Controllers\Imam;

use App\Http\Controllers\Controller;
use App\Models\RamadanSeason;
use App\Models\Schedule;
use App\Models\SwapRequest;
use App\Services\SwapService;
use Illuminate\Http\Request;

class SwapController extends Controller
{
    protected SwapService $swapService;

    public function __construct(SwapService $swapService)
    {
        $this->swapService = $swapService;
    }

    public function index()
    {
        $user = auth()->user();
        $season = RamadanSeason::where('is_active', true)->first();

        // Riwayat Swap saya
        $mySwapRequests = SwapRequest::with(['schedule.prayerType', 'targetSchedule.prayerType', 'targetSchedule.user'])
            ->where('requester_id', $user->id)
            ->latest()
            ->paginate(10, ['*'], 'my_page');

        // Broadcast Swap yang tersedia untuk diambil
        $availableSwaps = SwapRequest::with(['schedule.prayerType', 'schedule.user', 'requester'])
            ->where('status', 'pending')
            ->where('requester_id', '!=', $user->id)
            ->latest()
            ->paginate(10, ['*'], 'incoming_page');

        $mySchedules = collect();
        if ($season) {
            $rawSchedules = Schedule::with(['prayerType', 'attendance'])
                ->where('season_id', $season->id)
                ->where('user_id', $user->id)
                ->where('date', '>=', now()->toDateString())
                ->join('prayer_types', 'schedules.prayer_type_id', '=', 'prayer_types.id')
                ->orderBy('schedules.date')
                ->orderBy('prayer_types.sort_order')
                ->select('schedules.*')
                ->get();
                
            $mySchedules = $rawSchedules->filter(function($s) {
                if ($s->attendance) return false;
                $pt = $s->prayerTime;
                if (!$pt || !$pt->effective_time) return false;
                $prayerDt = \Carbon\Carbon::parse($s->date->toDateString() . ' ' . $pt->effective_time);
                return now()->diffInMinutes($prayerDt, false) >= 120;
            })->values();
        }

        return view('imam.swaps.index', compact('mySwapRequests', 'availableSwaps', 'mySchedules'));
    }

    public function create()
    {
        $user = auth()->user();
        $season = RamadanSeason::where('is_active', true)->first();

        $mySchedules = collect();

        if ($season) {
            $rawSchedules = Schedule::with(['prayerType', 'attendance'])
                ->where('season_id', $season->id)
                ->where('user_id', $user->id)
                ->where('date', '>=', now()->toDateString())
                ->join('prayer_types', 'schedules.prayer_type_id', '=', 'prayer_types.id')
                ->orderBy('schedules.date')
                ->orderBy('prayer_types.sort_order')
                ->select('schedules.*')
                ->get();
                
            $mySchedules = $rawSchedules->filter(function($s) {
                if ($s->attendance) return false;
                $pt = $s->prayerTime;
                if (!$pt || !$pt->effective_time) return false;
                $prayerDt = \Carbon\Carbon::parse($s->date->toDateString() . ' ' . $pt->effective_time);
                return now()->diffInMinutes($prayerDt, false) >= 120;
            })->values();
        }

        return view('imam.swaps.create', compact('mySchedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
        ]);

        try {
            $swap = $this->swapService->requestSwap(
                $request->schedule_id,
                auth()->id()
            );

            // Kirim notifikasi ke semua imam lain
            $imams = \App\Models\User::where('role', 'imam')
                ->where('is_active', true)
                ->where('id', '!=', auth()->id())
                ->get();
            \Illuminate\Support\Facades\Notification::send($imams, new \App\Notifications\SwapRequested($swap));

            return redirect()->route('imam.swaps.index')
                ->with('success', 'Permintaan swap berhasil disiarkan ke semua imam.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    public function respond(Request $request, SwapRequest $swap)
    {
        $request->validate(['action' => 'required|in:accept,cancel']);

        try {
            if ($request->action === 'accept') {
                $request->validate([
                    'target_schedule_id' => 'required|exists:schedules,id'
                ], [
                    'target_schedule_id.required' => 'Anda harus memilih jadwal Anda untuk ditukarkan.'
                ]);

                $updatedSwap = $this->swapService->acceptSwap($swap->id, $request->target_schedule_id, auth()->id());
                
                // Kirim notifikasi konfirmasi ke kedua belah pihak
                $updatedSwap->requester->notify(new \App\Notifications\SwapAccepted($updatedSwap, false));
                auth()->user()->notify(new \App\Notifications\SwapAccepted($updatedSwap, true));

                return redirect()->back()->with('success', 'Swap berhasil diterima dan jadwal kalian telah ditukar.');
            }

            // Cancel action for own request
            if ($swap->requester_id !== auth()->id()) {
                throw new \Exception("Hanya pembuat request yang bisa membatalkan.");
            }
            $this->swapService->rejectSwap($swap->id);
            return redirect()->back()->with('success', 'Swap berhasil dibatalkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
