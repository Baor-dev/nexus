<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-gray-900">Đăng nhập vào Nexus</h2>
        <p class="text-sm text-gray-500 mt-1">Chào mừng bạn quay trở lại!</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
            <input id="email" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-nexus-500 focus:ring-nexus-500" 
                   type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <div class="flex justify-between items-center mb-1">
                <label for="password" class="block font-medium text-sm text-gray-700">Mật khẩu</label>
                @if (Route::has('password.request'))
                    <a class="text-xs text-nexus-600 hover:text-nexus-500 font-bold" href="{{ route('password.request') }}">
                        Quên mật khẩu?
                    </a>
                @endif
            </div>
            
            <input id="password" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-nexus-500 focus:ring-nexus-500" 
                   type="password" name="password" required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-nexus-600 shadow-sm focus:ring-nexus-500" name="remember">
                <span class="ml-2 text-sm text-gray-600">{{ __('Ghi nhớ đăng nhập') }}</span>
            </label>
        </div>

        <div class="mt-6">
            <!-- Đã sửa py-2.5 thành py-3 cho nút dày hơn -->
            <button class="w-full bg-nexus-500 hover:bg-nexus-600 text-white font-bold py-3 rounded-full transition shadow-sm text-sm uppercase tracking-wide flex justify-center items-center">
                {{ __('Đăng nhập') }}
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

        <!-- Link chuyển qua Đăng ký -->
        <div class="text-center">
            <p class="text-sm text-gray-600">
                Chưa có tài khoản? 
                <a href="{{ route('register') }}" class="font-bold text-nexus-600 hover:text-nexus-500 hover:underline">
                    Đăng ký ngay
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>