<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra: Đã đăng nhập VÀ bị ban
        // Lưu ý: Đảm bảo model User có trường 'is_banned' (boolean/tinyint)
        if (Auth::check() && Auth::user()->is_banned) {
            
            // 1. Đăng xuất ngay lập tức
            Auth::logout();

            // 2. Hủy session hiện tại và tạo token mới (bảo mật)
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // 3. Đá về trang login kèm thông báo
            return redirect()->route('login')->with('error', 'Tài khoản của bạn đã bị khóa vĩnh viễn do vi phạm tiêu chuẩn cộng đồng.');
        }

        return $next($request);
    }
}