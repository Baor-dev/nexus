<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Nexus') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts & Styles -->
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
        @media (max-width: 1024px) {
            .max-w-3xl { max-width: 500px !important; }
        }

        @media (max-width: 768px) {
            /* Ẩn các phần chỉ dành cho desktop */
            .hidden.md\\:block,
            .md\\:block,
            .md\\:flex { 
                display: none !important; 
            }

            /* HIỆN nút search + hamburger trên mobile */
            .mobile-search-btn,
            .mobile-menu-btn { 
                display: flex !important; 
                align-items: center;
                justify-content: center;
            }

            /* Thu gọn padding navbar */
            nav .container {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            nav img.w-8 {
                width: 32px;
                height: 32px;
            }
        }

        /* IMPORTANT: Hide hamburger on desktop (md and above) */
        @media (min-width: 769px) {
            .mobile-search-btn,
            .mobile-menu-btn {
                display: none !important;
            }
        }

        /* Show mobile buttons only on mobile */
        .mobile-search-btn,
        .mobile-menu-btn {
            display: none;
        }

        @media (max-width: 768px) {
            .mobile-search-btn,
            .mobile-menu-btn {
                display: flex !important;
            }
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased min-h-screen">

    <!-- NAVBAR -->
    <nav class="bg-white border-b border-gray-200 fixed w-full top-0 z-50 h-14 flex items-center shadow-sm" x-data>
        <div class="container mx-auto px-4 max-w-7xl flex justify-between items-center h-full">

            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 mr-6 flex-shrink-0">
                <img src="{{ asset('images/logo.png') }}" class="w-8 h-8 object-contain" alt="Nexus Logo">
                <span class="font-bold text-xl tracking-tight hidden md:block">Nexus</span>
            </a>

            <!-- SEARCH BAR (Chỉ hiện từ md trở lên) -->
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

                        <input type="text" 
                               name="search" 
                               x-model="query"
                               @input.debounce.300ms="fetchResults"
                               @focus="showDropdown = true"
                               class="w-full bg-gray-100 border border-transparent rounded-full py-2 pl-10 text-sm focus:bg-white focus:border-nexus-500 focus:ring-0 transition shadow-sm placeholder-gray-400"
                               :class="context ? 'pr-[145px]' : 'pr-4'" 
                               :placeholder="placeholderText">

                        <div x-show="context" x-cloak 
                             class="absolute right-1.5 top-1/2 -translate-y-1/2 bg-white border border-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full flex items-center gap-1 cursor-pointer hover:bg-gray-50 shadow-sm z-20 max-w-[130px]" 
                             @click="resetContext()">
                            <span class="truncate font-medium" x-text="contextLabel"></span>
                            <span class="font-bold text-gray-400 hover:text-red-500 ml-0.5 text-[10px]">×</span>
                        </div>
                    </div>

                    <!-- Dropdown Results -->
                    <div x-show="showDropdown && (results.length > 0 || isLoading)" 
                         x-transition.opacity.duration.200ms
                         class="absolute top-full mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden ring-1 ring-black ring-opacity-5"
                         style="display: none;">
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

            <!-- Right Actions -->
            <div class="flex items-center gap-2">

                @auth
                    <!-- Mobile: Icon Search -->
                    <button @click="$store.mobileSearchOpen = true" 
                            class="mobile-search-btn p-2 text-gray-600 hover:bg-gray-100 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>

                    <!-- Tạo bài viết -->
                    <a href="{{ route('posts.create') }}" class="hidden md:flex items-center gap-1 text-gray-600 hover:bg-gray-100 px-3 py-1.5 rounded-full transition" title="Tạo bài viết">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </a>
                    <a href="{{ route('posts.create') }}" class="md:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </a>

                    <!-- Thông báo -->
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
                        @click.outside="open = false">

                        <button @click="open = !open" 
                                class="relative p-2 text-gray-500 hover:text-nexus-500 transition rounded-full hover:bg-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span x-show="unreadCount > 0" 
                                class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-red-600 rounded-full border-2 border-white shadow-sm min-w-[16px] h-4"
                                x-text="unreadCount > 99 ? '99+' : unreadCount"
                                style="display: none;">
                            </span>
                        </button>

                        <!-- Dropdown Thông báo - Responsive 100% -->
                        <div x-show="open" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute top-full mt-2 right-0 w-screen max-w-sm bg-white border border-gray-200 rounded-xl shadow-2xl z-50 overflow-hidden"
                            :class="{ 'left-0': $el.offsetWidth + $el.getBoundingClientRect().right > window.innerWidth }"
                            style="display: none; max-height: 80vh;">

                            <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50 sticky top-0 z-10">
                                <h3 class="text-sm font-bold text-gray-800">Thông báo</h3>
                                <template x-if="unreadCount > 0">
                                    <a href="{{ route('notifications.readAll') }}" 
                                    class="text-xs font-medium text-nexus-600 hover:underline">Đánh dấu đã đọc hết</a>
                                </template>
                            </div>

                            <div id="notification-list-content" 
                                class="max-h-[calc(80vh-60px)] overflow-y-auto custom-scrollbar">
                                @include('layouts.partials.notifications', ['notifications' => Auth::user()->notifications->take(10)])
                            </div>

                            @if(Auth::user()->notifications->count() > 10)
                                <div class="p-3 border-t border-gray-100 text-center">
                                    <a href="{{ route('notifications.index') }}" class="text-sm text-nexus-600 font-medium hover:underline">
                                        Xem tất cả thông báo
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- User Dropdown (Desktop) -->
                    <div class="relative dropdown group hidden md:block">
                        <button class="flex items-center gap-2 border border-transparent hover:border-gray-200 rounded px-2 py-1 transition">
                            <div class="w-8 h-8 bg-gray-200 rounded-md overflow-hidden">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-full h-full object-cover">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=random" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <span class="text-sm font-medium max-w-[100px] truncate">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="dropdown-menu absolute right-0 mt-0 w-48 bg-white border border-gray-200 rounded-md shadow-lg hidden group-hover:block pt-1 z-50">
                            <div class="py-1">
                                @if(Auth::user()->role === 'admin')
                                    <a href="{{ route('admin.reports') }}" class="block px-4 py-2 text-sm text-red-600 font-bold hover:bg-red-50 border-b border-gray-100">Admin Panel</a>
                                @endif
                                <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Hồ sơ của tôi</a>
                                <a href="{{ route('bookmarks.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Đã lưu</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Đăng xuất</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile: Hamburger Menu -->
                    <button @click="$store.mobileMenuOpen = !$store.mobileMenuOpen" 
                            class="mobile-menu-btn p-2 text-gray-600 hover:bg-gray-100 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                @else
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="bg-nexus-500 hover:bg-nexus-600 text-white font-bold py-2 px-6 rounded-full text-sm transition shadow-sm">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="bg-white hover:bg-gray-50 text-nexus-500 font-bold py-2 px-6 rounded-full text-sm transition shadow-sm border border-nexus-500 hidden sm:block">Đăng ký</a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Mobile Search Fullscreen -->
    <div x-data 
         x-show="$store.mobileSearchOpen" 
         x-transition 
         class="fixed inset-0 bg-white z-[60] flex flex-col"
         style="display: none;">
        <div class="p-4 border-b flex items-center gap-3">
            <button @click="$store.mobileSearchOpen = false">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <form action="{{ route('home') }}" method="GET" class="flex-1">
                <input type="text" name="search" placeholder="Tìm kiếm trên Nexus..." 
                       class="w-full text-lg outline-none" autofocus autocomplete="off">
            </form>
        </div>
        <div class="flex-1 overflow-y-auto p-4 text-gray-500 text-center pt-20">
            Nhập từ khóa để tìm kiếm...
        </div>
    </div>

    <!-- Mobile Menu Sidebar -->
    <div x-data 
         x-show="$store.mobileMenuOpen" 
         x-transition
         class="fixed inset-0 bg-black bg-opacity-50 z-[60]" 
         @click="$store.mobileMenuOpen = false"
         style="display: none;">
        <div @click.stop class="absolute right-0 top-0 h-full w-80 max-w-full bg-white shadow-2xl">
            <div class="p-4 border-b flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-gray-300">
                        @if(Auth::check() && Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-full h-full object-cover">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ Auth::check() ? Auth::user()->name : 'User' }}&background=random" class="w-full h-full">
                        @endif
                    </div>
                    <div>
                        <div class="font-bold text-lg">{{ Auth::check() ? Auth::user()->name : 'Khách' }}</div>
                    </div>
                </div>
                <button @click="$store.mobileMenuOpen = false">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="py-2">
                @if(Auth::check() && Auth::user()->role === 'admin')
                    <a href="{{ route('admin.reports') }}" class="block px-6 py-4 hover:bg-red-50 text-red-600 font-medium">Admin Panel</a>
                @endif
                <a href="/profile" class="block px-6 py-4 hover:bg-gray-100 text-base">Hồ sơ của tôi</a>
                <a href="{{ route('bookmarks.index') }}" class="block px-6 py-4 hover:bg-gray-100 text-base">Đã lưu</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left px-6 py-4 hover:bg-gray-100 text-red-600 font-medium">Đăng xuất</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto max-w-7xl px-0 md:px-4 mt-14 pt-6 pb-12">
        @yield('content')
    </main>

    <!-- Alpine Store - MUST BE BEFORE OTHER SCRIPTS -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('mobileSearchOpen', false);
            Alpine.store('mobileMenuOpen', false);
        });
    </script>

    <!-- Search Component -->
    <script>
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
                    @if(isset($community) && Route::currentRouteName() == 'communities.show')
                        this.context = 'community'; 
                        this.contextId = {{ $community->id }}; 
                        this.contextLabel = 'c/{{ $community->name }}';
                    @elseif(isset($user) && Route::currentRouteName() == 'users.show')
                        this.context = 'user'; 
                        this.contextId = {{ $user->id }}; 
                        this.contextLabel = 'u/{{ $user->name }}';
                    @endif
                },
                get placeholderText() { 
                    return this.context ? 'Tìm kiếm...' : 'Tìm kiếm trên Nexus...'; 
                },
                resetContext() { 
                    this.context = null; 
                    this.contextId = null; 
                    this.contextLabel = ''; 
                },
                fetchResults() {
                    if (this.query.length < 2) { 
                        this.results = []; 
                        this.showDropdown = false; 
                        return; 
                    }
                    this.isLoading = true; 
                    this.showDropdown = true;
                    let url = `/api/live-search?q=${this.query}`;
                    if (this.context && this.contextId) {
                        url += `&context=${this.context}&context_id=${this.contextId}`;
                    }
                    fetch(url)
                        .then(res => res.json())
                        .then(data => { 
                            this.results = data.results; 
                            this.isLoading = false; 
                        })
                        .catch(err => {
                            console.error('Search error:', err);
                            this.isLoading = false;
                        });
                }
            }
        }
    </script>

    <!-- SweetAlert Notifications -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success')) 
                Swal.fire({ 
                    icon: 'success', 
                    title: 'Thành công!', 
                    text: @json(session('success')), 
                    timer: 3000, 
                    showConfirmButton: false 
                }); 
            @endif
            @if(session('error')) 
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Lỗi!', 
                    text: @json(session('error')) 
                }); 
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>