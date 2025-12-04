<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Nexus') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #DAE0E6; }
        .dropdown:hover .dropdown-menu { display: block; }
        [x-cloak] { display: none !important; }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* RESPONSIVE FIXES */
        @media (max-width: 768px) {
            .hidden.md\:block, .md\:block, .md\:flex { display: none !important; }
            .mobile-search-btn, .mobile-menu-btn { display: flex !important; align-items: center; justify-content: center; }
            nav .container { padding-left: 0.5rem; padding-right: 0.5rem; }
            nav img.w-8 { width: 32px; height: 32px; }
        }

        @media (min-width: 769px) {
            .mobile-search-btn, .mobile-menu-btn { display: none !important; }
        }

        .mobile-search-btn, .mobile-menu-btn { display: none; }
        main, main > div { overflow: visible !important; }
        .sticky-sidebar { position: -webkit-sticky !important; position: sticky !important; top: 5rem !important; }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased min-h-screen">

    <nav class="bg-white border-b border-gray-200 fixed w-full top-0 z-50 h-14 flex items-center shadow-sm" x-data>
        <div class="container mx-auto px-4 max-w-7xl flex justify-between items-center h-full">

            <div class="flex items-center gap-3">
                <button @click="$store.mobileMenuOpen = true" 
                            class="lg:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                </button>

                <a href="{{ route('home') }}" class="flex items-center gap-2 mr-4">
                    <img src="{{ asset('images/logo.png') }}" class="w-8 h-8 object-contain" alt="Logo">
                    <span class="font-bold text-xl tracking-tight hidden md:block">Nexus</span>
                </a>
            </div>

            <div class="flex-1 max-w-3xl mx-4 hidden md:block" 
                 x-data="searchComponent()" 
                 @click.away="showDropdown = false">

                <form action="{{ route('home') }}" method="GET" class="relative w-full">
                    <template x-if="context === 'community'">
                        <input type="hidden" name="community_id" :value="contextId">
                    </template>
                    <template x-if="context === 'user'">
                        <input type="hidden" name="user_id" :value="contextId">
                    </template>

                    <div class="relative group">
                        <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-nexus-500 z-10">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>

                        <input type="text" name="search" x-model="query" @input.debounce.300ms="fetchResults" @focus="showDropdown = true"
                               class="w-full bg-gray-100 border border-transparent rounded-full py-2 pl-10 text-sm focus:bg-white focus:border-nexus-500 focus:ring-0 transition shadow-sm placeholder-gray-400"
                               :class="context ? 'pr-[145px]' : 'pr-4'" :placeholder="placeholderText">

                        <div x-show="context" x-cloak 
                             class="absolute right-1.5 top-1/2 -translate-y-1/2 bg-white border border-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full flex items-center gap-1 cursor-pointer hover:bg-gray-50 shadow-sm z-20 max-w-[130px]" 
                             @click="resetContext()">
                            <span class="truncate font-medium" x-text="contextLabel"></span>
                            <span class="font-bold text-gray-400 hover:text-red-500 ml-0.5 text-[10px]">✕</span>
                        </div>
                    </div>

                    <div x-show="showDropdown && (results.length > 0 || isLoading)" x-transition.opacity.duration.200ms
                         class="absolute top-full mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden ring-1 ring-black ring-opacity-5" style="display: none;">
                        <div x-show="isLoading" class="p-4 text-center text-gray-500 text-sm flex justify-center items-center">
                            <svg class="animate-spin h-4 w-4 mr-2 text-nexus-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Đang tìm kiếm...
                        </div>
                        <template x-for="result in results" :key="result.url">
                            <a :href="result.url" class="block px-4 py-2.5 hover:bg-gray-50 transition border-b border-gray-50 last:border-0 group">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0 mr-3">
                                        <div class="text-sm font-semibold text-gray-800 group-hover:text-nexus-600 truncate" x-text="result.text"></div>
                                        <div class="text-xs text-gray-500 truncate" x-text="result.sub_text"></div>
                                    </div>
                                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded flex-shrink-0" 
                                          :class="{'bg-blue-100 text-blue-700': result.type === 'post', 'bg-green-100 text-green-700': result.type === 'community', 'bg-purple-100 text-purple-700': result.type === 'user'}" 
                                          x-text="result.type"></span>
                                </div>
                            </a>
                        </template>
                    </div>
                </form>
            </div>

            <div class="flex items-center gap-2">
                @auth
                    <button @click="$store.mobileSearchOpen = true" class="mobile-search-btn p-2 text-gray-600 hover:bg-gray-100 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>

                    <a href="{{ route('leaderboard') }}" class="flex items-center justify-center p-2 text-gray-500 hover:bg-gray-100 hover:text-nexus-600 rounded-full transition" title="Bảng xếp hạng">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </a>

                    <a href="{{ route('posts.create') }}" class="hidden md:flex items-center gap-1 text-gray-600 hover:bg-gray-100 px-3 py-1.5 rounded-full transition" title="Tạo bài viết">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </a>
                    <a href="{{ route('posts.create') }}" class="md:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </a>

                    <div class="relative" 
                        x-data="{ 
                            open: false, 
                            unreadCount: {{ Auth::user()->unreadNotifications->count() }},
                            init() {
                                setInterval(() => {
                                    fetch('/notifications/check')
                                        .then(res => res.json())
                                        .then(data => {
                                            this.unreadCount = data.count;
                                            const listContent = document.getElementById('notification-list-content');
                                            if (listContent) listContent.innerHTML = data.html;
                                        });
                                }, 5000);
                            }
                        }" 
                        @keydown.escape.window="open = false">

                        <div x-show="open" 
                            @click="open = false"
                            x-transition.opacity
                            class="fixed inset-0 bg-black/20 backdrop-blur-[1px] z-40 md:hidden"></div>

                        <button @click="open = !open" 
                                class="relative p-1.5 md:p-2 text-gray-500 hover:text-nexus-500 transition rounded-full hover:bg-gray-100 z-50">
                            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            
                            <span x-show="unreadCount > 0" 
                                class="absolute top-0.5 right-0.5 md:top-0 md:right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[9px] md:text-[10px] font-bold leading-none text-white bg-red-600 rounded-full border-2 border-white shadow-sm min-w-[14px] h-3.5 md:min-w-[16px] md:h-4"
                                x-text="unreadCount > 99 ? '99+' : unreadCount"
                                style="display: none;">
                            </span>
                        </button>

                        <div x-show="open" 
                            @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                            class="
                                fixed top-14 left-2 right-2 md:absolute md:top-full md:left-auto md:right-0 md:mt-2 
                                bg-white border border-gray-200 rounded-xl shadow-2xl overflow-hidden z-50
                                w-auto md:w-80 md:max-w-sm
                            "
                            style="display: none;">

                            <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50/80 backdrop-blur-sm sticky top-0 z-10">
                                <h3 class="text-sm font-bold text-gray-800">Thông báo</h3>
                                <template x-if="unreadCount > 0">
                                    <a href="{{ route('notifications.readAll') }}" 
                                    class="text-xs font-medium text-nexus-600 hover:text-nexus-700 hover:underline transition-colors">
                                    Đánh dấu đã đọc
                                    </a>
                                </template>
                            </div>

                            <div id="notification-list-content" 
                                class="max-h-[60vh] md:max-h-[400px] overflow-y-auto custom-scrollbar bg-white">
                                @include('layouts.partials.notifications', ['notifications' => Auth::user()->notifications->take(10)])
                            </div>

                            @if(Auth::user()->notifications->count() > 10)
                                <div class="p-2.5 border-t border-gray-100 text-center bg-gray-50 sticky bottom-0 z-10">
                                    <a href="#" class="text-xs md:text-sm text-nexus-600 font-semibold hover:text-nexus-700 transition-colors block w-full h-full">
                                        Xem tất cả
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="relative hidden md:block" x-data="{ open: false }">
                        <button @click="open = !open" 
                                @click.outside="open = false"
                                class="flex items-center gap-3 pl-2 pr-3 py-1.5 rounded-full border border-transparent hover:bg-gray-100 transition-all duration-200 group focus:outline-none focus:ring-2 focus:ring-gray-200">
                            
                            <div class="w-9 h-9 relative">
                                <div class="w-full h-full rounded-full overflow-hidden border border-gray-200 shadow-sm group-hover:shadow-md transition-shadow">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-full h-full object-cover">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=0D8ABC&color=fff" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-green-400 ring-2 ring-white"></span>
                            </div>

                            <div class="flex flex-col items-start text-left">
                                <span class="text-sm font-bold text-gray-700 max-w-[100px] truncate group-hover:text-gray-900 transition-colors">
                                    {{ Auth::user()->name }}
                                </span>
                                
                                <span class="text-[10px] text-gray-500 font-medium -mt-0.5">
                                    {{-- Kiểm tra role: Bạn hãy thay 'role' bằng tên cột thực tế trong DB (ví dụ: is_admin, user_type...) --}}
                                    @if(Auth::user()->role === 'admin')
                                        Admin
                                    @else
                                        Thành viên
                                    @endif
                                </span>
                            </div>

                            <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-transform duration-200" 
                                :class="open ? 'rotate-180' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            class="absolute right-0 mt-2 w-60 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50 ring-1 ring-black ring-opacity-5"
                            style="display: none;">
                            
                            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                                <p class="text-xs text-gray-500 font-medium">Đăng nhập bởi</p>
                                <p class="text-sm font-bold text-gray-900 truncate mt-0.5">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <div class="py-2">
                                @if(Auth::user()->role === 'admin')
                                    <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                                        <span class="font-semibold">Admin Panel</span>
                                    </a>
                                    <div class="h-px bg-gray-100 my-1 mx-4"></div>
                                @endif

                                <a href="/profile" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-nexus-600 transition-colors">
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-nexus-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Hồ sơ cá nhân
                                </a>

                                <a href="{{ route('bookmarks.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-nexus-600 transition-colors">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                                    Bài viết đã lưu
                                </a>
                            </div>

                            <div class="border-t border-gray-100 py-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-red-600 transition-colors text-left">
                                        <svg class="w-5 h-5 text-gray-400 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <button @click="$store.bottomSheetOpen = true" class="md:hidden flex items-center justify-center w-8 h-8 rounded-md overflow-hidden border border-gray-200 ml-2 shadow-sm active:scale-95 transition-transform">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-full h-full object-cover">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=random" class="w-full h-full object-cover">
                        @endif
                    </button>
                @else
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="bg-nexus-500 hover:bg-nexus-600 text-white font-bold py-2 px-6 rounded-full text-sm transition shadow-sm border border-transparent">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="bg-white hover:bg-gray-50 text-nexus-500 font-bold py-2 px-6 rounded-full text-sm transition shadow-sm border border-nexus-500 hidden sm:block">Đăng ký</a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    @include('layouts.partials.sidebar')
    @include('layouts.partials.sidebar-content')

    <div x-data 
         x-show="$store.mobileSearchOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-full opacity-0"
         class="fixed inset-0 bg-white z-[60] flex flex-col"
         style="display: none;">
        
        <div class="p-3 border-b border-gray-100 flex items-center gap-3 shadow-sm bg-white">
            <button @click="$store.mobileSearchOpen = false" class="p-2 text-gray-500 hover:bg-gray-100 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            
            <form action="{{ route('home') }}" method="GET" class="flex-1 relative">
                <div class="relative">
                    <!-- Icon Kính lúp trang trí -->
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    
                    <!-- Input -->
                    <!-- pr-12 để chừa chỗ cho nút Submit -->
                    <input type="text" name="search" 
                           class="w-full bg-gray-100 text-gray-900 border-none rounded-full py-2.5 pl-10 pr-12 text-base focus:ring-2 focus:ring-nexus-500 focus:bg-white transition shadow-inner placeholder-gray-500" 
                           placeholder="Tìm kiếm trên Nexus..." autofocus autocomplete="off">
                    
                    <!-- Nút Submit (Mũi tên) - Nằm đè lên bên phải input -->
                    <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 p-1.5 bg-nexus-500 text-white rounded-full hover:bg-nexus-600 transition shadow-sm flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="flex-1 overflow-y-auto bg-white p-4 flex flex-col items-center justify-center text-gray-500">
            <div class="p-4 bg-gray-50 rounded-full mb-4">
                <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <p>Nhập từ khóa để bắt đầu tìm kiếm...</p>
        </div>
    </div>

    <main class="lg:ml-64 mt-14 min-h-screen bg-gray-100">
        <div class="container mx-auto max-w-7xl px-6 pt-6 pb-12">
            @yield('content')
        </div>
    </main>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('mobileSearchOpen', false);
            Alpine.store('mobileMenuOpen', false);
            Alpine.store('bottomSheetOpen', false);
        });
    </script>

    <!-- Global Scripts -->
    <script>
        // 1. Logic Search
        function searchComponent() {
            return {
                query: '{{ request("search") }}',
                results: [],
                isLoading: false,
                showDropdown: false,
                context: null,
                contextId: null,
                contextLabel: '',
                
                init() {
                    // Case 1: Đang ở trang Chi tiết (Community/User Show) - Logic cũ
                    @if(isset($community) && Route::currentRouteName() == 'communities.show')
                        this.context = 'community'; 
                        this.contextId = {{ $community->id }}; 
                        this.contextLabel = 'c/{{ $community->name }}';
                    @elseif(isset($user) && Route::currentRouteName() == 'users.show')
                        this.context = 'user'; 
                        this.contextId = {{ $user->id }}; 
                        this.contextLabel = 'u/{{ $user->name }}';
                    
                    // Case 2: Đang ở trang Kết quả tìm kiếm (Home có tham số) - LOGIC MỚI
                    @elseif(isset($contextType) && isset($contextId) && isset($contextLabel))
                        this.context = '{{ $contextType }}';
                        this.contextId = {{ $contextId }};
                        this.contextLabel = '{{ $contextLabel }}';
                    @endif
                },

                get placeholderText() {
                    return this.context ? 'Tìm kiếm...' : 'Tìm kiếm trên Nexus...';
                },

                resetContext() {
                    this.context = null;
                    this.contextId = null;
                    this.contextLabel = '';
                    this.searchAction = '{{ route('home') }}'; // Reset về Home
                    if (this.query.length >= 2) {
                        this.fetchResults();
                    }
                },

                fetchResults() {
                    if (this.query.length < 2) { this.results = []; this.showDropdown = false; return; }

                    this.isLoading = true;
                    this.showDropdown = true;

                    // Gọi API
                    let url = `/api/live-search?q=${this.query}`;
                    if (this.context && this.contextId) {
                        url += `&context=${this.context}&context_id=${this.contextId}`;
                    }

                    fetch(url)
                        .then(response => {
                            if (!response.ok) throw new Error('Server error');
                            return response.json();
                        })
                        .then(data => {
                            // Delay 5s giả lập (như bạn yêu cầu trước đó)
                            // Hoặc bỏ setTimeout đi nếu muốn hiện ngay
                            setTimeout(() => {
                                this.results = data.results;
                                this.isLoading = false; 
                            }, 500); // Mình giảm xuống 0.5s cho mượt, 5s hơi lâu quá
                        })
                        .catch(err => {
                            console.error('Search failed:', err);
                            this.isLoading = false;
                        });
                }
            }
        }

        // GLOBAL EVENT LISTENERS
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success')) Swal.fire({ icon: 'success', title: 'Thành công!', text: @json(session('success')), timer: 3000, showConfirmButton: false, confirmButtonColor: '#0ea5e9' }); @endif
            @if(session('error')) Swal.fire({ icon: 'error', title: 'Lỗi!', text: @json(session('error')), confirmButtonColor: '#d33' }); @endif
            
            // Sync Votes (Pageshow)
            window.addEventListener('pageshow', function(event) {
                syncVotesFromStorage();
            });

            // Sync Votes (Storage - Cross Tab)
            window.addEventListener('storage', function(event) {
                if (event.key === 'nexus_post_states') {
                    syncVotesFromStorage();
                }
            });
        });

        // MỚI: Hàm đọc dữ liệu từ LocalStorage và cập nhật toàn bộ bài viết trên trang
        function syncVotesFromStorage() {
            const states = JSON.parse(localStorage.getItem('nexus_post_states') || '{}');
            const scoreElements = document.querySelectorAll('[id^="post-score-"]');
            
            scoreElements.forEach(el => {
                // Tìm tất cả các ID điểm số trên trang (post-score-1, post-score-mobile-1...)
                const match = el.id.match(/-(\d+)$/);
                if (match && match[1]) {
                    const id = match[1];
                    // Nếu bài viết này có dữ liệu mới trong kho -> Cập nhật ngay
                    if (states[id]) {
                        updateVoteUI(id, states[id].score, states[id].status);
                    }
                }
            });
        }

        // MỚI: Hàm xử lý việc tô màu nút và đổi số điểm (được tách ra để tái sử dụng)
        function updateVoteUI(id, newScore, status) {
            // 1. Cập nhật điểm số (Tìm tất cả các chỗ hiển thị điểm của bài này)
            // Selector này tìm các element có id kết thúc bằng "-{id}" (vd: post-score-1, post-score-mobile-1)
            const scoreEls = document.querySelectorAll(`[id$="-${id}"][id^="post-score-"]`);
            
            scoreEls.forEach(el => {
                el.innerText = newScore;
                // Reset màu
                el.classList.remove('text-orange-500', 'text-blue-500', 'text-gray-700', 'dark:text-gray-200');
                
                if (status === 1) el.classList.add('text-orange-500');
                else if (status === -1) el.classList.add('text-blue-500');
                else el.classList.add('text-gray-700', 'dark:text-gray-200');
            });

            // 2. Cập nhật TẤT CẢ nút Upvote của bài này (Desktop + Mobile)
            // Tìm các nút button có chứa onclick="vote('post', id, 1)"
            const upBtns = document.querySelectorAll(`button[onclick*="vote('post', ${id}, 1)"]`);
            
            upBtns.forEach(btn => {
                btn.classList.remove('text-orange-500', 'text-gray-400', 'dark:text-gray-500');
                if (status === 1) {
                    btn.classList.add('text-orange-500');
                } else {
                    btn.classList.add('text-gray-400', 'dark:text-gray-500');
                }
            });

            // 3. Cập nhật TẤT CẢ nút Downvote của bài này
            const downBtns = document.querySelectorAll(`button[onclick*="vote('post', ${id}, -1)"]`);
            
            downBtns.forEach(btn => {
                btn.classList.remove('text-blue-500', 'text-gray-400', 'dark:text-gray-500');
                if (status === -1) {
                    btn.classList.add('text-blue-500');
                } else {
                    btn.classList.add('text-gray-400', 'dark:text-gray-500');
                }
            });
        }

        // 2. SweetAlert & Flash Messages
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success')) Swal.fire({ icon: 'success', title: 'Thành công!', text: @json(session('success')), timer: 3000, showConfirmButton: false, confirmButtonColor: '#0ea5e9' }); @endif
            @if(session('error')) Swal.fire({ icon: 'error', title: 'Lỗi!', text: @json(session('error')), confirmButtonColor: '#d33' }); @endif
        });

        // 3. Hàm Global (Vote, Bookmark, Delete, Report)
        function vote(type, id, value) {
            fetch('/vote', { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, 
                body: JSON.stringify({ votable_type: type, votable_id: id, value: value }) 
            })
            .then(response => {
                if (response.redirected) { window.location.href = '/login'; return; }
                if(response.status === 401) {
                    Swal.fire({ title: 'Bạn chưa đăng nhập!', text: "Vui lòng đăng nhập để vote.", icon: 'warning', showCancelButton: true, confirmButtonColor: '#0ea5e9', confirmButtonText: 'Đăng nhập' }).then((result) => { if (result.isConfirmed) window.location.href = '/login'; });
                    return null;
                }
                return response.json();
            })
            .then(data => {
                if (data) {
                    // 1. Cập nhật giao diện hiện tại
                    updateVoteUI(id, data.new_score, data.status);

                    // 2. MỚI: Lưu trạng thái vào LocalStorage để các trang khác biết
                    let states = JSON.parse(localStorage.getItem('nexus_post_states') || '{}');
                    states[id] = { 
                        score: data.new_score, 
                        status: data.status, 
                        timestamp: Date.now() 
                    };
                    localStorage.setItem('nexus_post_states', JSON.stringify(states));
                }
            });
        }

        function confirmDelete() { Swal.fire({ title: 'Xóa bài viết?', text: "Hành động này không thể hoàn tác!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Xóa ngay' }).then((result) => { if (result.isConfirmed) document.getElementById('delete-form').submit(); }); }
        
        function showReportForm(type, id) {
            Swal.fire({
                title: '<div class="text-2xl font-bold text-gray-900 mb-2">Báo cáo vi phạm</div>',
                html: `
                    <style>
                        .custom-radio {
                            appearance: none;
                            -webkit-appearance: none;
                            width: 20px;
                            height: 20px;
                            border: 2px solid #d1d5db;
                            border-radius: 50%;
                            outline: none;
                            cursor: pointer;
                            position: relative;
                            transition: all 0.2s ease;
                            flex-shrink: 0;
                            margin-top: 2px;
                        }
                        
                        .custom-radio:hover {
                            border-color: #0ea5e9;
                        }
                        
                        .custom-radio:checked {
                            border-color: #0ea5e9;
                            background-color: #fff;
                        }
                        
                        .custom-radio:checked::after {
                            content: '';
                            position: absolute;
                            top: 50%;
                            left: 50%;
                            transform: translate(-50%, -50%);
                            width: 10px;
                            height: 10px;
                            border-radius: 50%;
                            background-color: #0ea5e9;
                        }
                    </style>
                    <div class="text-left space-y-4 mt-6 px-2">
                        <p class="text-sm text-gray-600 mb-4 font-medium">Vui lòng chọn lý do báo cáo nội dung này:</p>
                        
                        <label class="flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-nexus-500 hover:bg-nexus-50 transition-all duration-200 group">
                            <input type="radio" name="report_reason" value="Spam" class="custom-radio" checked>
                            <div class="ml-5 flex-1">
                                <span class="block text-sm font-semibold text-gray-800 group-hover:text-nexus-700">Spam hoặc Quảng cáo</span>
                                <span class="block text-xs text-gray-500 mt-1">Nội dung quảng cáo không mong muốn</span>
                            </div>
                        </label>

                        <label class="flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-nexus-500 hover:bg-nexus-50 transition-all duration-200 group">
                            <input type="radio" name="report_reason" value="Nội dung không phù hợp" class="custom-radio">
                            <div class="ml-5 flex-1">
                                <span class="block text-sm font-semibold text-gray-800 group-hover:text-nexus-700">Nội dung không phù hợp</span>
                                <span class="block text-xs text-gray-500 mt-1">Nội dung nhạy cảm hoặc vi phạm quy định</span>
                            </div>
                        </label>

                        <label class="flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-nexus-500 hover:bg-nexus-50 transition-all duration-200 group">
                            <input type="radio" name="report_reason" value="Quấy rối" class="custom-radio">
                            <div class="ml-5 flex-1">
                                <span class="block text-sm font-semibold text-gray-800 group-hover:text-nexus-700">Quấy rối hoặc Xúc phạm</span>
                                <span class="block text-xs text-gray-500 mt-1">Hành vi bắt nạt hoặc công kích cá nhân</span>
                            </div>
                        </label>

                        <label class="flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-nexus-500 hover:bg-nexus-50 transition-all duration-200 group">
                            <input type="radio" name="report_reason" value="Tin giả" class="w-5 h-5 text-nexus-600 focus:ring-2 focus:ring-nexus-500 border-gray-300 mt-0.5">
                            <div class="ml-3 flex-1">
                                <span class="block text-sm font-semibold text-gray-800 group-hover:text-nexus-700">Thông tin sai lệch</span>
                                <span class="block text-xs text-gray-500 mt-1">Tin giả mạo hoặc thông tin không chính xác</span>
                            </div>
                        </label>
                        
                        <div class="mt-5 pt-4 border-t border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Chi tiết bổ sung (tùy chọn)</label>
                            <textarea 
                                id="report_details" 
                                class="w-full border-2 border-gray-300 rounded-lg shadow-sm focus:border-nexus-500 focus:ring-2 focus:ring-nexus-500 sm:text-sm p-3 transition-all duration-200" 
                                rows="3" 
                                placeholder="Mô tả thêm về vấn đề này để giúp chúng tôi xử lý tốt hơn..."
                            ></textarea>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '<span class="font-semibold">Gửi báo cáo</span>',
                cancelButtonText: 'Hủy bỏ',
                confirmButtonColor: '#0ea5e9',
                cancelButtonColor: '#6b7280',
                reverseButtons: true,
                focusConfirm: false,
                width: '540px',
                padding: '2rem',
                background: '#fff',
                customClass: {
                    popup: 'rounded-2xl shadow-2xl',
                    confirmButton: 'px-6 py-2.5 rounded-lg font-medium',
                    cancelButton: 'px-6 py-2.5 rounded-lg font-medium'
                },
                preConfirm: () => {
                    const reasonRadio = Swal.getPopup().querySelector('input[name="report_reason"]:checked');
                    const details = Swal.getPopup().querySelector('#report_details').value;
                    
                    if (!reasonRadio) {
                        Swal.showValidationMessage('Vui lòng chọn một lý do báo cáo');
                        return false;
                    }
                    
                    const fullReason = reasonRadio.value + (details ? ` - Chi tiết: ${details}` : '');
                    const modelType = type === 'post' ? 'App\\Models\\Post' : (type === 'comment' ? 'App\\Models\\Comment' : 'App\\Models\\User');
                    
                    return fetch('/report', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
                        },
                        body: JSON.stringify({ 
                            reportable_id: id, 
                            reportable_type: modelType, 
                            reason: fullReason 
                        })
                    }).then(response => { 
                        if (!response.ok) throw new Error(response.statusText); 
                        return response.json(); 
                    });
                }
            }).then((result) => { 
                if (result.isConfirmed) { 
                    Swal.fire({ 
                        icon: 'success', 
                        title: '<span class="text-xl font-bold">Đã gửi báo cáo!</span>', 
                        html: '<p class="text-gray-600">Cảm ơn bạn đã đóng góp để giữ cho cộng đồng an toàn và thân thiện.</p>', 
                        confirmButtonColor: '#0ea5e9',
                        confirmButtonText: 'Đóng',
                        timer: 3000,
                        customClass: {
                            popup: 'rounded-2xl'
                        }
                    }); 
                } 
            });
        }

        function bookmark(postId, btn) {
            fetch(`/posts/${postId}/bookmark`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })
            .then(response => {
                if (response.status === 401) {
                    Swal.fire({ title: 'Bạn chưa đăng nhập!', text: "Vui lòng đăng nhập để lưu bài viết.", icon: 'warning', showCancelButton: true, confirmButtonColor: '#0ea5e9', confirmButtonText: 'Đăng nhập' }).then((result) => { if (result.isConfirmed) window.location.href = '/login'; });
                    return null;
                }
                return response.json();
            })
            .then(data => {
                if (data) {
                    const Toast = Swal.mixin({ toast: true, position: 'bottom-end', showConfirmButton: false, timer: 2000, timerProgressBar: true });
                    Toast.fire({ icon: 'success', title: data.message });
                    const icon = btn.querySelector('svg');
                    if (data.bookmarked) { icon.setAttribute('fill', 'currentColor'); btn.classList.add('text-yellow-500'); btn.classList.remove('text-gray-500'); } 
                    else { icon.setAttribute('fill', 'none'); btn.classList.add('text-gray-500'); btn.classList.remove('text-yellow-500'); }
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function submitMainComment() {
            const content = document.getElementById('main-comment-content').value;
            if (!content.trim()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Chưa nhập nội dung',
                    text: 'Vui lòng nhập nội dung bình luận!',
                    confirmButtonColor: '#0ea5e9'
                });
                return;
            }
            document.getElementById('main-comment-form').submit();
        }
    </script>
    
    @stack('scripts')

    <style> [x-cloak] { display: none !important; } </style>

    <div x-data class="relative z-[60]">
        <div x-show="$store.bottomSheetOpen" 
            x-cloak
            x-on:keydown.escape.window="$store.bottomSheetOpen = false"
            x-effect="document.body.style.overflow = $store.bottomSheetOpen ? 'hidden' : ''"
            class="fixed inset-0 flex justify-center items-end md:hidden"
            role="dialog"
            aria-modal="true">
                
            <div x-show="$store.bottomSheetOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 backdrop-blur-none"
                x-transition:enter-end="opacity-100 backdrop-blur-sm"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 backdrop-blur-sm"
                x-transition:leave-end="opacity-0 backdrop-blur-none"
                @click="$store.bottomSheetOpen = false"
                class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"
                aria-hidden="true">
            </div>

            <div x-show="$store.bottomSheetOpen"
                x-transition:enter="transition ease-out duration-300 transform cubic-bezier(0.16, 1, 0.3, 1)"
                x-transition:enter-start="translate-y-full"
                x-transition:enter-end="translate-y-0"
                x-transition:leave="transition ease-in duration-200 transform cubic-bezier(0.16, 1, 0.3, 1)"
                x-transition:leave-start="translate-y-0"
                x-transition:leave-end="translate-y-full"
                @click.stop
                class="w-full bg-white rounded-t-3xl shadow-[0_-8px_30px_rgba(0,0,0,0.12)] relative overflow-hidden flex flex-col max-h-[90vh]">
                
                <div class="flex justify-center pt-4 pb-2 bg-white flex-shrink-0 cursor-grab active:cursor-grabbing" @click="$store.bottomSheetOpen = false">
                    <div class="w-16 h-1.5 bg-gray-300 rounded-full"></div>
                </div>

                <div class="overflow-y-auto overflow-x-hidden flex-1 pb-safe">
                    
                    <div class="px-6 py-5 mx-4 mt-2 mb-4 bg-gray-50 border border-gray-100 rounded-2xl flex items-center gap-4">
                        <div class="relative w-14 h-14 flex-shrink-0">
                            <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-white shadow-sm ring-1 ring-gray-200">
                                @auth
                                    <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random' }}" 
                                        alt="Avatar" 
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                @endauth
                            </div>
                            @auth
                            <span class="absolute bottom-0 right-0 block h-3.5 w-3.5 rounded-full bg-green-500 ring-2 ring-white"></span>
                            @endauth
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-lg text-gray-900 truncate">
                                {{ Auth::check() ? Auth::user()->name : 'Khách' }}
                            </div>
                            @auth
                            <div class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full inline-block mt-1 border border-green-100">
                                Đang hoạt động
                            </div>
                            @endauth
                        </div>
                    </div>

                    <nav class="px-4 space-y-1.5">
                        @auth
                            @if(Auth::user()->role === 'admin')
                                <a href="{{ route('admin.reports') }}" class="flex items-center gap-4 px-4 py-3.5 text-red-600 font-bold bg-red-50 hover:bg-red-100 active:bg-red-200 rounded-xl transition duration-200">
                                    <span class="p-2 bg-white rounded-lg text-red-500 shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"/></svg>
                                    </span>
                                    Admin Dashboard
                                </a>
                            @endif
                            
                            <a href="/profile" class="group flex items-center gap-4 px-4 py-3.5 text-gray-700 font-medium hover:bg-gray-50 active:bg-gray-100 rounded-xl transition duration-200">
                                <span class="p-2 bg-gray-100 rounded-lg text-gray-500 group-hover:bg-nexus-50 group-hover:text-nexus-600 transition shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </span>
                                Hồ sơ cá nhân
                            </a>
                            
                            <a href="{{ route('bookmarks.index') }}" class="group flex items-center gap-4 px-4 py-3.5 text-gray-700 font-medium hover:bg-gray-50 active:bg-gray-100 rounded-xl transition duration-200">
                                <span class="p-2 bg-gray-100 rounded-lg text-gray-500 group-hover:bg-nexus-50 group-hover:text-nexus-600 transition shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                </span>
                                Đã lưu
                            </a>

                            <div class="h-px bg-gray-100 my-3 mx-4"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full group flex items-center gap-4 px-4 py-3.5 text-gray-600 font-medium hover:bg-red-50 hover:text-red-600 active:bg-red-100 rounded-xl transition duration-200">
                                    <span class="p-2 bg-gray-100 rounded-lg text-gray-500 group-hover:bg-white group-hover:text-red-500 transition shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    </span>
                                    Đăng xuất
                                </button>
                            </form>
                        @endauth
                    </nav>
                </div>

                <div class="p-4 bg-white border-t border-gray-50 mt-auto">
                    <button @click="$store.bottomSheetOpen = false" class="w-full py-4 bg-gray-100 active:bg-gray-200 text-gray-800 font-bold rounded-2xl transition shadow-sm text-center">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>