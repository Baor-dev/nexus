<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-gray-900">Đặt lại mật khẩu</h2>
        <p class="text-sm text-gray-500 mt-1">Hãy chọn một mật khẩu mới an toàn hơn.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
            <!-- Cập nhật: Sử dụng $request->get('email') thay vì $request->email để tránh lỗi Closure -->
            <input id="email" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-nexus-500 focus:ring-nexus-500 bg-gray-100 text-gray-500 cursor-not-allowed" 
                   type="email" name="email" value="{{ old('email', $request->get('email')) }}" required readonly />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block font-medium text-sm text-gray-700">Mật khẩu mới</label>
            <!-- Cập nhật: Thêm autofocus vào đây để user nhập luôn -->
            <input id="password" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-nexus-500 focus:ring-nexus-500" 
                   type="password" name="password" required autofocus autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Nhập lại mật khẩu mới</label>
            <input id="password_confirmation" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-nexus-500 focus:ring-nexus-500" 
                   type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6 flex items-center justify-end">
            <button class="w-full bg-nexus-500 hover:bg-nexus-600 text-white font-bold py-3 rounded-full transition shadow-sm text-sm uppercase tracking-wide flex justify-center items-center">
                {{ __('Đổi mật khẩu') }}
            </button>
        </div>
    </form>
</x-guest-layout>