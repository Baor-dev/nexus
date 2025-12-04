@extends('layouts.nexus')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.reports') }}" class="p-2 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
        </div>
        <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm font-bold">Tổng: {{ $data->total() }}</span>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 font-bold">ID</th>
                        
                        {{-- Dynamic Headers --}}
                        @if($type === 'users' || $type === 'banned')
                            <th class="px-6 py-4 font-bold">Tên / Email</th>
                            <th class="px-6 py-4 font-bold">Ngày tham gia</th>
                            <th class="px-6 py-4 font-bold">Trạng thái</th>
                        @elseif($type === 'communities')
                            <th class="px-6 py-4 font-bold">Tên nhóm</th>
                            <th class="px-6 py-4 font-bold">Thống kê</th>
                            <th class="px-6 py-4 font-bold">Người tạo</th>
                        @elseif($type === 'posts')
                            <th class="px-6 py-4 font-bold w-1/2">Tiêu đề</th>
                            <th class="px-6 py-4 font-bold">Tác giả / Nhóm</th>
                            <th class="px-6 py-4 font-bold">Tương tác</th>
                        @endif

                        <th class="px-6 py-4 font-bold text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($data as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-500">#{{ $item->id }}</td>

                            {{-- USERS / BANNED ROW --}}
                            @if($type === 'users' || $type === 'banned')
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden">
                                            @if($item->avatar) <img src="{{ asset('storage/' . $item->avatar) }}" class="w-full h-full object-cover">
                                            @else <img src="https://ui-avatars.com/api/?name={{ $item->name }}&background=random" class="w-full h-full"> @endif
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $item->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $item->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $item->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    @if($item->role === 'admin')
                                        <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-xs font-bold">ADMIN</span>
                                    @elseif($item->is_banned)
                                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">BANNED</span>
                                    @else
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">Active</span>
                                    @endif
                                </td>
                            
                            {{-- COMMUNITIES ROW --}}
                            @elseif($type === 'communities')
                                <td class="px-6 py-4">
                                    <a href="{{ route('communities.show', $item->slug) }}" target="_blank" class="font-bold text-nexus-600 hover:underline">c/{{ $item->name }}</a>
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    <div>{{ $item->posts_count }} bài viết</div>
                                    <div class="text-xs">{{ $item->members_count }} thành viên</div>
                                </td>
                                <td class="px-6 py-4 text-gray-500">Admin ID: {{ $item->user_id }}</td>

                            {{-- POSTS ROW --}}
                            @elseif($type === 'posts')
                                <td class="px-6 py-4">
                                    <a href="{{ route('posts.show', $item->slug) }}" target="_blank" class="font-bold text-gray-800 hover:text-nexus-600 line-clamp-1" title="{{ $item->title }}">
                                        {{ $item->title }}
                                    </a>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $item->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    <div>u/{{ $item->user->name }}</div>
                                    <div class="text-nexus-600">c/{{ $item->community->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    Wait: {{ $item->votes_count }} vote • {{ $item->comments_count }} cmt
                                </td>
                            @endif

                            {{-- ACTIONS --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <!-- Nút Ban/Unban (Chỉ cho User) -->
                                    @if($type === 'users' || $type === 'banned')
                                        @if($item->role !== 'admin')
                                            <form action="{{ route('admin.users.toggleBan', $item->id) }}" method="POST" onsubmit="return confirm('Thay đổi trạng thái chặn user này?')">
                                                @csrf
                                                <button class="px-3 py-1.5 rounded border {{ $item->is_banned ? 'border-green-500 text-green-600 hover:bg-green-50' : 'border-orange-500 text-orange-600 hover:bg-orange-50' }} text-xs font-bold transition">
                                                    {{ $item->is_banned ? 'Mở khóa' : 'Khóa' }}
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                    <!-- Nút Sửa (Chỉ cho Community) -->
                                    @if($type === 'communities')
                                        <a href="{{ route('communities.edit', $item->id) }}" class="px-3 py-1.5 rounded border border-blue-500 text-blue-600 hover:bg-blue-50 text-xs font-bold transition">Sửa</a>
                                    @endif

                                    <!-- Nút Xóa (Chung cho tất cả) -->
                                    <form action="{{ route('admin.delete', ['type' => $type, 'id' => $item->id]) }}" method="POST" onsubmit="return confirm('Xác nhận xóa vĩnh viễn? Hành động này KHÔNG THỂ hoàn tác.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1.5 rounded border border-red-500 text-red-600 hover:bg-red-50 text-xs font-bold transition">
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">Không có dữ liệu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $data->links() }}
        </div>
    </div>
</div>
@endsection