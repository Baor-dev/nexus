<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-gray-900">Tạo tài khoản mới</h2>
        <p class="text-sm text-gray-500 mt-1">Tham gia cộng đồng Nexus ngay hôm nay.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block font-medium text-sm text-gray-700">Tên hiển thị</label>
            <input id="name" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-nexus-500 focus:ring-nexus-500" 
                   type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
            <input id="email" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-nexus-500 focus:ring-nexus-500" 
                   type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block font-medium text-sm text-gray-700">Mật khẩu</label>
            <input id="password" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-nexus-500 focus:ring-nexus-500" 
                   type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Nhập lại mật khẩu</label>
            <input id="password_confirmation" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-nexus-500 focus:ring-nexus-500" 
                   type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <!-- Đã sửa py-2.5 thành py-3 cho nút dày hơn -->
            <button class="w-full bg-nexus-500 hover:bg-nexus-600 text-white font-bold py-3 rounded-full transition shadow-sm text-sm uppercase tracking-wide flex justify-center items-center">
                {{ __('Đăng ký') }}
            </button>
        </div>

        <!-- Divider -->
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">Hoặc</span>
            </div>
        </div>

        <div class="text-center">
            <p class="text-sm text-gray-600">
                Đã có tài khoản? 
                <a href="{{ route('login') }}" class="font-bold text-nexus-600 hover:text-nexus-500 hover:underline">
                    Đăng nhập
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>