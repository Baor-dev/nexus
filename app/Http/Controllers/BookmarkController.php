<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    // 1. Xử lý Lưu/Bỏ lưu (Toggle)
    public function toggle(Post $post)
    {
        // Hàm toggle() của Laravel sẽ tự động kiểm tra:
        // Nếu chưa có -> Thêm vào
        // Nếu có rồi -> Xóa đi
        $user = auth()->user();
        $user->bookmarks()->toggle($post->id);

        // Kiểm tra trạng thái mới để trả về message phù hợp
        $isBookmarked = $user->bookmarks()->where('post_id', $post->id)->exists();
        $message = $isBookmarked ? 'Đã lưu bài viết' : 'Đã bỏ lưu bài viết';

        return response()->json([
            'bookmarked' => $isBookmarked,
            'message' => $message
        ]);
    }

    // 2. Trang danh sách bài đã lưu
    public function index()
    {
        $user = auth()->user();
        
        // Lấy các bài viết user đã lưu, kèm theo thông tin cần thiết để hiển thị Card
        $posts = $user->bookmarks()
                      ->with(['user', 'community', 'votes'])
                      ->withCount('comments')
                      ->latest('pivot_created_at') // Sắp xếp theo thời gian lưu gần nhất
                      ->paginate(10);

        return view('posts.saved', compact('posts'));
    }
}