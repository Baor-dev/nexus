@extends('layouts.nexus')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 bg-gray-50 border-b border-gray-200">
            <h1 class="text-xl font-bold text-gray-900">Tạo bài viết mới</h1>
            <p class="text-sm text-gray-500 mt-1">Chia sẻ kiến thức, câu hỏi hoặc ý tưởng của bạn với cộng đồng.</p>
        </div>

        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            
            <!-- 1. Tiêu đề -->
            <div>
                <label class="block font-bold text-gray-700 mb-2">Tiêu đề <span class="text-red-500">*</span></label>
                <input type="text" name="title" class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-nexus-500 focus:border-nexus-500" placeholder="Tiêu đề bài viết..." required>
            </div>

            <!-- 2. Chọn Cộng đồng -->
            <div>
                <label class="block font-bold text-gray-700 mb-2">Chọn cộng đồng <span class="text-red-500">*</span></label>
                <select name="community_id" class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-nexus-500 focus:border-nexus-500">
                    @foreach($communities as $community)
                        <option value="{{ $community->id }}" {{ (isset($selectedCommunity) && $selectedCommunity == $community->id) ? 'selected' : '' }}>
                            c/{{ $community->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Ảnh bìa (Thumbnail) -->
            <div>
                <label class="block font-bold text-gray-700 mb-2">Ảnh bìa</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:bg-gray-50 transition relative group cursor-pointer">
                    <input type="file" name="thumbnail" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewThumbnail(event)">
                    <div class="space-y-1 text-center" id="thumbnail-placeholder">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <span class="font-medium text-nexus-600 hover:text-nexus-500">Tải ảnh lên</span>
                            <p class="pl-1">hoặc kéo thả vào đây</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF tối đa 2MB</p>
                    </div>
                    <!-- Preview Container -->
                    <img id="thumbnail-preview" class="hidden max-h-48 rounded mx-auto" />
                </div>
            </div>

            <!-- 4. Nội dung (TinyMCE Editor) -->
            <div>
                <label class="block font-bold text-gray-700 mb-2">Nội dung bài viết</label>
                <textarea id="my-editor" name="content" class="h-96"></textarea>
            </div>

            <!-- ACTION BAR -->
            <div class="flex items-center justify-end pt-6 border-t border-gray-100 gap-3">
                <a href="{{ route('home') }}" class="px-6 py-2.5 rounded-full border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-400 transition duration-200">
                    Hủy bỏ
                </a>
                
                <!-- Nút Đăng bài (Đã đổi về màu Nexus) -->
                <button type="submit" class="group relative inline-flex items-center justify-center px-8 py-2.5 text-white transition-all duration-200 bg-gradient-to-r from-nexus-500 to-nexus-600 hover:from-nexus-600 hover:to-nexus-700 font-bold rounded-full shadow-md hover:shadow-lg hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-nexus-500">
                    <span class="mr-2">Đăng bài</span>
                    <svg class="w-5 h-5 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Script Preview Thumbnail -->
    <script>
        function previewThumbnail(event) {
            const reader = new FileReader();
            reader.onload = function(){
                document.getElementById('thumbnail-placeholder').classList.add('hidden');
                const output = document.getElementById('thumbnail-preview');
                output.src = reader.result;
                output.classList.remove('hidden');
            };
            if(event.target.files[0]) reader.readAsDataURL(event.target.files[0]);
        }
    </script>

    <!-- Script TinyMCE (Sử dụng API KEY từ env) -->
    <script src="https://cdn.tiny.cloud/1/{{ env('TINYMCE_API_KEY') }}/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#my-editor', // ID phải khớp với textarea
            height: 500,
            menubar: false,
            plugins: 'image link lists table code preview',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright | bullist numlist | image link | code',
            content_style: 'body { font-family:Inter,sans-serif; font-size:14px }',
            
            // Cấu hình Upload ảnh (Drag & Drop)
            images_upload_url: "{{ route('upload.image') }}",
            automatic_uploads: true,
            
            images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', "{{ route('upload.image') }}");
                
                // Lấy CSRF Token
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                xhr.setRequestHeader('X-CSRF-TOKEN', token);

                xhr.upload.onprogress = (e) => {
                    progress(e.loaded / e.total * 100);
                };

                xhr.onload = () => {
                    if (xhr.status === 403) {
                        reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                        return;
                    }
                    if (xhr.status < 200 || xhr.status >= 300) {
                        reject('HTTP Error: ' + xhr.status);
                        return;
                    }
                    const json = JSON.parse(xhr.responseText);
                    if (!json || typeof json.location != 'string') {
                        reject('Invalid JSON: ' + xhr.responseText);
                        return;
                    }
                    resolve(json.location);
                };

                const formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                xhr.send(formData);
            })
        });
    </script>
@endpush