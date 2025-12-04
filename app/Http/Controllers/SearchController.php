<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;      // <--- QUAN TRỌNG: Phải có dòng này
use App\Models\Community; // <--- Và dòng này
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Dùng Str::limit an toàn hơn

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q');
        $context = $request->get('context'); 
        $contextId = $request->get('context_id');

        // Chỉ tìm khi từ khóa >= 2 ký tự
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        // 1. TÌM BÀI VIẾT
        $postsQuery = Post::where(function($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        });

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
                'sub_text' => 'trong c/' . ($post->community->name ?? 'N/A')
            ];
        });

        // 2. TÌM CỘNG ĐỒNG (Chỉ tìm khi không đang xem Profile user khác)
        $communities = collect();
        if ($context !== 'user') {
            $communities = Community::where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->take(3)->get()->map(function($community) {
                    return [
                        'type' => 'community',
                        'text' => 'c/' . $community->name,
                        'url' => route('communities.show', $community->slug),
                        'sub_text' => Str::limit($community->description, 30)
                    ];
                });
        }

        // 3. TÌM USER (Chỉ tìm khi không đang trong Community)
        $users = collect();
        if ($context !== 'community') {
            $users = User::where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->take(3)->get()->map(function($user) {
                    return [
                        'type' => 'user',
                        'text' => 'u/' . $user->name,
                        'url' => route('users.show', $user->id),
                        'sub_text' => 'Thành viên'
                    ];
                });
        }

        return response()->json([
            'results' => $communities->merge($users)->merge($posts)
        ]);
    }
}