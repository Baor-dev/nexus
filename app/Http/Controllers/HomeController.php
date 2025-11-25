<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user', 'community', 'votes'])
                    ->withCount('comments')
                    ->withSum('votes', 'value');

        // 1. Tìm kiếm (Search)
        if ($search = $request->query('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Scoped Search
        if ($communityId = $request->query('community_id')) { $query->where('community_id', $communityId); }
        if ($userId = $request->query('user_id')) { $query->where('user_id', $userId); }

        // 2. Logic Sắp xếp
        if ($request->query('sort') === 'top') {
            // --- THUẬT TOÁN TRENDING ---
            $posts = $query->get()->sortByDesc(function($post) {
                $votes = $post->votes_sum_value ?? 0;
                $comments = $post->comments_count;
                $views = $post->views;
                
                // Tuổi bài viết (giờ)
                $hoursAge = $post->created_at->diffInHours(Carbon::now());
                
                // Công thức: (Tương tác) / (Thời gian)^1.5
                $score = $votes + ($comments * 2) + ($views / 100);
                $gravity = pow(($hoursAge + 2), 1.5);

                return $score / $gravity;
            });

            // Phân trang thủ công
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

        return view('welcome', compact('posts', 'topCommunities'));
    }
}