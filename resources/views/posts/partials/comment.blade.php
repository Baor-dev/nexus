{{-- Component này nhận vào biến $comment và $depth --}}
@php
    $depth = $depth ?? 0;
    $maxDepth = 5; // Giới hạn độ sâu tối đa
@endphp

<div class="mt-4" x-data="{ openReply: false }">
    <div class="flex gap-3">
        <!-- Avatar -->
        <div class="flex-shrink-0">
            <a href="{{ route('users.show', $comment->user->id) }}">
                <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-200">
                    @if($comment->user->avatar)
                        <img src="{{ asset('storage/' . $comment->user->avatar) }}" class="w-full h-full object-cover">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ $comment->user->name }}&background=random" class="w-full h-full object-cover">
                    @endif
                </div>
            </a>
        </div>
        
        <div class="flex-1">
            <!-- Nội dung Comment -->
            <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm relative group">
                <div class="flex justify-between items-start">
                    <div class="text-xs text-gray-500 mb-1">
                        <a href="{{ route('users.show', $comment->user->id) }}" class="font-bold text-gray-800 hover:underline">
                            {{ $comment->user->name }}
                        </a>
                        @if($comment->user->role === 'admin')
                            <span class="bg-red-100 text-red-600 text-[10px] px-1 rounded font-bold ml-1">ADMIN</span>
                        @endif
                        
                        <!-- Hiển thị Badge nếu có -->
                        @if(isset($comment->user->badge))
                            {!! $comment->user->badge !!}
                        @endif

                        <span>• {{ $comment->created_at->diffForHumans() }}</span>
                    </div>

                    <!-- ACTION MENU (Xóa / Báo cáo) -->
                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        @auth
                            @if(Auth::id() !== $comment->user_id)
                                <button onclick="showReportForm('comment', {{ $comment->id }})" class="text-gray-400 hover:text-orange-500 dark:hover:text-orange-400" title="Báo cáo">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </button>
                            @endif

                            @if(Auth::user()->role === 'admin' || Auth::id() === $comment->user_id)
                                <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Xóa bình luận này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 dark:hover:text-red-400" title="Xóa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
                <div class="text-gray-800 text-sm whitespace-pre-wrap">{{ $comment->content }}</div>
            </div>

            <!-- Action Bar -->
            <div class="flex gap-4 text-xs text-gray-500 font-bold mt-1 ml-1">
                <!-- Nút Trả lời: Chỉ hiện nếu depth < maxDepth -->
                @if($depth < $maxDepth)
                    <button @click="openReply = !openReply" class="hover:underline text-nexus-600">
                        Trả lời
                    </button>
                @endif
            </div>

            <!-- FORM TRẢ LỜI (Chỉ gen ra nếu chưa đạt max depth) -->
            @if($depth < $maxDepth)
                <div x-show="openReply" class="mt-3" style="display: none;" x-transition>
                    <form action="{{ route('comments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="post_id" value="{{ $comment->post_id }}">
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                        
                        <div class="flex gap-2 items-start">
                            <div class="w-8 border-b-2 border-l-2 border-gray-300 h-4 rounded-bl-lg"></div>
                            <div class="flex-1">
                                <textarea name="content" rows="2" class="w-full border border-gray-300 rounded-md p-2 text-sm focus:border-nexus-500 focus:ring-1 focus:ring-nexus-500" placeholder="Trả lời {{ $comment->user->name }}..."></textarea>
                                <div class="mt-1 flex justify-end gap-2">
                                    <button type="button" @click="openReply = false" class="text-xs text-gray-500 hover:text-gray-700 px-2 py-1">Hủy</button>
                                    <button type="submit" class="bg-nexus-500 text-white text-xs px-3 py-1.5 rounded-full font-bold hover:bg-nexus-600">Gửi trả lời</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            <!-- HIỂN THỊ COMMENT CON (ĐỆ QUY + TĂNG DEPTH) -->
            @if($comment->replies->count() > 0)
                <div class="ml-4 sm:ml-8 border-l-2 border-gray-100 pl-2">
                    @foreach($comment->replies as $reply)
                        @include('posts.partials.comment', ['comment' => $reply, 'depth' => $depth + 1])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>