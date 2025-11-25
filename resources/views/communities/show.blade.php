@extends('layouts.nexus')
@section('content')
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                c/{{ $community->name }}
            </h2>
            <a href="{{ route('posts.create') }}?community_id={{ $community->id }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                Đăng bài vào nhóm này
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Thông tin nhóm -->
            <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
                <p class="text-gray-700">{{ $community->description }}</p>
            </div>

            <!-- Danh sách bài viết -->
            <h3 class="font-bold text-lg mb-4">Bài viết mới nhất</h3>
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
                            <p class="text-gray-600 text-sm">{{ $post->description }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-10">Chưa có bài viết nào. Hãy là người đầu tiên!</p>
                @endforelse

                <!-- Phân trang -->
                <div class="mt-4">
                    {{ $posts->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
