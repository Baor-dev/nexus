@extends('layouts.nexus')

@section('content')
<div class="flex flex-col md:flex-row gap-6">
    
    <!-- CỘT TRÁI: DANH SÁCH BÀI VIẾT (66%) -->
    <div class="w-full md:w-2/3 space-y-4">
        
        <!-- Thanh lọc bài viết -->
        <div class="mb-4 flex items-center gap-2 overflow-x-auto pb-2">
            <a href="/" class="px-4 py-2 rounded-full font-bold text-sm transition {{ !request('sort') ? 'bg-gray-200 text-black' : 'text-gray-500 hover:bg-gray-100' }}">
                🔥 Mới nhất
            </a>
            <a href="/?sort=top" class="px-4 py-2 rounded-full font-bold text-sm transition {{ request('sort') == 'top' ? 'bg-gray-200 text-black' : 'text-gray-500 hover:bg-gray-100' }}">
                🏆 Nổi bật
            </a>
        </div>

        <!-- BẮT ĐẦU VÒNG LẶP BÀI VIẾT -->
        @forelse($posts as $post)
            @php
                // Kiểm tra trạng thái vote của user cho bài viết này
                $userVote = Auth::check() ? $post->votes->where('user_id', Auth::id())->first() : null;
                $voteValue = $userVote ? $userVote->value : 0;
                
                // TỐI ƯU: Lấy tổng điểm đã tính sẵn từ Controller (withSum)
                $totalScore = $post->votes_sum_value ?? 0;
            @endphp

            <!-- Post Card (Square Style) -->
            <div class="bg-white border border-gray-300 rounded-lg hover:border-gray-400 transition cursor-pointer flex flex-col" 
                 onclick="window.location='{{ route('posts.show', $post->slug) }}'">
                
                <!-- Header Bài viết -->
                <div class="p-3 flex items-center justify-between border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <!-- Avatar User -->
                        <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-200">
                            @if($post->user->avatar)
                                <img src="{{ asset('storage/' . $post->user->avatar) }}" class="w-full h-full object-cover">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ $post->user->name }}&background=random" class="w-full h-full object-cover">
                            @endif
                        </div>
                        
                        <!-- Info -->
                        <div class="text-xs">
                            <div class="font-bold text-gray-900">
                                <a href="{{ route('communities.show', $post->community->slug) }}" class="hover:underline z-10" onclick="event.stopPropagation()">
                                    c/{{ $post->community->name }}
                                </a>
                                <span class="text-gray-400 font-normal">• {{ $post->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-gray-500">
                                Đăng bởi u/{{ $post->user->name }}
                            </div>
                        </div>
                    </div>

                    <!-- Điểm Vote -->
                    <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2 py-1 rounded-full">
                        {{ $totalScore }} điểm
                    </span>
                </div>

                <!-- Tiêu đề -->
                <div class="px-3 pt-2 pb-1">
                    <h3 class="text-lg font-bold text-gray-900 leading-snug">{{ $post->title }}</h3>
                </div>

                <!-- ẢNH THUMBNAIL -->
                @if($post->thumbnail)
                    <div class="w-full aspect-square bg-gray-100 overflow-hidden flex justify-center items-center relative mt-2">
                        <div class="absolute inset-0 bg-cover bg-center blur-sm opacity-50" style="background-image: url('{{ asset('storage/' . $post->thumbnail) }}')"></div>
                        <img src="{{ asset('storage/' . $post->thumbnail) }}" class="relative w-full h-full object-contain z-10" loading="lazy" alt="{{ $post->title }}">
                    </div>
                @else
                    <div class="px-3 py-4">
                        <p class="text-sm text-gray-800 line-clamp-6">{{ $post->description }}</p>
                    </div>
                @endif

                <!-- Footer Actions -->
                <div class="p-3 flex items-center gap-4 border-t border-gray-100 mt-auto">
                    <div class="flex items-center gap-1 bg-gray-100 rounded-full px-2 py-1" onclick="event.stopPropagation()">
                        <button onclick="vote('post', {{ $post->id }}, 1)" 
                                id="upvote-btn-{{ $post->id }}"
                                class="p-1 rounded hover:bg-gray-200 transition {{ $voteValue == 1 ? 'text-orange-500' : 'text-gray-400' }}">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4L4 12h5v8h6v-8h5z"/></svg>
                        </button>
                        <span id="post-score-{{ $post->id }}" class="text-sm font-bold text-gray-700 min-w-[20px] text-center">
                            {{ $totalScore }}
                        </span>
                        <button onclick="vote('post', {{ $post->id }}, -1)" 
                                id="downvote-btn-{{ $post->id }}"
                                class="p-1 rounded hover:bg-gray-200 transition {{ $voteValue == -1 ? 'text-blue-500' : 'text-gray-400' }}">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 20l8-8h-5V4h-6v8H4z"/></svg>
                        </button>
                    </div>
                    <div class="flex items-center gap-1 text-gray-500 hover:bg-gray-100 px-2 py-1 rounded transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span class="text-xs font-bold">{{ $post->comments_count }} bình luận</span>
                    </div>
                    <!-- Bookmark Button -->
                    @php
                        $isBookmarked = Auth::check() ? Auth::user()->bookmarks->contains($post->id) : false;
                    @endphp
                    <button onclick="event.stopPropagation(); bookmark({{ $post->id }}, this)" 
                            class="flex items-center gap-1 hover:bg-gray-100 px-2 py-1 rounded transition {{ $isBookmarked ? 'text-yellow-500' : 'text-gray-500' }}" 
                            title="Lưu bài viết">
                        <!-- Icon Bookmark -->
                        <svg class="w-5 h-5" fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                        </svg>
                        <span class="text-xs font-bold hidden sm:inline">Lưu</span>
                    </button>

                    <div class="flex items-center gap-1 hover:bg-gray-100 dark:hover:bg-gray-700 px-2 py-1 rounded transition text-gray-500 dark:text-gray-400" title="Lượt xem">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span class="text-xs font-bold">{{ number_format($post->views) }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white p-10 rounded-md border border-gray-300 text-center">
                <img src="https://www.redditstatic.com/desktop2x/img/empty_feed.png" class="w-20 mx-auto mb-4 opacity-50">
                <h3 class="font-bold text-gray-600 text-lg">Hơi trống vắng nhỉ?</h3>
                <p class="text-gray-500 mb-4">Chưa có bài viết nào trên Nexus. Hãy trở thành người tiên phong!</p>
                <a href="{{ route('posts.create') }}" class="inline-block bg-nexus-500 text-white px-6 py-2 rounded-full font-bold hover:bg-nexus-600">Tạo bài viết ngay</a>
            </div>
        @endforelse

        <div class="mt-4">
            {{ $posts->links() }}
        </div>
    </div>

    <!-- CỘT PHẢI: SIDEBAR (33%) -->
    <div class="hidden md:block w-1/3 space-y-4">
        
        <!-- About Community Card -->
        <div class="bg-white border border-gray-300 rounded-md overflow-hidden">
            <div class="h-10 bg-nexus-500"></div> 
            <div class="p-4">
                <div class="flex items-center gap-2 mb-2">
                    <!-- Logo Sidebar (ĐÃ SỬA THÀNH ẢNH) -->
                    <div class="w-10 h-10 bg-white p-1 rounded-full -mt-8 shadow overflow-hidden">
                        <img src="{{ asset('images/logo.png') }}" class="w-full h-full object-contain">
                    </div>
                    <h2 class="font-bold text-gray-800 text-base pt-1">Trang chủ Nexus</h2>
                </div>
                <p class="text-sm text-gray-600 mb-4 leading-relaxed">
                    Chào mừng bạn đến với Nexus, nơi chia sẻ kiến thức, thảo luận và kết nối cộng đồng.
                </p>
                
                <hr class="my-3 border-gray-200">
                
                <a href="{{ route('posts.create') }}" class="block w-full text-center bg-nexus-500 text-white font-bold py-2 rounded-full hover:bg-nexus-600 transition mb-2">
                    Tạo bài viết
                </a>
                <a href="{{ route('communities.create') }}" class="block w-full text-center border border-nexus-500 text-nexus-500 font-bold py-2 rounded-full hover:bg-nexus-50 transition">
                    Tạo cộng đồng
                </a>
            </div>
        </div>

        <!-- Top Communities -->
        <div class="bg-white border border-gray-300 rounded-md p-4">
            <h3 class="font-bold text-gray-500 text-xs uppercase mb-4 tracking-wider">Cộng đồng nổi bật</h3>
            <ul class="space-y-4">
                @foreach($topCommunities as $index => $community)
                    <li class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-gray-600 w-4">{{ $index + 1 }}</span>
                            <div class="flex flex-col">
                                <a href="{{ route('communities.show', $community->slug) }}" class="text-sm font-medium text-gray-700 hover:underline">
                                    c/{{ $community->name }}
                                </a>
                                <span class="text-xs text-gray-400">{{ $community->posts_count }} thành viên</span>
                            </div>
                        </div>
                        <a href="{{ route('communities.show', $community->slug) }}" class="bg-nexus-500 text-white text-xs font-bold px-3 py-1 rounded-full hover:bg-nexus-600">Xem</a>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Footer -->
        <div class="text-xs text-gray-500 px-4">
            <div class="flex flex-wrap gap-2 mb-2">
                <a href="#" class="hover:underline">Giới thiệu</a>
                <a href="#" class="hover:underline">Điều khoản</a>
                <a href="#" class="hover:underline">Bảo mật</a>
            </div>
            <p>Nexus © {{ date('Y') }}. All rights reserved.</p>
        </div>
    </div>
</div>
@endsection