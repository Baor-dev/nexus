<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Notifications\NewComment; 
use App\Models\Post;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|max:1000',
            'post_id' => 'required|exists:posts,id',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $comment = Comment::create([
            'content' => $request->content,
            'user_id' => auth()->id(),
            'post_id' => $request->post_id,
            'parent_id' => $request->parent_id
        ]);

        // --- LOGIC THÔNG BÁO (MỚI) ---
        $post = Post::find($request->post_id);
        
        // Nếu bình luận vào bài viết của người khác -> Thông báo cho tác giả bài viết
        if ($post->user_id !== auth()->id()) {
            $post->user->notify(new NewComment(auth()->user(), $post));
        }

        // (Nâng cao: Nếu trả lời bình luận -> Thông báo cho chủ comment cha)
        // Phần này bạn có thể tự làm thêm sau.
        // -----------------------------

        return back()->with('success', 'Đã gửi bình luận!');
    }

    // THÊM HÀM XÓA
    public function destroy(Comment $comment)
    {
        // Chỉ cho phép xóa nếu là Admin HOẶC là người viết comment đó
        if (auth()->user()->role !== 'admin' && auth()->id() !== $comment->user_id) {
            abort(403, 'Bạn không có quyền xóa bình luận này.');
        }

        $comment->delete();

        return back()->with('success', 'Đã xóa bình luận.');
    }
}