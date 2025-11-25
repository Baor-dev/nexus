<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Models\Community; // Nhớ import Community
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Danh sách các báo cáo đang chờ (Pending) & Thống kê
    public function index()
    {
        // 1. THỐNG KÊ TỔNG QUAN (MỚI THÊM)
        $stats = [
            'total_users' => User::count(),
            'total_posts' => Post::count(),
            'total_comments' => Comment::count(),
            'total_communities' => Community::count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'banned_users' => User::where('is_banned', true)->count(),
        ];

        // 2. Lấy danh sách Report Bài viết/Comment
        $postReports = Report::with(['user', 'reportable'])
                         ->where('status', 'pending')
                         ->whereIn('reportable_type', ['App\Models\Post', 'App\Models\Comment'])
                         ->latest()
                         ->paginate(10, ['*'], 'posts_page');

        // 3. Lấy danh sách Report User
        $userReports = Report::with(['user', 'reportable'])
                         ->where('status', 'pending')
                         ->where('reportable_type', 'App\Models\User')
                         ->latest()
                         ->paginate(10, ['*'], 'users_page');
                         
        return view('admin.reports', compact('postReports', 'userReports', 'stats'));
    }

    // Xử lý báo cáo (Giữ nguyên logic cũ)
    public function handle(Request $request, Report $report)
    {
        $action = $request->action; 

        if ($action === 'delete_content') {
            if ($report->reportable) {
                $report->reportable->delete();
            }
            $report->update(['status' => 'resolved']);
            return back()->with('success', 'Đã xóa nội dung vi phạm.');
        }

        if ($action === 'ban_user') {
            if ($report->reportable && $report->reportable->user) {
                // Case 1: Report bài viết/comment -> Ban tác giả
                $userToBan = $report->reportable->user;
                $userToBan->update(['is_banned' => true]);
                $report->reportable->delete(); // Xóa luôn nội dung
            } elseif ($report->reportable_type === 'App\Models\User') {
                // Case 2: Report trực tiếp user -> Ban user đó
                $userToBan = User::find($report->reportable_id);
                if($userToBan) $userToBan->update(['is_banned' => true]);
            }
            
            $report->update(['status' => 'resolved']);
            return back()->with('success', 'Đã BAN người dùng vi phạm.');
        }

        if ($action === 'dismiss') {
            $report->update(['status' => 'dismissed']);
            return back()->with('success', 'Đã bỏ qua báo cáo.');
        }
    }
}