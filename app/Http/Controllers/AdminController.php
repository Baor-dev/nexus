<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Models\Community;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Danh sách Report & Thống kê
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_posts' => Post::count(),
            'total_comments' => Comment::count(),
            'total_communities' => Community::count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'banned_users' => User::where('is_banned', true)->count(),
        ];

        $postReports = Report::with(['user', 'reportable'])
                         ->where('status', 'pending')
                         ->whereIn('reportable_type', ['App\Models\Post', 'App\Models\Comment'])
                         ->latest()
                         ->paginate(10, ['*'], 'posts_page');

        $userReports = Report::with(['user', 'reportable'])
                         ->where('status', 'pending')
                         ->where('reportable_type', 'App\Models\User')
                         ->latest()
                         ->paginate(10, ['*'], 'users_page');
                          
        return view('admin.reports', compact('postReports', 'userReports', 'stats'));
    }

    // Xử lý Report (ĐÃ CẬP NHẬT LOGIC BAN MẠNH MẼ HƠN)
    public function handle(Request $request, Report $report)
    {
        $action = $request->action; 

        // 1. XÓA NỘI DUNG
        if ($action === 'delete_content') {
            if ($report->reportable) {
                $report->reportable->delete();
                $report->update(['status' => 'resolved']);
                return back()->with('success', 'Đã xóa nội dung vi phạm.');
            }
            return back()->with('error', 'Nội dung này không còn tồn tại (có thể đã bị xóa trước đó).');
        }

        // 2. BAN USER
        if ($action === 'ban_user') {
            $userToBan = null;

            // Trường hợp A: Report trực tiếp User
            if ($report->reportable_type === 'App\Models\User') {
                $userToBan = User::find($report->reportable_id);
            }
            // Trường hợp B: Report Bài viết/Comment -> Tìm tác giả
            elseif ($report->reportable) {
                $userToBan = $report->reportable->user;
                // Xóa luôn nội dung vi phạm sau khi tìm được tác giả
                $report->reportable->delete(); 
            }

            // Thực hiện BAN
            if ($userToBan) {
                // Chặn ban Admin
                if ($userToBan->role === 'admin') {
                    return back()->with('error', 'Không thể ban tài khoản Admin!');
                }

                // Cập nhật trực tiếp vào Database
                $userToBan->is_banned = true;
                $userToBan->save();

                $report->update(['status' => 'resolved']);
                
                // --- Đã sửa dòng thông báo ở đây ---
                return back()->with('success', $userToBan->name . ' đã bị ban vĩnh viễn.');
            } else {
                return back()->with('error', 'Không tìm thấy User để ban (Có thể nội dung gốc đã bị xóa làm mất liên kết).');
            }
        }

        // 3. BỎ QUA
        if ($action === 'dismiss') {
            $report->update(['status' => 'dismissed']);
            return back()->with('success', 'Đã bỏ qua báo cáo.');
        }
    }

    // Hàm xử lý Ban trực tiếp từ nút bấm
    // public function banUser(User $user)
    // {
    //     if ($user->role === 'admin') {
    //         return back()->with('error', 'Không thể ban Admin!');
    //     }

    //     $user->is_banned = true;
    //     $user->save();

    //     // --- Đã sửa dòng thông báo ở đây ---
    //     return back()->with('success', $user->name . ' đã bị ban vĩnh viễn.');
    // }

    // CÁC HÀM QUẢN LÝ MỚI
    public function users()
    {
        $data = User::latest()->paginate(15);
        $type = 'users';
        $title = 'Quản lý Thành viên';
        return view('admin.manage', compact('data', 'type', 'title'));
    }

    public function communities()
    {
        $data = Community::withCount('posts', 'members')->latest()->paginate(15);
        $type = 'communities';
        $title = 'Quản lý Cộng đồng';
        return view('admin.manage', compact('data', 'type', 'title'));
    }

    public function posts()
    {
        $data = Post::with('user', 'community')->withCount('comments', 'votes')->latest()->paginate(15);
        $type = 'posts';
        $title = 'Quản lý Bài viết';
        return view('admin.manage', compact('data', 'type', 'title'));
    }

    public function banned()
    {
        $data = User::where('is_banned', true)->latest()->paginate(15);
        $type = 'banned';
        $title = 'Danh sách Bị khóa';
        return view('admin.manage', compact('data', 'type', 'title'));
    }

    // Hàm xóa chung cho mọi loại
    public function deleteResource($type, $id)
    {
        switch ($type) {
            case 'users':
            case 'banned':
                $user = User::findOrFail($id);
                if ($user->role === 'admin') return back()->with('error', 'Không thể xóa Admin!');
                $user->delete();
                break;
            case 'communities':
                Community::findOrFail($id)->delete();
                break;
            case 'posts':
                Post::findOrFail($id)->delete();
                break;
        }
        return back()->with('success', 'Đã xóa thành công!');
    }

    // Hàm Ban/Unban nhanh
    public function toggleBan(User $user)
    {
        if ($user->role === 'admin') return back()->with('error', 'Không thể tác động lên Admin!');
        
        $user->is_banned = !$user->is_banned;
        $user->save();

        $status = $user->is_banned ? 'đã bị khóa' : 'đã được mở khóa';
        return back()->with('success', "Tài khoản {$user->name} $status.");
    }
}