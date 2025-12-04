@extends('layouts.nexus')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Cài đặt tài khoản</h1>
        <p class="text-gray-500 mt-2">Quản lý thông tin cá nhân, bảo mật và tùy chọn riêng tư của bạn.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- LEFT SIDEBAR NAVIGATION -->
        <div class="lg:col-span-3">
            <nav class="space-y-1 sticky top-24">
                <a href="#profile-info" 
                   class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg bg-white text-nexus-600 shadow-sm border border-gray-200 hover:bg-gray-50 transition-all duration-200 ring-1 ring-black/5">
                    <svg class="flex-shrink-0 -ml-1 mr-3 h-6 w-6 text-nexus-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span class="truncate">Hồ sơ công khai</span>
                </a>

                <a href="#security" 
                   class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-600 hover:bg-white hover:text-gray-900 hover:shadow-sm transition-all duration-200">
                    <svg class="flex-shrink-0 -ml-1 mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span class="truncate">Bảo mật & Mật khẩu</span>
                </a>

                <a href="#danger-zone" 
                   class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-red-600 hover:bg-red-50 transition-all duration-200">
                    <svg class="flex-shrink-0 -ml-1 mr-3 h-6 w-6 text-red-500 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    <span class="truncate">Khu vực nguy hiểm</span>
                </a>
            </nav>
        </div>

        <!-- RIGHT CONTENT -->
        <div class="lg:col-span-9 space-y-8">

            <!-- 1. CARD: THÔNG TIN HỒ SƠ -->
            <div id="profile-info" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Thông tin hồ sơ</h2>
                        <p class="text-sm text-gray-500 mt-1">Thông tin này sẽ hiển thị công khai trên trang cá nhân của bạn.</p>
                    </div>
                </div>

                <div class="p-6">
                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

                    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('patch')

                        <!-- Avatar Upload -->
                        <div class="flex items-center gap-6">
                            <div class="relative group w-24 h-24 flex-shrink-0">
                                <div class="w-full h-full rounded-full overflow-hidden bg-gray-100 border-4 border-white shadow-md">
                                    @if($user->avatar)
                                        <img id="avatar-preview" src="{{ asset('storage/' . $user->avatar) }}" class="w-full h-full object-cover">
                                    @else
                                        <img id="avatar-preview" src="https://ui-avatars.com/api/?name={{ $user->name }}&background=random&size=128" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <label for="avatar" class="absolute inset-0 flex items-center justify-center bg-black/40 text-white opacity-0 group-hover:opacity-100 cursor-pointer rounded-full transition duration-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                                </label>
                                <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*" onchange="previewImage(event)">
                            </div>
                            
                            <div class="flex-1">
                                <h3 class="text-sm font-bold text-gray-900">Ảnh đại diện</h3>
                                <p class="text-xs text-gray-500 mt-1 mb-3">Hỗ trợ JPG, PNG hoặc GIF. Tối đa 2MB.</p>
                                <label for="avatar" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 cursor-pointer">
                                    Tải ảnh mới
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tên hiển thị -->
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Tên hiển thị</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required 
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-nexus-500 focus:ring-nexus-500 transition-colors">
                                <x-input-error class="mt-1" :messages="$errors->get('name')" />
                            </div>

                            <!-- Email (Readonly) -->
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email đăng nhập</label>
                                <div class="relative">
                                    <input type="email" id="email" value="{{ $user->email }}" readonly 
                                           class="w-full rounded-lg border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed shadow-sm pr-10">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    </div>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1">Không thể thay đổi email để bảo vệ tài khoản.</p>
                            </div>
                        </div>

                        <!-- Cảnh báo xác thực -->
                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 flex items-start gap-3">
                                <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <div>
                                    <h4 class="text-sm font-bold text-amber-800">Email chưa được xác minh</h4>
                                    <p class="text-sm text-amber-700 mt-1">Vui lòng kiểm tra hộp thư đến (hoặc mục Spam) để kích hoạt tài khoản và nhận thông báo.</p>
                                    <button form="send-verification" class="mt-2 text-sm font-bold text-amber-900 underline hover:text-amber-700 transition">Gửi lại email xác minh</button>
                                    @if (session('status') === 'verification-link-sent')
                                        <div class="mt-2 text-sm font-bold text-green-600 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Đã gửi link mới!
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="pt-4 flex items-center gap-4 border-t border-gray-100">
                            <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-bold rounded-full text-white bg-nexus-600 hover:bg-nexus-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-nexus-500 transition-transform active:scale-95">
                                Lưu thay đổi
                            </button>
                            
                            @if (session('status') === 'profile-updated')
                                <span x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600 font-bold flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Đã cập nhật thành công!
                                </span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- 2. CARD: BẢO MẬT (LOGIC MỚI) -->
            <div id="security" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden scroll-mt-24">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-lg font-bold text-gray-900">Bảo mật & Mật khẩu</h2>
                    <p class="text-sm text-gray-500 mt-1">Quản lý cách bạn đăng nhập vào Nexus.</p>
                </div>

                <div class="p-6">
                    <!-- LOGIC KIỂM TRA MẬT KHẨU -->
                    @if($user->password)
                        <!-- FORM ĐỔI MẬT KHẨU (Cho user thường) -->
                        <form method="post" action="{{ route('password.update') }}" class="space-y-6 max-w-xl">
                            @csrf
                            @method('put')
                            
                            <div>
                                <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-1">Mật khẩu hiện tại</label>
                                <input type="password" name="current_password" id="current_password" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-nexus-500 focus:ring-nexus-500">
                                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Mật khẩu mới</label>
                                    <input type="password" name="password" id="password" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-nexus-500 focus:ring-nexus-500">
                                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Nhập lại mật khẩu mới</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-nexus-500 focus:ring-nexus-500">
                                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex items-center gap-4 pt-2">
                                <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-bold rounded-full text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-transform active:scale-95">
                                    Cập nhật mật khẩu
                                </button>
                                @if (session('status') === 'password-updated')
                                    <span x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600 font-bold">Đã lưu!</span>
                                @endif
                            </div>
                        </form>
                    @else
                        <!-- THÔNG BÁO CHO USER SOCIAL LOGIN (Không hiện form) -->
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 flex items-start gap-4">
                            <div class="bg-blue-100 p-3 rounded-full flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">Bảo mật bởi bên thứ ba</h3>
                                <p class="text-sm text-gray-600 leading-relaxed mb-3">
                                    Tài khoản này được đăng nhập thông qua <strong>Google</strong> hoặc <strong>GitHub</strong>. Bạn không cần (và không thể) đổi mật khẩu tại đây.
                                </p>
                                <div class="flex items-center gap-2 text-xs text-blue-700 font-medium">
                                    <span class="flex items-center gap-1 bg-blue-100 px-2 py-1 rounded">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                                        Đăng nhập an toàn
                                    </span>
                                    <span>Để đổi mật khẩu, vui lòng truy cập cài đặt tài khoản Google/GitHub của bạn.</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 3. CARD: DANGER ZONE -->
            <div id="delete-account" class="bg-white rounded-xl shadow-sm border border-red-200 overflow-hidden scroll-mt-24">
                <div class="p-6 border-b border-red-100 bg-red-50">
                    <h2 class="text-lg font-bold text-red-700 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Khu vực nguy hiểm
                    </h2>
                </div>

                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">
                        Khi bạn xóa tài khoản, tất cả bài viết, bình luận và dữ liệu liên quan sẽ bị xóa vĩnh viễn. Hành động này <strong>không thể hoàn tác</strong>.
                    </p>

                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent font-bold rounded-full text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        Tôi muốn xóa tài khoản này
                    </button>

                    <!-- Modal Xác nhận -->
                    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                            @csrf
                            @method('delete')

                            <h2 class="text-lg font-bold text-gray-900 mb-2">
                                {{ __('Bạn có chắc chắn muốn xóa tài khoản?') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-500 mb-6">
                                {{ __('Vui lòng nhập mật khẩu của bạn để xác nhận hành động này.') }}
                            </p>

                            @if($user->password)
                                <div class="mt-4">
                                    <label for="password" class="sr-only">Password</label>
                                    <input id="password" name="password" type="password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Nhập mật khẩu để xác nhận..." />
                                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                                </div>
                            @else
                                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                                    <p class="text-sm text-yellow-700">
                                        Vì bạn đăng nhập bằng Mạng xã hội, bạn không cần nhập mật khẩu. Hãy bấm nút xóa bên dưới để xác nhận.
                                    </p>
                                </div>
                            @endif

                            <div class="mt-6 flex justify-end gap-3">
                                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-50">
                                    Giữ lại tài khoản
                                </button>
                                <button type="submit" class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700">
                                    Xóa vĩnh viễn
                                </button>
                            </div>
                        </form>
                    </x-modal>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('avatar-preview');
            output.src = reader.result;
        };
        if(event.target.files[0]) reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection