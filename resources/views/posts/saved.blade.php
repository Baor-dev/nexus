@extends('layouts.nexus')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 border-b border-gray-200 pb-4 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-8 h-8 text-yellow-500" fill="currentColor" viewBox="0 0 24 24"><path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
            Bài viết đã lưu
        </h1>
        <span class="text-gray-500 text-sm font-medium">{{ $posts->total() }} bài viết</span>
    </div>

    <div class="space-y-4">
        @forelse($posts as $post)
            <div class="bg-white p-6 rounded-lg shadow-sm flex gap-4">
                        <!-- Ảnh Thumbnail -->
                        @if($post->thumbnail)
                            <div class="w-32 h-24 flex-shrink-0">
                                <img src="{{ asset('storage/' . $post->thumbnail) }}" class="w-full h-full object-cover rounded">
                            </div>
                        @endif
                        
                        <!-- Nội dung -->
                        <div class="flex-1">
                            <h4 class="font-bold text-xl mb-1">
                                <a href="{{ route('posts.show', $post->slug) }}" class="hover:underline">{{ $post->title }}</a>
                            </h4>
                            <div class="text-xs text-gray-500 mb-2">
                                Đăng bởi {{ $post->user->name }} • {{ $post->created_at->diffForHumans() }}
                            </div>
                            <p class="text-gray-600 text-sm">{{ html_entity_decode($post->description) }}</p>
                        </div>
                </div>
        @empty
            <div class="text-center py-12 bg-white rounded-lg border border-dashed border-gray-300">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có bài viết nào</h3>
                <p class="mt-1 text-sm text-gray-500">Hãy bấm nút "Lưu" ở các bài viết bạn yêu thích.</p>
                <div class="mt-6">
                    <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-nexus-600 hover:bg-nexus-700">
                        Khám phá ngay
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $posts->links() }}
    </div>
</div>
@endsection
