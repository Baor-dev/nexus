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
    // Form tạo bài viết
    public function create()
    {
        $communities = Community::all();
        return view('posts.create', compact('communities'));
    }

    // Xử lý lưu bài viết
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'community_id' => 'required|exists:communities,id',
            'thumbnail' => 'required|image|max:2048', // Bắt buộc có ảnh bìa, max 2MB
            'content' => 'required'
        ]);

        // Xử lý upload ảnh bìa (Thumbnail)
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            // Lưu vào storage/app/public/thumbnails
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        Post::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . time(), // Thêm time để tránh trùng slug
            'user_id' => auth()->id(),
            'community_id' => $request->community_id,
            'content' => $request->content,
            'thumbnail' => $thumbnailPath,
            'description' => substr(strip_tags($request->content), 0, 150) . '...' // Tự tạo mô tả ngắn từ nội dung
        ]);

        return redirect()->route('dashboard')->with('success', 'Đăng bài thành công!');
    }
    
    // API Upload ảnh cho TinyMCE (kéo thả ảnh vào bài viết)
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('posts_content', 'public');
            return response()->json(['location' => '/storage/' . $path]);
        }
        return response()->json(['error' => 'Upload failed'], 500);
    }
    
    // Xem chi tiết bài viết
    public function show($slug)
    {
        // TỐI ƯU: Load comment cha -> comment con -> user của comment con
        $post = Post::where('slug', $slug)
            ->with(['user', 'community', 'comments' => function($q) {
                $q->whereNull('parent_id') // Chỉ lấy comment gốc
                  ->with(['user', 'replies.user', 'replies.replies']); // Load sâu thêm 2 cấp nữa nếu muốn
            }])
            ->firstOrFail();

        $sessionKey = 'post_viewed_' . $post->id;
        if (!Session::has($sessionKey)) {
            $post->increment('views');
            Session::put($sessionKey, true);
        }

        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        // CHẶN: Nếu không phải tác giả thì báo lỗi 403
        if (auth()->id() !== $post->user_id) {
            abort(403, 'Bạn không có quyền sửa bài viết này');
        }

        $communities = Community::all();
        return view('posts.edit', compact('post', 'communities'));
    }

    // 2. Xử lý cập nhật dữ liệu
    public function update(Request $request, Post $post)
    {
        if (auth()->id() !== $post->user_id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|max:255',
            'community_id' => 'required|exists:communities,id',
            'content' => 'required',
            'thumbnail' => 'nullable|image|max:2048' // Ảnh là tùy chọn khi sửa
        ]);

        // Logic cập nhật ảnh (chỉ upload nếu user chọn ảnh mới)
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
            $post->thumbnail = $thumbnailPath;
        }

        $post->update([
            'title' => $request->title,
            'community_id' => $request->community_id,
            'content' => $request->content,
            // Không đổi slug để giữ SEO, hoặc đổi thì phải xử lý redirect
            'description' => substr(strip_tags($request->content), 0, 150) . '...'
        ]);

        return redirect()->route('posts.show', $post->slug)->with('success', 'Đã cập nhật bài viết!');
    }

    // 3. Xử lý xóa bài viết
    public function destroy(Post $post)
    {
        if (auth()->id() !== $post->user_id) {
            abort(403, 'Bạn không được phép xóa bài này');
        }

        $post->delete();

        return redirect()->route('home')->with('success', 'Đã xóa bài viết.');
    }
}