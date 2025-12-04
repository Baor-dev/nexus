<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Notifications\NewComment;
use App\Notifications\NewReply;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|max:1000',
            'post_id' => 'required|exists:posts,id',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        // Tạo bình luận
        $comment = Comment::create([
            'content' => $request->content,
            'user_id' => auth()->id(),
            'post_id' => $request->post_id,
            'parent_id' => $request->parent_id
        ]);

        // Lấy thông tin bài viết
        $post = Post::find($request->post_id);
        $currentUser = auth()->user();

        // --- LOGIC THÔNG BÁO ---

        // 1. Thông báo cho chủ bài viết (Nếu người comment không phải là chủ bài)
        if ($post->user_id !== $currentUser->id) {
            $post->user->notify(new NewComment($currentUser, $post));
        }

        // 2. Thông báo cho người được trả lời (Nếu là Reply)
        if ($request->parent_id) {
            $parentComment = Comment::find($request->parent_id);
            
            // Nếu người trả lời KHÁC người viết comment cha (không tự rep mình)
            // VÀ người viết comment cha KHÁC chủ bài viết (để tránh spam 2 thông báo cùng lúc cho chủ bài)
            if ($parentComment && $parentComment->user_id !== $currentUser->id && $parentComment->user_id !== $post->user_id) {
                $parentComment->user->notify(new NewReply($currentUser, $post));
            }
            // Nếu chủ bài viết chính là người viết comment cha, họ đã nhận thông báo (1) rồi, không cần gửi (2) nữa.
        }

        return back()->with('success', 'Đã gửi bình luận!');
    }

    // HÀM XÓA (Giữ nguyên)
    public function destroy(Comment $comment)
    {
        if (auth()->user()->role !== 'admin' && auth()->id() !== $comment->user_id) {
            abort(403, 'Bạn không có quyền xóa bình luận này.');
        }

        $comment->delete();

        return back()->with('success', 'Đã xóa bình luận.');
    }
}