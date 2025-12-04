<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    // 1. Chuyển hướng người dùng sang Google/GitHub
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    // 2. Xử lý khi Google/GitHub trả về
    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Đăng nhập thất bại, vui lòng thử lại.');
        }

        // Tìm user trong DB
        $user = User::where('email', $socialUser->getEmail())->first();

        // Nếu chưa có -> Tạo mới
        if (!$user) {
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'email' => $socialUser->getEmail(),
                'password' => null, // Không có pass
                'avatar' => null, // Có thể lưu avatar từ social nếu muốn: $socialUser->getAvatar()
                $provider . '_id' => $socialUser->getId(),
                'role' => 'user',
            ]);
        } else {
            // Nếu đã có -> Cập nhật ID social nếu chưa có
            if (empty($user->{$provider . '_id'})) {
                $user->update([$provider . '_id' => $socialUser->getId()]);
            }
        }

        // Đăng nhập user đó
        Auth::login($user);

        return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
    }
}
