<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show($id)
    {
        $user = User::findOrFail($id);
        
        // Lấy bài viết của user này (kèm phân trang)
        $posts = $user->posts()
                      ->with(['community', 'votes'])
                      ->withCount('allComments')
                      ->latest()
                      ->paginate(10);

        // Tính tổng điểm Karma (Tổng vote nhận được từ các bài viết)
        $karma = $user->posts->sum(function($post) {
            return $post->votes->sum('value');
        });

        return view('users.show', compact('user', 'posts', 'karma'));
    }
}
