@extends('layouts.nexus')

@section('content')
<div class="max-w-5xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Cài đặt tài khoản</h1>

    <div class="flex flex-col md:flex-row gap-6">
        
        <!-- SIDEBAR MENU (CỘT TRÁI) -->
        <div class="w-full md:w-1/4">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden sticky top-20">
                <nav class="flex flex-col">
                    <a href="#profile-info" class="px-4 py-3 text-sm font-medium text-nexus-600 bg-blue-50 border-l-4 border-nexus-500 hover:bg-gray-50 transition">
                        Hồ sơ công khai
                    </a>
                    <a href="#password-update" class="px-4 py-3 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 border-l-4 border-transparent transition">
                        Đổi mật khẩu
                    </a>
                    <a href="#delete-account" class="px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50 border-l-4 border-transparent transition">
                        Khu vực nguy hiểm
                    </a>
                </nav>
            </div>
        </div>

        <!-- MAIN CONTENT (CỘT PHẢI) -->
        <div class="w-full md:w-3/4 space-y-6">

            <!-- 1. FORM CẬP NHẬT HỒ SƠ & AVATAR -->
            <div id="profile-info" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Thông tin hồ sơ</h2>
                    <p class="text-sm text-gray-500">Cập nhật thông tin hiển thị và ảnh đại diện của bạn.</p>
                </div>

                <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('patch')

                    <!-- Avatar Upload Section -->
                    <div class="flex items-center gap-6">
                        <div class="relative group">
                            <!-- Preview Ảnh -->
                            <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100 border-2 border-gray-200">
                                @if($user->avatar)
                                    <img id="avatar-preview" src="{{ asset('storage/' . $user->avatar) }}" class="w-full h-full object-cover">
                                @else
                                    <img id="avatar-preview" src="[https://ui-avatars.com/api/?name=](https://ui-avatars.com/api/?name=){{ $user->name }}&background=random&size=128" class="w-full h-full object-cover">
                                @endif
                            </div>
                            
                            <!-- Nút chọn ảnh ảo -->
                            <label for="avatar" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 text-white opacity-0 group-hover:opacity-100 cursor-pointer rounded-full transition text-xs font-bold text-center">
                                Đổi ảnh
                            </label>
                        </div>
                        
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh đại diện</label>
                            <input type="file" id="avatar" name="avatar" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-nexus-600 hover:file:bg-blue-100" onchange="previewImage(event)">
                            <p class="mt-1 text-xs text-gray-500">JPG, GIF hoặc PNG. Tối đa 2MB.</p>
                        </div>
                    </div>

                    <!-- Tên hiển thị -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Tên hiển thị</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-nexus-500 focus:ring-nexus-500 sm:text-sm p-2 border">
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email đăng nhập</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-nexus-500 focus:ring-nexus-500 sm:text-sm p-2 border">
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="bg-nexus-500 text-white px-4 py-2 rounded-md font-bold hover:bg-nexus-600 transition">Lưu thay đổi</button>
                        
                        @if (session('status') === 'profile-updated')
                            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600 font-bold">
                                {{ __('Đã lưu!') }}
                            </p>
                        @endif
                    </div>
                </form>
            </div>

            <!-- 2. FORM ĐỔI MẬT KHẨU -->
            <div id="password-update" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Đổi mật khẩu</h2>
                    <p class="text-sm text-gray-500">Hãy sử dụng mật khẩu mạnh để bảo vệ tài khoản.</p>
                </div>

                <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                    @csrf
                    @method('put')

                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" id="current_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-nexus-500 focus:ring-nexus-500 sm:text-sm p-2 border">
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu mới</label>
                        <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-nexus-500 focus:ring-nexus-500 sm:text-sm p-2 border">
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Nhập lại mật khẩu mới</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-nexus-500 focus:ring-nexus-500 sm:text-sm p-2 border">
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md font-bold hover:bg-gray-700 transition">Cập nhật mật khẩu</button>
                        
                        @if (session('status') === 'password-updated')
                            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600 font-bold">
                                {{ __('Đã lưu!') }}
                            </p>
                        @endif
                    </div>
                </form>
            </div>

            <!-- 3. FORM XÓA TÀI KHOẢN -->
            <div id="delete-account" class="bg-red-50 rounded-lg shadow-sm border border-red-100 p-6">
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-red-700">Xóa tài khoản</h2>
                    <p class="text-sm text-red-600">Hành động này không thể hoàn tác. Tất cả dữ liệu của bạn sẽ bị xóa vĩnh viễn.</p>
                </div>

                <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="bg-red-600 text-white px-4 py-2 rounded-md font-bold hover:bg-red-700 transition">
                    Tôi muốn xóa tài khoản này
                </button>

                <!-- Modal Xác nhận (Dùng component có sẵn của Breeze) -->
                <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                    <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                        @csrf
                        @method('delete')

                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Bạn có chắc chắn muốn xóa tài khoản?') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ __('Sau khi xóa, mọi tài nguyên và dữ liệu sẽ mất vĩnh viễn. Vui lòng nhập mật khẩu để xác nhận.') }}
                        </p>

                        <div class="mt-6">
                            <label for="password" class="sr-only">Password</label>
                            <input id="password" name="password" type="password" class="mt-1 block w-3/4 p-2 border border-gray-300 rounded" placeholder="Mật khẩu của bạn" />
                            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="button" x-on:click="$dispatch('close')" class="bg-gray-200 text-gray-800 px-4 py-2 rounded mr-3">Hủy</button>
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Xóa tài khoản</button>
                        </div>
                    </form>
                </x-modal>
            </div>

        </div>
    </div>
</div>

<!-- Script Preview Ảnh khi chọn -->
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('avatar-preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection