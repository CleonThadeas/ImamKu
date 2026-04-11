<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\MosqueConfig;
use App\Models\RamadanSeason;
use Illuminate\Http\JsonResponse;

class GuidelineController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/guidelines
     * Returns dynamic configurations related to physical attendance and generic app variables.
     */
    public function index(): JsonResponse
    {
        $season = RamadanSeason::where('is_active', true)->first();
        $config = $season ? MosqueConfig::where('season_id', $season->id)->first() : null;

        return $this->success([
            'windows' => [
                'before_minutes' => $config ? $config->attendance_window_minutes : 30,
                'after_minutes'  => $config ? $config->attendance_window_after_minutes : 30,
            ],
            'penalties' => [
                'attendance_ontime' => config('imamku.penalty.attendance_ontime', 10),
                'attendance_late' => config('imamku.penalty.attendance_late', -5),
                'no_show' => config('imamku.penalty.no_show', -20),
                'swap_expired' => config('imamku.penalty.swap_expired', -10),
                'limit' => config('imamku.penalty.restriction_threshold', -30),
            ]
        ], 'Konfigurasi ketentuan berhasil diambil');
    }
}
