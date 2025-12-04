@extends('layouts.nexus')

@section('content')
<div class="space-y-8">
    
    <!-- 1. DASHBOARD STATS (Các thẻ biến thành LINK) -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Card: User -->
        <a href="{{ route('admin.users') }}" class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col items-center justify-center transition hover:shadow-md hover:border-nexus-300 cursor-pointer group">
            <div class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1 group-hover:text-nexus-600">Thành viên</div>
            <div class="text-2xl font-extrabold text-gray-900">{{ number_format($stats['total_users']) }}</div>
        </a>
        
        <!-- Card: Cộng đồng -->
        <a href="{{ route('admin.communities') }}" class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col items-center justify-center transition hover:shadow-md hover:border-blue-300 cursor-pointer group">
            <div class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1 group-hover:text-blue-600">Cộng đồng</div>
            <div class="text-2xl font-extrabold text-blue-600">{{ number_format($stats['total_communities']) }}</div>
        </a>

        <!-- Card: Bài viết -->
        <a href="{{ route('admin.posts') }}" class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col items-center justify-center transition hover:shadow-md hover:border-green-300 cursor-pointer group">
            <div class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1 group-hover:text-green-600">Bài viết</div>
            <div class="text-2xl font-extrabold text-green-600">{{ number_format($stats['total_posts']) }}</div>
        </a>

        <!-- Card: Bình luận -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col items-center justify-center opacity-75">
            <div class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Bình luận</div>
            <div class="text-2xl font-extrabold text-purple-600">{{ number_format($stats['total_comments']) }}</div>
        </div>

        <!-- Card: Banned -->
        <a href="{{ route('admin.banned') }}" class="bg-red-50 p-4 rounded-xl shadow-sm border border-red-100 flex flex-col items-center justify-center transition hover:shadow-md hover:border-red-300 cursor-pointer group">
            <div class="text-red-500 text-xs font-bold uppercase tracking-wider mb-1 group-hover:text-red-700">Bị khóa</div>
            <div class="text-2xl font-extrabold text-red-600">{{ number_format($stats['banned_users']) }}</div>
        </a>

        <!-- Card: Report Chờ (Link về chính trang này - reset tab) -->
        <a href="{{ route('admin.reports') }}" class="bg-orange-50 p-4 rounded-xl shadow-sm border border-orange-100 flex flex-col items-center justify-center transition hover:shadow-md hover:border-orange-300 cursor-pointer group">
            <div class="text-orange-500 text-xs font-bold uppercase tracking-wider mb-1 group-hover:text-orange-700">Cần xử lý</div>
            <div class="text-2xl font-extrabold text-orange-600">{{ number_format($stats['pending_reports']) }}</div>
        </a>
    </div>

    <!-- 2. KHU VỰC XỬ LÝ REPORT -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" x-data="{ activeTab: '{{ request('activeTab', 'posts') }}' }">
        
        <!-- Header & Tabs -->
        <div class="border-b border-gray-200 bg-gray-50">
            <div class="p-6 pb-4 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <span class="bg-red-100 text-red-600 p-1.5 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </span>
                        Trung Tâm Kiểm Duyệt
                    </h2>
                    <p class="text-sm text-gray-500 mt-1 ml-11">Xử lý các báo cáo vi phạm từ cộng đồng.</p>
                </div>
            </div>
            
            <!-- Modern Tabs -->
            <div class="flex px-6 gap-8">
                <button @click="activeTab = 'posts'" 
                        :class="activeTab === 'posts' ? 'border-nexus-500 text-nexus-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="pb-3 border-b-2 font-semibold text-sm transition flex items-center gap-2">
                    Nội dung vi phạm
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold" :class="activeTab === 'posts' ? 'bg-nexus-100 text-nexus-700' : 'bg-gray-100 text-gray-600'">
                        {{ $postReports->total() }}
                    </span>
                </button>
                <button @click="activeTab = 'users'" 
                        :class="activeTab === 'users' ? 'border-nexus-500 text-nexus-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="pb-3 border-b-2 font-semibold text-sm transition flex items-center gap-2">
                    Người dùng vi phạm
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold" :class="activeTab === 'users' ? 'bg-nexus-100 text-nexus-700' : 'bg-gray-100 text-gray-600'">
                        {{ $userReports->total() }}
                    </span>
                </button>
            </div>
        </div>

        <!-- TAB 1: CONTENT REPORTS -->
        <div x-show="activeTab === 'posts'" class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 font-medium">Người báo cáo</th>
                        <th class="px-6 py-4 font-medium">Lý do & Loại</th>
                        <th class="px-6 py-4 font-medium w-1/3">Nội dung chi tiết</th>
                        <th class="px-6 py-4 font-medium text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($postReports as $report)
                        <tr class="hover:bg-gray-50 transition">
                            <!-- Người báo cáo -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden">
                                         @if($report->user->avatar) <img src="{{ asset('storage/' . $report->user->avatar) }}" class="w-full h-full object-cover">
                                         @else <img src="https://ui-avatars.com/api/?name={{ $report->user->name }}&background=random" class="w-full h-full object-cover"> @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $report->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $report->created_at->format('H:i d/m/Y') }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Lý do & Loại -->
                            <td class="px-6 py-4">
                                <div class="mb-1">
                                    @if($report->reportable_type === 'App\Models\Post')
                                        <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 text-xs font-bold px-2.5 py-0.5 rounded-md border border-blue-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Bài viết
                                        </span>
                                    @elseif($report->reportable_type === 'App\Models\Comment')
                                        <span class="inline-flex items-center gap-1 bg-purple-50 text-purple-700 text-xs font-bold px-2.5 py-0.5 rounded-md border border-purple-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                            Bình luận
                                        </span>
                                    @endif
                                </div>
                                <span class="text-red-600 font-semibold text-sm">{{ $report->reason }}</span>
                            </td>

                            <!-- Nội dung vi phạm -->
                            <td class="px-6 py-4">
                                @if($report->reportable)
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        @if($report->reportable_type === 'App\Models\Post')
                                            <div class="flex items-start gap-3">
                                                @if($report->reportable->thumbnail)
                                                    <img src="{{ asset('storage/' . $report->reportable->thumbnail) }}" class="w-12 h-12 object-cover rounded-md border border-gray-200">
                                                @endif
                                                <div class="flex-1 min-w-0">
                                                    <a href="{{ route('posts.show', $report->reportable->slug) }}" target="_blank" class="text-sm font-bold text-nexus-600 hover:underline block truncate">
                                                        {{ $report->reportable->title }}
                                                    </a>
                                                    <div class="text-xs text-gray-500 line-clamp-1 mt-0.5">
                                                        {{ $report->reportable->description }}
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($report->reportable_type === 'App\Models\Comment')
                                            <div class="text-sm text-gray-700 italic mb-1">
                                                "{{ Str::limit($report->reportable->content, 80) }}"
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                Bởi: <strong>{{ $report->reportable->user->name }}</strong>
                                                @if($report->reportable->post)
                                                    • trong bài <a href="{{ route('posts.show', $report->reportable->post->slug) }}" target="_blank" class="text-nexus-500 hover:underline">xem bài viết</a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-gray-400 italic flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Nội dung đã bị xóa
                                    </div>
                                @endif
                            </td>

                            <!-- Hành động -->
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.handle', $report->id) }}" method="POST" class="flex justify-end gap-2">
                                    @csrf
                                    <button name="action" value="dismiss" class="px-3 py-1.5 rounded-md text-xs font-bold text-gray-500 hover:bg-gray-100 transition border border-transparent hover:border-gray-300">
                                        Bỏ qua
                                    </button>
                                    <button name="action" value="delete_content" class="px-3 py-1.5 rounded-md text-xs font-bold text-white bg-orange-500 hover:bg-orange-600 shadow-sm transition">
                                        Xóa Nội Dung
                                    </button>
                                    <button name="action" value="ban_user" class="px-3 py-1.5 rounded-md text-xs font-bold text-white bg-red-600 hover:bg-red-700 shadow-sm transition" onclick="return confirm('Hành động này sẽ khóa tài khoản vĩnh viễn. Bạn chắc chứ?')">
                                        BAN User
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-sm font-medium">Tuyệt vời! Không có báo cáo nào.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-gray-200 bg-gray-50">
                {{ $postReports->appends(['activeTab' => 'posts'])->links() }}
            </div>
        </div>

        <!-- TAB 2: USER REPORTS -->
        <div x-show="activeTab === 'users'" class="overflow-x-auto" style="display: none;">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 font-medium">Người báo cáo</th>
                        <th class="px-6 py-4 font-medium">Lý do</th>
                        <th class="px-6 py-4 font-medium w-1/3">User bị báo cáo</th>
                        <th class="px-6 py-4 font-medium text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($userReports as $report)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden">
                                         @if($report->user->avatar) <img src="{{ asset('storage/' . $report->user->avatar) }}" class="w-full h-full object-cover">
                                         @else <img src="https://ui-avatars.com/api/?name={{ $report->user->name }}&background=random" class="w-full h-full object-cover"> @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $report->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $report->created_at->format('H:i d/m/Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-red-600 font-semibold">{{ $report->reason }}</td>
                            <td class="px-6 py-4">
                                @if($report->reportable)
                                    <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden border border-gray-200">
                                            @if($report->reportable->avatar) <img src="{{ asset('storage/' . $report->reportable->avatar) }}" class="w-full h-full object-cover">
                                            @else <img src="https://ui-avatars.com/api/?name={{ $report->reportable->name }}&background=random" class="w-full h-full object-cover"> @endif
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('users.show', $report->reportable->id) }}" target="_blank" class="text-sm font-bold text-nexus-600 hover:underline block">{{ $report->reportable->name }}</a>
                                                @if($report->reportable->role === 'admin') <span class="bg-red-100 text-red-600 text-[10px] font-bold px-1.5 py-0.5 rounded">ADMIN</span> @endif
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $report->reportable->email }}</div>
                                            <div class="text-[10px] text-gray-400 mt-0.5">Tham gia: {{ $report->reportable->created_at->format('d/m/Y') }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-gray-400 italic flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        User không tồn tại
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.handle', $report->id) }}" method="POST" class="flex justify-end gap-2">
                                    @csrf
                                    <button name="action" value="dismiss" class="px-3 py-1.5 rounded-md text-xs font-bold text-gray-500 hover:bg-gray-100 transition border border-transparent hover:border-gray-300">Bỏ qua</button>
                                    <button name="action" value="ban_user" class="px-3 py-1.5 rounded-md text-xs font-bold text-white bg-red-600 hover:bg-red-700 shadow-sm transition" onclick="return confirm('Xác nhận BAN vĩnh viễn user này?')">BAN USER</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    <span class="text-sm font-medium">Không có báo cáo người dùng.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-gray-200 bg-gray-50">
                {{ $userReports->appends(['activeTab' => 'users'])->links() }}
            </div>
        </div>
    </div>
</div>
@endsection