<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-gray-900">Quên mật khẩu?</h2>
        <p class="text-sm text-gray-500 mt-1">Đừng lo, hãy nhập email và chúng tôi sẽ gửi link khôi phục cho bạn.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-medium text-sm text-gray-700">Email đăng ký</label>
            <input id="email" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-nexus-500 focus:ring-nexus-500" 
                   type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-6 flex items-center justify-end">
            <button class="w-full bg-nexus-500 hover:bg-nexus-600 text-white font-bold py-3 rounded-full transition shadow-sm text-sm uppercase tracking-wide flex justify-center items-center">
                {{ __('Gửi link khôi phục') }}
            </button>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-nexus-600 font-bold flex items-center justify-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Quay lại đăng nhập
            </a>
        </div>
    </form>
</x-guest-layout>