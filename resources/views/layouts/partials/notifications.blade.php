@forelse($notifications as $notification)
    <a href="{{ route('notifications.read', $notification->id) }}" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-50 transition relative group {{ $notification->read_at ? 'opacity-70 hover:opacity-100' : 'bg-blue-50/60' }}">
        <div class="flex gap-3">
            <!-- Avatar -->
            <div class="flex-shrink-0 relative">
                @if(isset($notification->data['commenter_avatar']))
                    <img src="{{ asset('storage/' . $notification->data['commenter_avatar']) }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-sm">
                @else
                    <div class="w-10 h-10 bg-gradient-to-br from-nexus-400 to-nexus-600 rounded-full flex items-center justify-center text-white font-bold shadow-sm">
                        {{ substr($notification->data['commenter_name'] ?? 'A', 0, 1) }}
                    </div>
                @endif
                
                <!-- Icon loại thông báo -->
                <div class="absolute -bottom-1 -right-1 bg-white rounded-full p-0.5 shadow-sm">
                    <div class="bg-green-500 rounded-full p-1">
                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Nội dung -->
            <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-800 leading-snug">
                    <span class="font-bold text-gray-900">{{ $notification->data['commenter_name'] ?? 'Ai đó' }}</span>
                    <span class="text-gray-600">đã bình luận:</span>
                    @if(isset($notification->data['message']))
                        <span class="text-gray-500 italic">"{{ Str::limit($notification->data['message'], 50) }}"</span>
                    @endif
                </p>
                <p class="text-xs text-nexus-500 font-medium mt-1.5 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ $notification->created_at->diffForHumans() }}
                </p>
            </div>

            <!-- Chấm xanh chưa đọc -->
            @if(!$notification->read_at)
                <div class="absolute top-1/2 right-3 -translate-y-1/2">
                    <div class="w-2.5 h-2.5 bg-nexus-500 rounded-full shadow-sm border-2 border-white"></div>
                </div>
            @endif
        </div>
    </a>
@empty
    <div class="flex flex-col items-center justify-center py-10 px-4 text-center">
        <div class="bg-gray-100 p-3 rounded-full mb-3">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
        </div>
        <p class="text-gray-800 font-medium text-sm">Chưa có thông báo nào</p>
        <p class="text-gray-500 text-xs mt-1">Tương tác mới sẽ hiển thị tại đây.</p>
    </div>
@endforelse