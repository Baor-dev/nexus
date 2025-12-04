<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // API Check Real-time
    public function check()
    {
        $user = auth()->user();
        $notifications = $user->notifications()->limit(10)->get(); // Lấy 10 thông báo mới nhất
        
        // Render HTML từ file partial vừa tạo
        $html = view('layouts.partials.notifications', compact('notifications'))->render();

        return response()->json([
            'count' => $user->unreadNotifications->count(),
            'html' => $html
        ]);
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            return redirect($notification->data['link']);
        }

        return back();
    }
    
    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Đã đánh dấu tất cả là đã đọc.');
    }
}