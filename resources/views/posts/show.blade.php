@extends('layouts.nexus')

@section('content')
<div class="flex gap-6">
    
    <!-- CỘT TRÁI: VOTING SYSTEM (DESKTOP) -->
    <div class="hidden md:flex flex-col items-center gap-2 w-12 pt-2">
        @php
            $userVote = Auth::check() ? $post->votes->where('user_id', Auth::id())->first() : null;
            $voteValue = $userVote ? $userVote->value : 0;
        @endphp

        <!-- Upvote Button -->
        <button onclick="vote('post', {{ $post->id }}, 1)" 
                id="upvote-btn-{{ $post->id }}"
                class="p-2 rounded hover:bg-gray-200 transition {{ $voteValue == 1 ? 'text-orange-500' : 'text-gray-400' }}">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4L4 12h5v8h6v-8h5z"/></svg>
        </button>
        
        <!-- Score -->
        <span id="post-score-{{ $post->id }}" class="font-bold text-lg text-gray-800">
            {{ $post->votes()->sum('value') }}
        </span>
        
        <!-- Downvote Button -->
        <button onclick="vote('post', {{ $post->id }}, -1)" 
                id="downvote-btn-{{ $post->id }}"
                class="p-2 rounded hover:bg-gray-200 transition {{ $voteValue == -1 ? 'text-blue-500' : 'text-gray-400' }}">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12 20l8-8h-5V4h-6v8H4z"/></svg>
        </button>
    </div>

    <!-- CỘT PHẢI: NỘI DUNG CHÍNH -->
    <div class="flex-1 bg-white border border-gray-300 rounded-md overflow-hidden p-0">
        
        <!-- Header Bài Viết -->
        <div class="p-4">
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-4">
                <a href="{{ route('communities.show', $post->community->slug) }}" class="font-bold text-gray-900 hover:underline">
                    c/{{ $post->community->name }}
                </a>
                <span>•</span>
                <span>Đăng bởi <a href="{{ route('users.show', $post->user->id) }}" class="hover:underline">u/{{ $post->user->name }}</a></span>
                {!! $post->user->badge !!}
                <span>{{ $post->created_at->diffForHumans() }}</span>

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

                <!-- REPORT BUTTON (MỚI THÊM) -->
                @auth
                    @if(Auth::id() !== $post->user_id)
                        <button onclick="showReportForm('post', {{ $post->id }})" 
                                class="flex items-center gap-1 hover:bg-red-50 px-2 py-1 rounded transition text-gray-500 hover:text-red-500" 
                                title="Báo cáo vi phạm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span class="text-xs font-bold hidden sm:inline">Báo cáo</span>
                        </button>
                    @endif
                @endauth
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>

            <!-- NÚT SỬA/XÓA (Cho chính chủ) -->
            @if(auth()->id() === $post->user_id)
                <div class="flex gap-2 mb-4">
                    <a href="{{ route('posts.edit', $post->id) }}" class="text-gray-500 font-bold hover:bg-gray-100 px-2 py-1 rounded text-xs border border-gray-300">
                        Sửa bài
                    </a>
                    <button onclick="confirmDelete()" class="text-red-500 font-bold hover:bg-red-50 px-2 py-1 rounded text-xs border border-red-200">
                        Xóa bài
                    </button>
                    <form id="delete-form" action="{{ route('posts.destroy', $post->id) }}" method="POST" style="display: none;">
                        @csrf @method('DELETE')
                    </form>
                </div>
            @endif

            <!-- 1. NỘI DUNG TEXT (HTML) -->
            <div class="prose max-w-none text-gray-800 mb-6">
                {!! $post->content !!}
            </div>

            <!-- 2. MEDIA DISPLAY (ẢNH/VIDEO) -->
                @if($post->thumbnail)
                    <div class="w-full aspect-square bg-gray-100 overflow-hidden flex justify-center items-center relative mt-2">
                        <div class="absolute inset-0 bg-cover bg-center blur-sm opacity-50" style="background-image: url('{{ asset('storage/' . $post->thumbnail) }}')"></div>
                        <!-- Chỉ cần thẻ IMG, GIF sẽ tự chạy -->
                        <img src="{{ asset('storage/' . $post->thumbnail) }}" class="relative w-full h-full object-contain z-10" loading="lazy" alt="{{ $post->title }}">
                    </div>
                @else
                    <div class="px-3 py-4">
                        <p class="text-sm text-gray-800 line-clamp-6">{{ html_entity_decode($post->description) }}</p>
                    </div>
                @endif

        </div>

        <!-- Footer Actions (Mobile Vote layout) -->
        <div class="bg-gray-50 p-2 border-t border-gray-200 flex md:hidden items-center gap-4">
             <div class="flex items-center gap-1 bg-white border border-gray-300 rounded-full px-2 py-1">
                <button onclick="vote('post', {{ $post->id }}, 1)" class="{{ $voteValue == 1 ? 'text-orange-500' : 'text-gray-400' }}"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4L4 12h5v8h6v-8h5z"/></svg></button>
                <span id="post-score-mobile-{{ $post->id }}" class="font-bold text-sm">{{ $post->votes()->sum('value') }}</span>
                <button onclick="vote('post', {{ $post->id }}, -1)" class="{{ $voteValue == -1 ? 'text-blue-500' : 'text-gray-400' }}"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 20l8-8h-5V4h-6v8H4z"/></svg></button>
            </div>
        </div>

        <!-- KHU VỰC BÌNH LUẬN -->
        <div class="bg-gray-50 p-4 sm:p-8 border-t border-gray-200">
            @auth
                <div class="mb-8">
                    <p class="text-sm text-gray-600 mb-2">Bình luận dưới tên <span class="font-bold text-nexus-600">{{ Auth::user()->name }}</span></p>
                    
                    <!-- FORM BÌNH LUẬN CHÍNH -->
                    <form id="main-comment-form" action="{{ route('comments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="post_id" value="{{ $post->id }}">
                        <textarea id="main-comment-content" name="content" rows="4" class="w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:border-nexus-500 focus:ring-1 focus:ring-nexus-500" placeholder="Bạn nghĩ gì về bài viết này?"></textarea>
                        <div class="mt-2 flex justify-end">
                            <button type="button" onclick="submitMainComment()" class="bg-nexus-500 text-white px-6 py-2 rounded-full font-bold text-sm hover:bg-nexus-600 transition">Bình Luận</button>
                        </div>
                    </form>
                </div>
            @else
                <div class="bg-white border border-gray-300 p-6 rounded-lg text-center mb-8 shadow-sm">
                    <h3 class="font-bold text-gray-800 mb-2">Bạn nghĩ gì về bài viết này?</h3>
                    <p class="text-gray-500 mb-4 text-sm">Đăng nhập để tham gia thảo luận.</p>
                    <div class="flex justify-center gap-4">
                        <a href="{{ route('login') }}" class="bg-nexus-500 text-white px-6 py-2 rounded-full font-bold text-sm hover:bg-nexus-600">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="border border-nexus-500 text-nexus-500 px-6 py-2 rounded-full font-bold text-sm hover:bg-blue-50">Đăng ký</a>
                    </div>
                </div>
            @endauth

            <!-- Danh sách comments -->
            <div class="space-y-6">
                @forelse($post->comments as $comment)
                    <!-- CẬP NHẬT: Truyền thêm depth = 0 cho cấp đầu tiên -->
                    @include('posts.partials.comment', ['comment' => $comment, 'depth' => 0])
                @empty
                    <div class="text-center py-10 text-gray-400 italic">
                        Chưa có bình luận nào. Hãy là người đầu tiên!
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection