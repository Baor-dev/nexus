<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Community;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    // API cho Live Search (Trả về JSON)
    public function search(Request $request)
    {
        $query = $request->get('q');
        $context = $request->get('context'); // 'community', 'user', hoặc null
        $contextId = $request->get('context_id');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = [];

        // 1. TÌM BÀI VIẾT (POSTS)
        $postsQuery = Post::where(function($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        });

        // Áp dụng Scope nếu có (Logic cho yêu cầu số 2)
        if ($context === 'community' && $contextId) {
            $postsQuery->where('community_id', $contextId);
        } elseif ($context === 'user' && $contextId) {
            $postsQuery->where('user_id', $contextId);
        }

        $posts = $postsQuery->take(5)->get()->map(function($post) {
            return [
                'type' => 'post',
                'text' => $post->title,
                'url' => route('posts.show', $post->slug),
                'sub_text' => 'trong c/' . $post->community->name
            ];
        });

        // 2. TÌM CỘNG ĐỒNG (Chỉ tìm nếu không đang trong context User)
        $communities = collect();
        if ($context !== 'user') {
            $communities = Community::where('name', 'like', "%{$query}%")
                ->take(3)->get()->map(function($community) {
                    return [
                        'type' => 'community',
                        'text' => 'c/' . $community->name,
                        'url' => route('communities.show', $community->slug),
                        'sub_text' => 'Cộng đồng'
                    ];
                });
        }

        // 3. TÌM USER (Chỉ tìm nếu không đang trong context Community)
        $users = collect();
        if ($context !== 'community') {
            $users = User::where('name', 'like', "%{$query}%")
                ->take(3)->get()->map(function($user) {
                    return [
                        'type' => 'user',
                        'text' => 'u/' . $user->name,
                        'url' => route('users.show', $user->id),
                        'sub_text' => 'Người dùng'
                    ];
                });
        }

        // Gộp kết quả: Post trước, rồi đến Community/User
        return response()->json([
            'results' => $posts->merge($communities)->merge($users)
        ]);
    }
}