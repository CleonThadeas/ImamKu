<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Services\PenaltyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PenaltyController extends Controller
{
    use ApiResponse;

    protected PenaltyService $penaltyService;

    public function __construct(PenaltyService $penaltyService)
    {
        $this->penaltyService = $penaltyService;
    }

    /**
     * GET /api/penalties
     * Get penalty history and status for current imam.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $history = $this->penaltyService->getImamHistory($user->id);

        return $this->success([
            'standing' => [
                'penalty_points' => $user->penalty_points,
                'is_restricted' => $user->is_restricted,
            ],
            'history' => [
                'data' => $history->items(),
                'current_page' => $history->currentPage(),
                'last_page' => $history->lastPage(),
            ],
        ], 'Histori penalti berhasil diambil');
    }

    /**
     * GET /api/penalties/ranking
     * Get global ranking of all imams.
     */
    public function ranking(): JsonResponse
    {
        $ranking = $this->penaltyService->getImamRanking();

        return $this->success([
            'ranking' => $ranking,
        ], 'Peringkat imam berhasil diambil');
    }
}
