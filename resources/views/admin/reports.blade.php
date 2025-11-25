@extends('layouts.nexus')

@section('content')
<div class="space-y-6">
    
    <!-- 1. THỐNG KÊ TỔNG QUAN (DASHBOARD STATS) -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Card: User -->
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex flex-col items-center justify-center">
            <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Thành viên</div>
            <div class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_users']) }}</div>
        </div>
        
        <!-- Card: Cộng đồng -->
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex flex-col items-center justify-center">
            <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Cộng đồng</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_communities']) }}</div>
        </div>

        <!-- Card: Bài viết -->
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex flex-col items-center justify-center">
            <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Bài viết</div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($stats['total_posts']) }}</div>
        </div>

        <!-- Card: Bình luận -->
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex flex-col items-center justify-center">
            <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Bình luận</div>
            <div class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_comments']) }}</div>
        </div>

        <!-- Card: Banned -->
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex flex-col items-center justify-center bg-red-50 border-red-100">
            <div class="text-red-400 text-xs font-bold uppercase tracking-wider mb-1">Bị khóa</div>
            <div class="text-2xl font-bold text-red-600">{{ number_format($stats['banned_users']) }}</div>
        </div>

        <!-- Card: Report Chờ -->
        <div class="bg-white p-4 rounded-lg shadow-sm border border-orange-200 flex flex-col items-center justify-center bg-orange-50">
            <div class="text-orange-400 text-xs font-bold uppercase tracking-wider mb-1">Cần xử lý</div>
            <div class="text-2xl font-bold text-orange-600">{{ number_format($stats['pending_reports']) }}</div>
        </div>
    </div>

    <!-- 2. KHU VỰC XỬ LÝ REPORT -->
    <div class="bg-white rounded-lg shadow overflow-hidden" x-data="{ activeTab: 'posts' }">
        <!-- Header & Tabs -->
        <div class="border-b border-gray-200">
            <div class="p-6 pb-4 flex justify-between items-center">
                <h2 class="text-xl font-bold text-red-600 flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Trung Tâm Xử Lý Vi Phạm
                </h2>
                <span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">Admin Only</span>
            </div>
            
            <!-- Tab Navigation -->
            <div class="flex px-6 gap-6">
                <button @click="activeTab = 'posts'" 
                        :class="activeTab === 'posts' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="pb-3 border-b-2 font-medium text-sm transition">
                    Nội dung (Bài viết/Comment)
                    <span class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs">{{ $postReports->total() }}</span>
                </button>
                <button @click="activeTab = 'users'" 
                        :class="activeTab === 'users' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="pb-3 border-b-2 font-medium text-sm transition">
                    User bị báo cáo
                    <span class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs">{{ $userReports->total() }}</span>
                </button>
            </div>
        </div>

        <!-- TAB 1: NỘI DUNG (BÀI VIẾT + COMMENT) -->
        <div x-show="activeTab === 'posts'" class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Người báo</th>
                        <th class="px-6 py-3">Loại</th>
                        <th class="px-6 py-3">Lý do</th>
                        <th class="px-6 py-3">Nội dung vi phạm</th>
                        <th class="px-6 py-3 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($postReports as $report)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $report->user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $report->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($report->reportable_type === 'App\Models\Post')
                                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded">Post</span>
                                @elseif($report->reportable_type === 'App\Models\Comment')
                                    <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2 py-0.5 rounded">Comment</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-red-600 font-bold">{{ $report->reason }}</td>
                            <td class="px-6 py-4 max-w-md">
                                @if($report->reportable)
                                    <!-- Xử lý hiển thị Post -->
                                    @if($report->reportable_type === 'App\Models\Post')
                                        <div class="flex items-start gap-3">
                                            @if($report->reportable->thumbnail)
                                                <img src="{{ asset('storage/' . $report->reportable->thumbnail) }}" class="w-12 h-12 object-cover rounded border">
                                            @endif
                                            <div>
                                                <a href="{{ route('posts.show', $report->reportable->slug) }}" target="_blank" class="text-blue-600 font-medium hover:underline block mb-1">
                                                    {{ $report->reportable->title }}
                                                </a>
                                                <div class="text-xs text-gray-500 line-clamp-1">{{ $report->reportable->description }}</div>
                                            </div>
                                        </div>
                                    
                                    <!-- Xử lý hiển thị Comment -->
                                    @elseif($report->reportable_type === 'App\Models\Comment')
                                        <div class="p-2 bg-gray-50 rounded border border-gray-200 text-gray-700 italic">
                                            "{{ Str::limit($report->reportable->content, 100) }}"
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            Bởi: {{ $report->reportable->user->name }} 
                                            @if($report->reportable->post)
                                                trong bài <a href="{{ route('posts.show', $report->reportable->post->slug) }}" target="_blank" class="text-blue-500 hover:underline">xem bài viết</a>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <span class="text-gray-400 italic">(Nội dung đã bị xóa)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('admin.handle', $report->id) }}" method="POST" class="flex gap-2 justify-center">
                                    @csrf
                                    <button name="action" value="dismiss" class="text-gray-500 hover:bg-gray-100 px-3 py-1 rounded text-xs font-bold transition">Bỏ qua</button>
                                    <button name="action" value="delete_content" class="bg-orange-100 text-orange-600 hover:bg-orange-200 px-3 py-1 rounded text-xs font-bold transition">Xóa</button>
                                    <button name="action" value="ban_user" class="bg-red-100 text-red-600 hover:bg-red-200 px-3 py-1 rounded text-xs font-bold transition" onclick="return confirm('BAN user này?')">BAN USER</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-8 text-gray-400">Không có báo cáo nội dung nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">{{ $postReports->appends(['activeTab' => 'posts'])->links() }}</div>
        </div>

        <!-- TAB 2: USER (Giữ nguyên) -->
        <div x-show="activeTab === 'users'" class="overflow-x-auto" style="display: none;">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Người báo</th>
                        <th class="px-6 py-3">Lý do</th>
                        <th class="px-6 py-3">User bị báo cáo</th>
                        <th class="px-6 py-3 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($userReports as $report)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $report->user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $report->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 text-red-600 font-bold">{{ $report->reason }}</td>
                            <td class="px-6 py-4">
                                @if($report->reportable)
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name={{ $report->reportable->name }}&background=random" class="w-10 h-10 rounded-full">
                                        <div>
                                            <a href="{{ route('users.show', $report->reportable->id) }}" target="_blank" class="text-blue-600 font-medium hover:underline block">
                                                {{ $report->reportable->name }}
                                            </a>
                                            <div class="text-xs text-gray-500">{{ $report->reportable->email }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">(User không tồn tại)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('admin.handle', $report->id) }}" method="POST" class="flex gap-2 justify-center">
                                    @csrf
                                    <button name="action" value="dismiss" class="text-gray-500 hover:bg-gray-100 px-3 py-1 rounded text-xs font-bold transition">Bỏ qua</button>
                                    <button name="action" value="ban_user" class="bg-red-600 text-white hover:bg-red-700 px-4 py-1 rounded text-xs font-bold transition shadow" onclick="return confirm('Xác nhận BAN vĩnh viễn user này?')">BAN NGAY</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-8 text-gray-400">Không có báo cáo user nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">{{ $userReports->appends(['activeTab' => 'users'])->links() }}</div>
        </div>
    </div>
</div>
@endsection