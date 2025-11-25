@extends('layouts.nexus')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 bg-gray-50 border-b border-gray-200">
            <h1 class="text-xl font-bold text-gray-900">Chỉnh sửa bài viết</h1>
        </div>

        <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <!-- 1. Tiêu đề -->
            <div>
                <label class="block font-bold text-gray-700 mb-2">Tiêu đề</label>
                <input type="text" name="title" value="{{ old('title', $post->title) }}" class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-nexus-500 focus:border-nexus-500" required>
            </div>

            <!-- 2. Cộng đồng -->
            <div>
                <label class="block font-bold text-gray-700 mb-2">Cộng đồng</label>
                <select name="community_id" class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-nexus-500 focus:border-nexus-500">
                    @foreach($communities as $community)
                        <option value="{{ $community->id }}" {{ $post->community_id == $community->id ? 'selected' : '' }}>
                            {{ $community->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Ảnh bìa -->
            <div>
                <label class="block font-bold text-gray-700 mb-2">Ảnh bìa</label>
                
                <!-- Hiển thị ảnh cũ nếu có -->
                @if($post->thumbnail)
                    <div class="mb-2">
                        <p class="text-xs text-gray-500 mb-1">Ảnh hiện tại:</p>
                        <img src="{{ asset('storage/' . $post->thumbnail) }}" class="h-32 rounded border border-gray-200">
                    </div>
                @endif

                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:bg-gray-50 transition relative group cursor-pointer">
                    <input type="file" name="thumbnail" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewThumbnail(event)">
                    <div class="space-y-1 text-center" id="thumbnail-placeholder">
                        <span class="font-medium text-nexus-600">Chọn ảnh mới</span>
                        <span class="text-gray-500"> (nếu muốn thay đổi)</span>
                    </div>
                    <img id="thumbnail-preview" class="hidden max-h-48 rounded mx-auto" />
                </div>
            </div>

            <!-- 4. Nội dung -->
            <div>
                <label class="block font-bold text-gray-700 mb-2">Nội dung</label>
                <textarea id="my-editor" name="content" class="h-96">{!! old('content', $post->content) !!}</textarea>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100">
                <a href="{{ route('posts.show', $post->slug) }}" class="bg-white border border-gray-300 text-gray-700 font-bold py-2 px-6 rounded-full mr-3 hover:bg-gray-50 transition">Hủy</a>
                <button type="submit" class="bg-nexus-500 text-white font-bold py-2 px-8 rounded-full hover:bg-nexus-600 transition shadow-sm">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
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
            selector: '#my-editor',
            height: 500,
            menubar: false,
            plugins: 'image link lists table code',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright | bullist numlist | image link | code',
            content_style: 'body { font-family:Inter,sans-serif; font-size:14px }',
            images_upload_url: "{{ route('upload.image') }}",
            automatic_uploads: true,
            images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', "{{ route('upload.image') }}");
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                xhr.setRequestHeader('X-CSRF-TOKEN', token);
                xhr.upload.onprogress = (e) => { progress(e.loaded / e.total * 100); };
                xhr.onload = () => {
                    if (xhr.status === 403) { reject({ message: 'HTTP Error: ' + xhr.status, remove: true }); return; }
                    if (xhr.status < 200 || xhr.status >= 300) { reject('HTTP Error: ' + xhr.status); return; }
                    const json = JSON.parse(xhr.responseText);
                    resolve(json.location);
                };
                const formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                xhr.send(formData);
            })
        });
    </script>
@endpush