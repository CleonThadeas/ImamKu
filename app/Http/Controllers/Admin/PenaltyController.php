<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PenaltyService;
use Illuminate\Http\Request;

class PenaltyController extends Controller
{
    protected PenaltyService $penaltyService;

    public function __construct(PenaltyService $penaltyService)
    {
        $this->penaltyService = $penaltyService;
    }

    /**
     * Display imam ranking/leaderboard by penalty points.
     */
    public function index()
    {
        $imams = $this->penaltyService->getImamRanking();
        $penaltyConfig = config('imamku.penalty');

        return view('admin.penalties.index', compact('imams', 'penaltyConfig'));
    }

    /**
     * Show penalty history for a specific imam.
     */
    public function history(User $user)
    {
        $logs = $this->penaltyService->getImamHistory($user->id);

        return view('admin.penalties.history', compact('user', 'logs'));
    }

    /**
     * Lift restriction on an imam.
     */
    public function liftRestriction(User $user)
    {
        $this->penaltyService->liftRestriction($user->id);

        return redirect()->back()
            ->with('success', "Pembatasan untuk {$user->name} berhasil diangkat.");
    }
}
