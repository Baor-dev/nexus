@extends('layouts.nexus')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Header Section (Căn giữa giống mẫu) -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-nexus-700 mb-4 tracking-tight">Khám Phá Cộng Đồng</h1>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">Tìm kiếm và tham gia các không gian thảo luận phù hợp với sở thích của bạn.</p>
            
            <!-- Search & Action Area -->
            <div class="mt-8 flex flex-col sm:flex-row justify-center items-center gap-4">
                <form action="{{ route('communities.index') }}" method="GET" class="relative w-full max-w-md">
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ request('q') }}" 
                        placeholder="Tìm kiếm cộng đồng..." 
                        class="block w-full pl-12 pr-4 py-3 bg-white border border-gray-300 rounded-full text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-nexus-500 focus:border-transparent shadow-sm transition-all"
                    >
                </form>

                @auth
                <a href="{{ route('communities.create') }}" class="inline-flex items-center gap-2 bg-nexus-600 hover:bg-nexus-700 text-white font-semibold px-6 py-3 rounded-full transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 whitespace-nowrap">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tạo Cộng Đồng
                </a>
                @endauth
            </div>
        </div>

        <!-- Communities Grid -->
        @if($communities->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($communities as $community)
                    <a href="{{ route('communities.show', $community->slug) }}" class="block group h-full">
                        <!-- Card Style giống mẫu tham khảo -->
                        <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-xl transition-all duration-300 border border-gray-200 h-full flex flex-col relative overflow-hidden">
                            
                            <!-- Accent Line on Hover -->
                            <div class="absolute top-0 left-0 w-full h-1 bg-nexus-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>

                            <div class="flex items-start gap-4">
                                <!-- Avatar / Icon -->
                                <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-nexus-50 text-nexus-600 rounded-xl flex items-center justify-center shadow-sm border border-nexus-100 group-hover:scale-110 transition-transform duration-300 overflow-hidden">
                                    @if($community->icon)
                                        <img src="{{ asset('storage/' . $community->icon) }}" class="w-full h-full object-cover" alt="{{ $community->name }}">
                                    @else
                                        <!-- Fallback icon text -->
                                        <span class="text-xl font-bold">
                                            {{ strtoupper(substr($community->name, 0, 1)) }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Title & Stats -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-bold text-gray-800 group-hover:text-nexus-700 transition-colors truncate">
                                        c/{{ $community->name }}
                                    </h3>
                                    <div class="flex items-center gap-1 text-xs text-gray-500 mt-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                        <span>{{ number_format($community->posts_count) }} bài viết</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="mt-4 flex-1">
                                <p class="text-sm text-gray-600 line-clamp-2 leading-relaxed">
                                    {{ $community->description ?: 'Chưa có mô tả cho cộng đồng này.' }}
                                </p>
                            </div>

                            <!-- Footer Link -->
                            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between text-sm">
                                <span class="text-nexus-600 font-medium group-hover:underline">Join</span>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-nexus-500 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $communities->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center max-w-2xl mx-auto mt-8 shadow-sm">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-6">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Không tìm thấy cộng đồng nào</h3>
                <p class="text-gray-600 mb-8">
                    @if(request('q'))
                        Không có kết quả cho "{{ request('q') }}". Hãy thử từ khóa khác.
                    @else
                        Hệ thống chưa có cộng đồng nào.
                    @endif
                </p>
                @auth
                    <a href="{{ route('communities.create') }}" class="inline-flex items-center gap-2 bg-nexus-600 hover:bg-nexus-700 text-white font-semibold px-8 py-3 rounded-full transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tạo Cộng Đồng Mới
                    </a>
                @endauth
            </div>
        @endif
    </div>
</div>
@endsection