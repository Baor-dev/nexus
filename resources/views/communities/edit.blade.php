@extends('layouts.nexus')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 bg-gray-50 border-b border-gray-200">
            <h1 class="text-xl font-bold text-gray-900">Cài đặt Cộng đồng</h1>
            <p class="text-sm text-gray-500 mt-1">Quản lý thông tin, hình ảnh và giao diện của c/{{ $community->name }}</p>
        </div>

        <form action="{{ route('communities.update', $community->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <!-- 1. Tên & Mô tả -->
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block font-bold text-gray-700 mb-2 text-sm">Tên hiển thị</label>
                    <input type="text" name="name" value="{{ old('name', $community->name) }}" class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-nexus-500 focus:border-nexus-500" required>
                    <p class="text-xs text-gray-500 mt-1">Tên này sẽ hiển thị trên URL và tiêu đề nhóm.</p>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-2 text-sm">Mô tả</label>
                    <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-nexus-500 focus:border-nexus-500" required>{{ old('description', $community->description) }}</textarea>
                </div>
            </div>

            <hr class="border-gray-200">

            <!-- 2. Icon (Avatar nhóm) -->
            <div>
                <label class="block font-bold text-gray-700 mb-2 text-sm">Biểu tượng nhóm (Icon)</label>
                <div class="flex items-center gap-6">
                    <!-- Preview -->
                    <div class="w-20 h-20 rounded-full bg-gray-100 border border-gray-300 overflow-hidden flex-shrink-0">
                        <img id="icon-preview" src="{{ $community->icon ? asset('storage/' . $community->icon) : 'https://ui-avatars.com/api/?name=' . $community->name }}" class="w-full h-full object-cover">
                    </div>
                    
                    <!-- Input -->
                    <div class="flex-1">
                        <input type="file" name="icon" accept="image/*" onchange="previewImage(event, 'icon-preview')" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-nexus-50 file:text-nexus-700 hover:file:bg-nexus-100 transition cursor-pointer">
                        <p class="text-xs text-gray-500 mt-2">Khuyên dùng ảnh vuông, tối đa 2MB.</p>
                    </div>
                </div>
            </div>

            <!-- 3. Ảnh bìa (Banner) -->
            <div>
                <label class="block font-bold text-gray-700 mb-2 text-sm">Ảnh bìa (Banner)</label>
                <div class="relative group w-full h-32 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg overflow-hidden hover:border-nexus-400 transition cursor-pointer">
                    <!-- Preview -->
                    <img id="cover-preview" src="{{ $community->cover_image ? asset('storage/' . $community->cover_image) : '' }}" class="{{ $community->cover_image ? '' : 'hidden' }} w-full h-full object-cover">
                    
                    <!-- Placeholder -->
                    <div id="cover-placeholder" class="{{ $community->cover_image ? 'hidden' : 'flex' }} absolute inset-0 items-center justify-center flex-col text-gray-400 group-hover:text-nexus-600">
                        <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-xs font-medium">Nhấn để tải ảnh bìa</span>
                    </div>

                    <!-- Input -->
                    <input type="file" name="cover_image" accept="image/*" onchange="previewCover(event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                </div>
                <p class="text-xs text-gray-500 mt-2">Kích thước đề xuất: 1200x300px.</p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end pt-6 border-t border-gray-100 gap-3">
                <a href="{{ route('communities.show', $community->slug) }}" class="px-6 py-2.5 rounded-full border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition duration-200 text-sm">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-8 py-2.5 bg-nexus-600 hover:bg-nexus-700 text-white font-bold rounded-full shadow-sm transition duration-200 text-sm">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(event, targetId) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById(targetId);
            output.src = reader.result;
        };
        if(event.target.files[0]) reader.readAsDataURL(event.target.files[0]);
    }

    function previewCover(event) {
        const reader = new FileReader();
        reader.onload = function(){
            document.getElementById('cover-placeholder').classList.add('hidden');
            const output = document.getElementById('cover-preview');
            output.src = reader.result;
            output.classList.remove('hidden');
        };
        if(event.target.files[0]) reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection