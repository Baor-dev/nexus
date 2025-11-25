<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function vote(Request $request)
    {
        $request->validate([
            'votable_id' => 'required|integer',
            'votable_type' => 'required|in:post,comment',
            'value' => 'required|in:1,-1' 
        ]);

        $user = auth()->user();
        
        $modelClass = $request->votable_type === 'post' ? Post::class : Comment::class;
        $model = $modelClass::findOrFail($request->votable_id);

        $existingVote = Vote::where('user_id', $user->id)
                            ->where('votable_id', $request->votable_id)
                            ->where('votable_type', $modelClass)
                            ->first();

        $status = 0; // 0: Không vote, 1: Upvote, -1: Downvote

        if ($existingVote) {
            if ($existingVote->value == $request->value) {
                // Toggle off: Nếu bấm lại nút cũ -> Xóa vote
                $existingVote->delete();
                $status = 0; 
            } else {
                // Đổi ý: Update giá trị mới
                $existingVote->update(['value' => $request->value]);
                $status = $request->value;
            }
        } else {
            // Tạo mới
            Vote::create([
                'user_id' => $user->id,
                'votable_id' => $request->votable_id,
                'votable_type' => $modelClass,
                'value' => $request->value
            ]);
            $status = $request->value;
        }

        $newScore = $model->votes()->sum('value');

        // Trả về cả điểm số mới VÀ trạng thái để JS đổi màu
        return response()->json([
            'new_score' => $newScore,
            'status' => $status 
        ]);
    }
}