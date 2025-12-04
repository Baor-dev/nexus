<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index()
    {
        // Lấy Top 10 User có tổng điểm vote bài viết cao nhất
        // Sử dụng withSum để tính tổng cột value trong bảng votes thông qua posts
        $users = User::withSum(['posts as karma' => function($query) {
            $query->join('votes', 'posts.id', '=', 'votes.votable_id')
                  ->where('votes.votable_type', 'App\Models\Post')
                  ->select(\DB::raw('sum(value)'));
        }], 'value')
        ->orderByDesc('karma')
        ->take(10)
        ->get();

        return view('leaderboard.index', compact('users'));
    }
}