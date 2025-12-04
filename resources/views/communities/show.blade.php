@extends('layouts.nexus')

@section('content')
<!-- 1. BANNER & HEADER CỘNG ĐỒNG -->
<div class="bg-white border-b border-gray-200 mb-6 -mt-6 pb-4">
    <!-- Cover Image -->
    <div class="h-32 md:h-48 bg-gradient-to-r from-blue-600 to-nexus-500 relative overflow-hidden">
        @if($community->cover_image)
            <img src="{{ asset('storage/' . $community->cover_image) }}" class="w-full h-full object-cover opacity-90">
        @else
            <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;"></div>
        @endif
    </div>

    <!-- Community Info Bar -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-4">
        <!-- THAY ĐỔI Ở ĐÂY: Thêm 'relative z-10' để nổi lên trên ảnh bìa -->
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-end -mt-12 mb-2 gap-4">
            
            <!-- Avatar (Icon) -->
            <div class="w-24 h-24 bg-white p-1.5 rounded-2xl shadow-lg flex-shrink-0 overflow-hidden">
                @if($community->icon)
                    <img src="{{ asset('storage/' . $community->icon) }}" class="w-full h-full object-cover rounded-xl bg-white">
                @else
                    <div class="w-full h-full bg-gray-200 rounded-xl flex items-center justify-center text-4xl font-bold text-gray-500 uppercase">
                        {{ substr($community->name, 0, 1) }}
                    </div>
                @endif
            </div>

            <!-- Title & Actions -->
            <div class="flex-1 flex flex-col md:flex-row justify-between items-end md:items-end w-full gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 leading-none mb-1 flex items-center gap-2">
                        {{ $community->name }}
                        <!-- Nút Edit cho Admin -->
                        @if(Auth::check() && Auth::id() === $community->user_id)
                            <a href="{{ route('communities.edit', $community->id) }}" class="text-gray-400 hover:text-nexus-600 transition p-1" title="Cài đặt cộng đồng">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </a>
                        @endif
                    </h1>
                    <p class="text-gray-500 font-medium">c/{{ $community->slug }}</p>
                </div>

                <div class="flex items-center gap-3">
                    @auth
                        @php $isJoined = Auth::user()->joinedCommunities->contains($community->id); @endphp
                        <button onclick="toggleJoin({{ $community->id }}, this)" 
                                class="px-6 py-2 rounded-full border font-bold transition min-w-[120px] {{ $isJoined ? 'border-nexus-600 text-nexus-600 bg-white hover:bg-red-50 hover:text-red-600 hover:border-red-600 group' : 'border-gray-300 text-gray-700 bg-white hover:bg-gray-50' }}">
                            <span class="group-hover:hidden">{{ $isJoined ? 'Đã tham gia' : 'Tham gia' }}</span>
                            <span class="hidden group-hover:inline">Rời nhóm</span>
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-2 rounded-full border border-gray-300 text-gray-700 font-bold hover:bg-gray-50 transition">Tham gia</a>
                    @endauth
                    
                    <a href="{{ route('posts.create') }}?community_id={{ $community->id }}" class="px-6 py-2 rounded-full bg-nexus-600 hover:bg-nexus-700 text-white font-bold shadow-sm transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Đăng bài
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row gap-6">
    
    <!-- CỘT TRÁI: BÀI VIẾT (CONTENT) -->
    <div class="w-full md:w-2/3 space-y-4">
        <!-- Bộ lọc (Tùy chọn) -->
        <div class="flex items-center gap-2 mb-2">
            <span class="px-3 py-1 rounded-full bg-gray-200 text-gray-800 text-sm font-bold">Mới nhất</span>
            <span class="px-3 py-1 rounded-full hover:bg-gray-100 text-gray-500 text-sm font-medium cursor-pointer">Nổi bật</span>
        </div>

        @forelse($posts as $post)
            @php
                $userVote = Auth::check() ? $post->votes->where('user_id', Auth::id())->first() : null;
                $voteValue = $userVote ? $userVote->value : 0;
                $totalScore = $post->votes_sum_value ?? 0;
                $isBookmarked = Auth::check() ? Auth::user()->bookmarks->contains($post->id) : false;
            @endphp

            <!-- Post Card (Style chuẩn Nexus) -->
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
                                u/{{ $post->user->name }}
                                <span class="text-gray-400 font-normal">• {{ $post->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Title -->
                <div class="px-3 pt-2 pb-1">
                    <h3 class="text-lg font-bold text-gray-900 leading-snug">{{ $post->title }}</h3>
                </div>

                <!-- Thumbnail -->
                @if($post->thumbnail)
                    <div class="w-full aspect-square bg-gray-100 dark:bg-gray-900 overflow-hidden flex justify-center items-center relative mt-2">
                        <div class="absolute inset-0 bg-cover bg-center blur-sm opacity-50" style="background-image: url('{{ asset('storage/' . $post->thumbnail) }}')"></div>
                        <!-- Chỉ cần thẻ IMG, GIF sẽ tự chạy -->
                        <img src="{{ asset('storage/' . $post->thumbnail) }}" class="relative w-full h-full object-contain z-10" loading="lazy" alt="{{ $post->title }}">
                    </div>
                @else
                    <div class="px-3 py-4">
                        <p class="text-sm text-gray-800 line-clamp-4">{{ html_entity_decode($post->description) }}</p>
                    </div>
                @endif

                <!-- Actions -->
                <div class="p-3 flex items-center gap-4 border-t border-gray-100 mt-auto text-gray-500">
                    <!-- Vote -->
                    <div class="flex items-center gap-1 bg-gray-100 rounded-full px-2 py-1" onclick="event.stopPropagation()">
                        <button onclick="vote('post', {{ $post->id }}, 1)" id="upvote-btn-{{ $post->id }}" class="p-1 rounded hover:bg-gray-200 transition {{ $voteValue == 1 ? 'text-orange-500' : '' }}"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4L4 12h5v8h6v-8h5z"/></svg></button>
                        <span id="post-score-{{ $post->id }}" class="text-sm font-bold min-w-[20px] text-center {{ $voteValue != 0 ? ($voteValue == 1 ? 'text-orange-500' : 'text-blue-500') : '' }}">{{ $totalScore }}</span>
                        <button onclick="vote('post', {{ $post->id }}, -1)" id="downvote-btn-{{ $post->id }}" class="p-1 rounded hover:bg-gray-200 transition {{ $voteValue == -1 ? 'text-blue-500' : '' }}"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 20l8-8h-5V4h-6v8H4z"/></svg></button>
                    </div>

                    <!-- Comments -->
                    <div class="flex items-center gap-1 hover:bg-gray-100 px-2 py-1 rounded transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span class="text-xs font-bold">{{ $post->all_comments_count }} bình luận</span>
                    </div>

                    <!-- Bookmark -->
                    <button onclick="event.stopPropagation(); bookmark({{ $post->id }}, this)" class="flex items-center gap-1 hover:bg-gray-100 px-2 py-1 rounded transition {{ $isBookmarked ? 'text-yellow-500' : '' }}">
                        <svg class="w-5 h-5" fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                        <span class="text-xs font-bold hidden sm:inline">Lưu</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white p-10 rounded-lg border border-gray-200 text-center">
                <div class="text-4xl mb-3">📭</div>
                <h3 class="font-bold text-gray-600 text-lg">Chưa có bài viết nào</h3>
                <p class="text-gray-500 mb-4">Hãy là người đầu tiên đóng góp cho cộng đồng này!</p>
                <a href="{{ route('posts.create') }}?community_id={{ $community->id }}" class="inline-block bg-nexus-600 hover:bg-nexus-700 text-white px-6 py-2 rounded-full font-bold transition">Đăng bài ngay</a>
            </div>
        @endforelse

        <div class="mt-6">
            {{ $posts->links() }}
        </div>
    </div>

    <!-- CỘT PHẢI: SIDEBAR THÔNG TIN -->
    <div class="hidden md:block w-1/3 space-y-4">
        <!-- About Card -->
        <div class="bg-white border border-gray-300 rounded-lg overflow-hidden shadow-sm sticky top-20">
            <div class="bg-gray-100 p-4 font-bold text-gray-800 border-b border-gray-200">
                Về cộng đồng này
            </div>
            <div class="p-4">
                <p class="text-sm text-gray-600 mb-4 leading-relaxed">
                    {{ $community->description }}
                </p>
                
                <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Thành lập {{ $community->created_at->format('d/m/Y') }}</span>
                </div>



                <hr class="my-4 border-gray-200">
                
                <div class="flex justify-between items-center text-sm font-bold mb-4">
                    <div class="text-center">
                        <div class="text-lg text-gray-900">{{ $posts->total() }}</div>
                        <div class="text-xs text-gray-500 font-normal">Bài viết</div>
                    </div>
                    <div class="text-center">
                    <div class="text-lg text-gray-900" id="member-count">
                        {{ $community->members()->count() }} <!-- Cập nhật: Dùng count thật thay vì số liệu giả -->
                    </div>
                    <div class="text-xs text-gray-500 font-normal">Thành viên</div>
                </div>
                </div>

                <a href="{{ route('posts.create') }}?community_id={{ $community->id }}" class="block w-full text-center bg-nexus-600 hover:bg-nexus-700 text-white font-bold py-2 rounded-full transition">
                    Tạo bài viết
                </a>
            </div>
        </div>

        <!-- Rules Card (Static) -->
        <div class="bg-white border border-gray-300 rounded-lg p-4">
            <h3 class="font-bold text-sm text-gray-800 mb-3">Quy tắc cộng đồng</h3>
            <ul class="space-y-2 text-xs text-gray-600">
                <li class="flex gap-2 border-b border-gray-100 pb-2 last:border-0">
                    <span class="font-bold">1.</span> Tôn trọng lẫn nhau.
                </li>
                <li class="flex gap-2 border-b border-gray-100 pb-2 last:border-0">
                    <span class="font-bold">2.</span> Không spam hoặc quảng cáo trái phép.
                </li>
                <li class="flex gap-2 border-b border-gray-100 pb-2 last:border-0">
                    <span class="font-bold">3.</span> Nội dung phải liên quan đến chủ đề.
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    function toggleJoin(communityId, btn) {
        fetch(`/communities/${communityId}/join`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if(response.status === 401) window.location.href = '/login';
            return response.json();
        })
        .then(data => {
            // Cập nhật số lượng thành viên
            document.getElementById('member-count').innerText = data.members_count;

            // Cập nhật giao diện nút bấm
            if (data.joined) {
                // Trạng thái: Đã tham gia
                btn.className = "px-6 py-2 rounded-full border font-bold transition min-w-[120px] border-nexus-600 text-nexus-600 bg-white hover:bg-red-50 hover:text-red-600 hover:border-red-600 group";
                btn.innerHTML = '<span class="group-hover:hidden">Đã tham gia</span><span class="hidden group-hover:inline">Rời nhóm</span>';
                
                Swal.fire({ icon: 'success', title: 'Thành công!', text: data.message, timer: 1500, showConfirmButton: false });
            } else {
                // Trạng thái: Chưa tham gia
                btn.className = "px-6 py-2 rounded-full border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 transition min-w-[120px]";
                btn.innerHTML = 'Tham gia';
                
                Swal.fire({ icon: 'info', title: 'Đã rời nhóm', text: data.message, timer: 1500, showConfirmButton: false });
            }
        })
        .catch(error => console.error('Error:', error));
    }
    </script>
@endsection