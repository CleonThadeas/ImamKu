<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\NotificationResource;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/notifications
     * Daftar notifikasi imam (paginated).
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->notifications()
            ->paginate(20);

        return $this->success([
            'notifications' => NotificationResource::collection($notifications),
            'unread_count'  => $request->user()->unreadNotifications()->count(),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page'    => $notifications->lastPage(),
                'per_page'     => $notifications->perPage(),
                'total'        => $notifications->total(),
            ],
        ], 'Notifikasi berhasil diambil');
    }

    /**
     * POST /api/notifications/read-all
     * Tandai semua notifikasi sebagai sudah dibaca.
     */
    public function readAll(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return $this->success(null, 'Semua notifikasi telah ditandai dibaca');
    }

    /**
     * POST /api/notifications/{id}/read
     * Tandai satu notifikasi sebagai dibaca.
     */
    public function read(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return $this->success(null, 'Notifikasi ditandai dibaca');
    }
}
