<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Community;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user', 'community', 'votes'])
                     ->withCount('allComments')
                     ->withSum('votes', 'value');

        // Biến Context để giữ Tag trên thanh Search
        $contextType = null;
        $contextId = null;
        $contextLabel = null;

        $isSearching = false;

        // 1. Logic Tìm kiếm
        if ($search = $request->query('search')) {
            $isSearching = true;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // 2. Scoped Search & Restore Context
        if ($communityId = $request->query('community_id')) {
            $query->where('community_id', $communityId);
            
            // Lấy thông tin để hiển thị lại Tag
            $c = Community::find($communityId);
            if ($c) {
                $contextType = 'community';
                $contextId = $c->id;
                $contextLabel = 'c/' . $c->name;
            }
        }
        
        if ($userId = $request->query('user_id')) {
            $query->where('user_id', $userId);

            // Lấy thông tin để hiển thị lại Tag
            $u = User::find($userId);
            if ($u) {
                $contextType = 'user';
                $contextId = $u->id;
                $contextLabel = 'u/' . $u->name;
            }
        }

        // 3. Logic Sắp xếp
        if ($request->query('sort') === 'top') {
            $posts = $query->get()->sortByDesc(function($post) {
                $votes = $post->votes_sum_value ?? 0;
                $comments = $post->comments_count;
                $views = $post->views;
                $hoursAge = $post->created_at->diffInHours(Carbon::now());
                $score = $votes + ($comments * 2) + ($views / 100);
                $gravity = pow(($hoursAge + 2), 1.5);
                return $score / $gravity;
            });

            $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
            $perPage = 10;
            $posts = new \Illuminate\Pagination\LengthAwarePaginator(
                $posts->forPage($page, $perPage),
                $posts->count(),
                $perPage,
                $page,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => $request->query()]
            );
        } else {
            $posts = $query->latest()->paginate(10);
        }
        
        $topCommunities = Community::withCount('posts')->orderByDesc('posts_count')->take(5)->get();

        // Biến phụ cho giao diện tìm kiếm
        $foundCommunities = collect();
        $foundUsers = collect();

        if ($isSearching) {
             $foundCommunities = Community::where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->withCount('posts')->take(4)->get();

            $foundUsers = User::where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->take(4)->get();
        }

        // Truyền thêm các biến context... sang View
        return view('welcome', compact(
            'posts', 'topCommunities', 
            'foundCommunities', 'foundUsers', 'isSearching',
            'contextType', 'contextId', 'contextLabel' 
        ));
    }
}