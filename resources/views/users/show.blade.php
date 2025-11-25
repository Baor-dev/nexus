@extends('layouts.nexus')

@section('content')
<div class="flex flex-col md:flex-row gap-6">
    
    <!-- CỘT TRÁI: DANH SÁCH BÀI VIẾT (66%) -->
    <div class="w-full md:w-2/3 space-y-4">
        <h3 class="font-bold text-lg text-gray-700 mb-2">Bài viết của {{ $user->name }}</h3>

        @forelse($posts as $post)
            @php
                // Logic vote cho bài viết
                $userVote = Auth::check() ? $post->votes->where('user_id', Auth::id())->first() : null;
                $voteValue = $userVote ? $userVote->value : 0;
                $totalScore = $post->votes_sum_value ?? $post->votes->sum('value');
            @endphp

            <!-- Post Card (Đã cập nhật hiển thị ảnh) -->
            <div class="bg-white border border-gray-300 rounded-lg hover:border-gray-400 transition cursor-pointer flex flex-col" 
                 onclick="window.location='{{ route('posts.show', $post->slug) }}'">
                
                <!-- Header Card -->
                <div class="p-3 flex items-center justify-between border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-200">
                            @if($post->user->avatar)
                                <img src="{{ asset('storage/' . $post->user->avatar) }}" class="w-full h-full object-cover">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ $post->user->name }}&background=random" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="text-xs">
                            <div class="font-bold text-gray-900">
                                <a href="{{ route('communities.show', $post->community->slug) }}" class="hover:underline z-10" onclick="event.stopPropagation()">
                                    c/{{ $post->community->name }}
                                </a>
                                <span class="text-gray-400 font-normal">• {{ $post->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2 py-1 rounded-full">
                        {{ $totalScore }} điểm
                    </span>
                </div>

                <!-- Tiêu đề -->
                <div class="px-3 pt-2 pb-1">
                    <h3 class="text-lg font-bold text-gray-900 leading-snug">{{ $post->title }}</h3>
                </div>

                <!-- 1. HIỂN THỊ ẢNH (THUMBNAIL) -->
                @if($post->thumbnail)
                    <div class="w-full aspect-square bg-gray-100 overflow-hidden flex justify-center items-center relative mt-2">
                        <div class="absolute inset-0 bg-cover bg-center blur-sm opacity-50" style="background-image: url('{{ asset('storage/' . $post->thumbnail) }}')"></div>
                        <img src="{{ asset('storage/' . $post->thumbnail) }}" class="relative w-full h-full object-contain z-10" loading="lazy">
                    </div>
                @else
                    <div class="px-3 py-4">
                        <p class="text-sm text-gray-800 line-clamp-6">{{ $post->description }}</p>
                    </div>
                @endif

                <!-- Footer Actions -->
                <div class="p-3 flex items-center gap-4 border-t border-gray-100 mt-auto">
                    <!-- Vote Buttons -->
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
                    
                    <!-- Comments -->
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
            <div class="bg-white p-10 rounded text-center border border-gray-200">
                <p class="text-gray-500">Thành viên này chưa đăng bài viết nào.</p>
            </div>
        @endforelse

        <div class="mt-4">
            {{ $posts->links() }}
        </div>
    </div>

    <!-- CỘT PHẢI: THÔNG TIN USER -->
    <div class="hidden md:block w-1/3 space-y-4">
        <div class="bg-white border border-gray-300 rounded-md overflow-hidden shadow-sm sticky top-20">
            <div class="h-24 bg-nexus-500"></div>
            <div class="px-4 pb-4">
                <div class="relative flex justify-between items-end -mt-10 mb-4">
                    <!-- Avatar Lớn -->
                    <div class="w-24 h-24 bg-white p-1 rounded-lg shadow-lg overflow-hidden">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" class="w-full h-full object-cover rounded-md">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ $user->name }}&background=random&size=128" class="w-full h-full object-cover rounded-md">
                        @endif
                    </div>
                    
                    <!-- Nút Sửa Profile (Nếu là chính chủ) -->
                    @auth
                        @if(Auth::id() === $user->id)
                            <a href="/profile" class="bg-white border border-gray-300 text-gray-700 text-xs font-bold px-4 py-2 rounded-full hover:bg-gray-50 transition shadow-sm">
                                Sửa Profile
                            </a>
                        @endif
                    @endauth
                </div>

                <h2 class="font-bold text-2xl text-gray-900 leading-tight">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500 mb-4">u/{{ $user->name }}</p>

                <!-- 2. NÚT BÁO CÁO USER (Cho người khác xem) -->
                @auth
                    @if(Auth::id() !== $user->id)
                        <button onclick="showReportForm('user', {{ $user->id }})" 
                                class="w-full mb-4 flex items-center justify-center gap-2 text-red-600 hover:bg-red-50 border border-red-200 font-bold py-1.5 rounded-full text-xs transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Báo cáo người dùng
                        </button>
                    @endif
                @endauth

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-gray-50 p-3 rounded-lg text-center border border-gray-100">
                        <span class="block font-bold text-lg text-gray-800">{{ $karma }}</span>
                        <span class="text-[10px] text-gray-500 uppercase tracking-wider font-bold">Karma</span>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg text-center border border-gray-100">
                        <span class="block font-bold text-lg text-gray-800">{{ $user->created_at->format('d/m/y') }}</span>
                        <span class="text-[10px] text-gray-500 uppercase tracking-wider font-bold">Tham gia</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection