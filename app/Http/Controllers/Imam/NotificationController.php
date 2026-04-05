<?php

namespace App\Http\Controllers\Imam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $notifications = $user->notifications()->paginate(10);
        
        // Mark as read when visited
        $user->unreadNotifications->markAsRead();

        return view('imam.notifications.index', compact('notifications'));
    }
}
