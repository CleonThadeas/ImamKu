<?php

namespace App\Http\Controllers\Imam;

use App\Http\Controllers\Controller;
use App\Services\PenaltyService;
use Illuminate\Http\Request;

class PenaltyController extends Controller
{
    public function index(PenaltyService $penaltyService)
    {
        $user = auth()->user();
        $logs = $penaltyService->getImamHistory($user->id);
        $penaltyConfig = config('imamku.penalty');

        return view('imam.penalties.index', compact('user', 'logs', 'penaltyConfig'));
    }
}
