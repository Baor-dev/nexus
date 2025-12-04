<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Notifications\NewComment;
use Illuminate\Support\Facades\Session;

class PostController extends Controller
{
    // 1. Form tạo bài viết
    public function create(Request $request)
    {
        $communities = Community::all();
        $selectedCommunity = $request->query('community_id'); 
        return view('posts.create', compact('communities', 'selectedCommunity'));
    }

    // 2. Xử lý lưu bài viết mới (CẬP NHẬT)
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'community_id' => 'required|exists:communities,id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', 
            'content' => 'required',
            'description' => 'nullable|string|max:500',
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        // Sử dụng mô tả người dùng nhập, nếu không có thì tự động cắt từ content
        $description = $request->description 
            ? $request->description 
            : substr(strip_tags($request->content), 0, 150) . '...';

        Post::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . time(),
            'user_id' => auth()->id(),
            'community_id' => $request->community_id,
            'content' => $request->content,
            'thumbnail' => $thumbnailPath,
            'description' => $description,
            'views' => 0
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Đăng bài thành công!',
                'redirect_url' => route('home')
            ]);
        }

        return redirect()->route('home')->with('success', 'Đăng bài thành công!');
    }
    
    // 3. API Upload ảnh cho TinyMCE
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('posts_content', 'public');
            return response()->json(['location' => '/storage/' . $path]);
        }
        return response()->json(['error' => 'Upload failed'], 500);
    }
    
    // 4. Xem chi tiết bài viết
    public function show($slug)
    {
        $post = Post::where('slug', $slug)
            ->with(['user', 'community', 'comments' => function($q) {
                $q->whereNull('parent_id')
                  ->with(['user', 'replies.user', 'replies.replies']);
            }])
            ->firstOrFail();

        $sessionKey = 'post_viewed_' . $post->id;
        if (!Session::has($sessionKey)) {
            $post->increment('views');
            Session::put($sessionKey, true);
        }

        return view('posts.show', compact('post'));
    }

    // 5. Hiển thị form chỉnh sửa
    public function edit(Post $post)
    {
        if (auth()->id() !== $post->user_id) {
            abort(403, 'Bạn không có quyền sửa bài viết này');
        }
        $communities = Community::all();
        return view('posts.edit', compact('post', 'communities'));
    }

    // 6. Xử lý cập nhật bài viết (CẬP NHẬT)
    public function update(Request $request, Post $post)
    {
        if (auth()->id() !== $post->user_id) abort(403);

        $request->validate([
            'title' => 'required|max:255',
            'community_id' => 'required|exists:communities,id',
            'content' => 'required',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // Max 10MB
            'description' => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
            $post->thumbnail = $thumbnailPath;
        }

        // Logic cập nhật mô tả tương tự
        $description = $request->description 
            ? $request->description 
            : substr(strip_tags($request->content), 0, 150) . '...';

        $post->update([
            'title' => $request->title,
            'community_id' => $request->community_id,
            'content' => $request->content,
            'description' => $description
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Đã cập nhật bài viết!',
                'redirect_url' => route('posts.show', $post->slug)
            ]);
        }

        return redirect()->route('posts.show', $post->slug)->with('success', 'Đã cập nhật bài viết!');
    }

    // 7. Xử lý xóa bài viết
    public function destroy(Post $post)
    {
        if (auth()->id() !== $post->user_id) abort(403);
        $post->delete();
        return redirect()->route('home')->with('success', 'Đã xóa bài viết.');
    }
}