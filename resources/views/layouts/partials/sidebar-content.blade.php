<div x-data 
     x-show="$store.mobileMenuOpen" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 -translate-x-full"
     x-transition:enter-end="opacity-100 translate-x-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-x-0"
     x-transition:leave-end="opacity-0 -translate-x-full"
     class="fixed inset-0 z-[60] flex lg:hidden" 
     style="display: none;">
    
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" 
         x-show="$store.mobileMenuOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="$store.mobileMenuOpen = false"></div>

    <div class="relative w-[85%] max-w-[360px] bg-white h-full shadow-2xl flex flex-col z-10 rounded-r-2xl overflow-hidden"
         x-show="$store.mobileMenuOpen"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full">
        
        <div class="px-5 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between flex-shrink-0"></div>

        <div class="flex-1 overflow-y-auto custom-scrollbar py-4 px-3 space-y-6">
            
            <div class="space-y-1">
                <a href="{{ route('home') }}" 
                   class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all duration-200 group
                   {{ request()->routeIs('home') && !request('sort') ? 'bg-blue-50 text-nexus-700 font-bold' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                    <div class="{{ request()->routeIs('home') && !request('sort') ? 'text-nexus-600' : 'text-gray-400 group-hover:text-nexus-600' }} transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    Trang chủ
                </a>

                <a href="{{ route('home', ['sort' => 'new']) }}" 
                   class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all duration-200 group
                   {{ request('sort') === 'new' ? 'bg-blue-50 text-nexus-700 font-bold' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                    <div class="{{ request('sort') === 'new' ? 'text-nexus-600' : 'text-gray-400 group-hover:text-nexus-600' }} transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    Mới nhất
                </a>

                <a href="{{ route('home', ['sort' => 'top']) }}" 
                   class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all duration-200 group
                   {{ request('sort') === 'top' ? 'bg-blue-50 text-nexus-700 font-bold' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                    <div class="{{ request('sort') === 'top' ? 'text-nexus-600' : 'text-gray-400 group-hover:text-nexus-600' }} transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    Nổi bật
                </a>

                <a href="{{ route('communities.index') }}" 
                   class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all duration-200 group
                   {{ request()->routeIs('communities.index') ? 'bg-blue-50 text-nexus-700 font-bold' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                    <div class="{{ request()->routeIs('communities.index') ? 'text-nexus-600' : 'text-gray-400 group-hover:text-nexus-600' }} transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                    </div>
                    Khám phá cộng đồng
                </a>
            </div>

            <hr class="border-gray-100">

            @auth
                <div class="space-y-2">
                    <h3 class="px-3 text-xs font-bold text-gray-400 uppercase tracking-wider">
                        Cộng đồng của bạn
                    </h3>
                    <div class="space-y-0.5">
                        @forelse(Auth::user()->joinedCommunities->take(10) as $community)
                            <a href="{{ route('communities.show', $community->slug) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-100 text-gray-700 hover:text-gray-900 transition group">
                                <div class="w-6 h-6 rounded-full overflow-hidden shadow-sm flex-shrink-0 group-hover:scale-105 transition-transform">
                                    @if($community->icon)
                                        <img src="{{ asset('storage/' . $community->icon) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-nexus-600 flex items-center justify-center text-white text-[10px] font-bold">c/</div>
                                    @endif
                                </div>
                                <span class="truncate font-medium text-sm">c/{{ $community->name }}</span>
                            </a>
                        @empty
                            <div class="px-3 py-4 text-center bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-500 italic">Chưa tham gia cộng đồng nào</p>
                                <a href="{{ route('communities.index') }}" class="text-xs text-nexus-600 font-bold mt-1 block hover:underline">Khám phá ngay</a>
                            </div>
                        @endforelse

                        @if(Auth::user()->joinedCommunities->count() > 10)
                            <a href="{{ route('communities.index') }}" class="block px-3 py-2 text-sm text-nexus-600 font-semibold hover:underline mt-1">
                                Xem tất cả ({{ Auth::user()->joinedCommunities->count() }})
                            </a>
                        @endif
                    </div>
                </div>
            @endauth
        </div>
    </div>
</div>