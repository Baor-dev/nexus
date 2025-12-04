<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // 1. Fill dữ liệu cơ bản (Tên, Email)
        $user->fill($request->validated());

        // 2. Nếu thay đổi email thì reset xác thực
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // 3. XỬ LÝ UPLOAD AVATAR (MỚI)
        if ($request->hasFile('avatar')) {
            // Validate ảnh (chỉ cho phép jpg, png..., tối đa 2MB)
            $request->validate([
                'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Xóa ảnh cũ nếu có (để tiết kiệm dung lượng)
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
            }

            // Lưu ảnh mới
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        // 4. Lưu User
        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Hồ sơ đã được cập nhật!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
