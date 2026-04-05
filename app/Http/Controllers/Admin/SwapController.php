<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SwapRequest;
use Illuminate\Http\Request;

class SwapController extends Controller
{
    /**
     * Tampilkan monitoring seluruh request swap jadwal secara global.
     */
    public function index(Request $request)
    {
        $swaps = SwapRequest::with([
            'requester', 
            'schedule.user', 
            'schedule.prayerType', 
            'targetSchedule.user', 
            'targetSchedule.prayerType'
        ])
        ->latest()
        ->paginate(20);

        return view('admin.swaps.index', compact('swaps'));
    }
}
