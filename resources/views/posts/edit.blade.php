@extends('layouts.nexus')

@section('content')
<div class="max-w-4xl mx-auto w-full px-4 sm:px-6 lg:px-8">
    <div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-hidden">
        <div class="p-4 sm:p-6 bg-gray-50 border-b border-gray-200">
            <h1 class="text-lg sm:text-xl font-bold text-gray-900">Chỉnh sửa bài viết</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Cập nhật nội dung và thông tin bài viết của bạn.</p>
        </div>

        <form id="edit-post-form" action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-6 space-y-4 sm:space-y-6">
            @csrf
            @method('PUT')
            
            <!-- 1. Cộng đồng -->
            <div>
                <label class="block font-bold text-gray-700 mb-1 sm:mb-2 text-sm sm:text-base">Cộng đồng <span class="text-red-500">*</span></label>
                <select name="community_id" class="w-full border border-gray-300 rounded-md px-3 py-2 sm:px-4 sm:py-2 text-sm sm:text-base focus:ring-nexus-500 focus:border-nexus-500">
                    @foreach($communities as $community)
                        <option value="{{ $community->id }}" {{ (old('community_id', $post->community_id) == $community->id) ? 'selected' : '' }}>
                            c/{{ $community->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Tiêu đề -->
            <div>
                <label class="block font-bold text-gray-700 mb-1 sm:mb-2 text-sm sm:text-base">Tiêu đề <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $post->title) }}" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 sm:px-4 sm:py-2 text-sm sm:text-base focus:ring-nexus-500 focus:border-nexus-500" required>
            </div>

            <!-- 3. Mô tả ngắn -->
            <div>
                <label class="block font-bold text-gray-700 mb-1 sm:mb-2 text-sm sm:text-base">Mô tả ngắn</label>
                <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 sm:px-4 sm:py-2 text-sm sm:text-base focus:ring-nexus-500 focus:border-nexus-500" placeholder="Tóm tắt nội dung bài viết" required>{{ old('description', html_entity_decode($post->description)) }}</textarea>
            </div>

            <!-- 4. Media -->
            <div>
                <label class="block font-bold text-gray-700 mb-1 sm:mb-2 text-sm sm:text-base">Media (Ảnh/Video) <span class="text-gray-400 font-normal text-xs">(Video < 60s)</span></label>
                
                <!-- Hiển thị Media hiện tại -->
                @if($post->thumbnail)
                    @php
                        $extension = pathinfo($post->thumbnail, PATHINFO_EXTENSION);
                        $isVideo = in_array(strtolower($extension), ['mp4', 'mov', 'avi', 'webm']);
                    @endphp
                    <div class="mb-3 p-2 border border-gray-200 rounded bg-gray-50 inline-block">
                        <p class="text-xs text-gray-500 mb-1 font-semibold">Hiện tại:</p>
                        @if($isVideo)
                            <!-- Video cũ cũng tự chạy -->
                            <video class="h-32 rounded object-cover bg-black" autoplay muted loop playsinline controls>
                                <source src="{{ asset('storage/' . $post->thumbnail) }}" type="video/mp4">
                            </video>
                        @else
                            <img src="{{ asset('storage/' . $post->thumbnail) }}" class="h-32 rounded object-cover">
                        @endif
                    </div>
                @endif

                <div class="mt-1 flex justify-center px-4 pt-4 pb-5 sm:px-6 sm:pt-5 sm:pb-6 border-2 border-gray-300 border-dashed rounded-md hover:bg-gray-50 transition relative group cursor-pointer">
                    <input type="file" id="thumbnail_input" name="thumbnail" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewMedia(event)" accept="image/*,video/mp4,video/quicktime">
                    
                    <div class="space-y-1 text-center" id="media-placeholder">
                        <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        <div class="flex text-xs sm:text-sm text-gray-600 justify-center"><span class="font-medium text-nexus-600 hover:text-nexus-500">Chọn file mới</span><p class="pl-1">để thay thế (tùy chọn)</p></div>
                        <p class="text-[10px] sm:text-xs text-gray-500">Ảnh hoặc Video (Max 50MB)</p>
                    </div>

                    <!-- Container Preview -->
                    <div id="preview-container" class="hidden w-full flex justify-center relative z-0">
                        <img id="img-preview" class="hidden max-h-64 rounded shadow-sm object-contain">
                        <video id="video-preview" class="hidden max-h-64 rounded shadow-sm bg-black w-full" autoplay muted loop playsinline controls>
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
            </div>

            <!-- 5. Nội dung -->
            <div>
                <label class="block font-bold text-gray-700 mb-1 sm:mb-2 text-sm sm:text-base">Nội dung <span class="text-red-500">*</span></label>
                <textarea id="my-editor" name="content" class="h-64 sm:h-96">{!! old('content', $post->content) !!}</textarea>
            </div>

            <!-- ACTION BAR -->
            <div class="flex flex-col-reverse sm:flex-row items-center justify-end pt-4 sm:pt-6 border-t border-gray-100 gap-2 sm:gap-3">
                <a href="{{ route('posts.show', $post->slug) }}" class="w-full sm:w-auto text-center px-6 py-2.5 rounded-full border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-400 transition duration-200 text-sm sm:text-base">
                    Hủy bỏ
                </a>
                
                <button type="button" onclick="submitPost()" class="w-full sm:w-auto group relative inline-flex items-center justify-center px-8 py-2.5 text-white transition-all duration-200 bg-gradient-to-r from-nexus-500 to-nexus-600 hover:from-nexus-600 hover:to-nexus-700 font-bold rounded-full shadow-md hover:shadow-lg hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-nexus-500 text-sm sm:text-base">
                    <span class="mr-2">Lưu thay đổi</span>
                    <svg class="w-5 h-5 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </button>
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

        // Hàm Xử lý Cập nhật bài viết (AJAX)
        function submitPost() {
            tinymce.triggerSave();

            const form = document.getElementById('edit-post-form');
            const formData = new FormData(form);
            
            const title = formData.get('title');
            const content = formData.get('content');

            if (!title.trim()) {
                Swal.fire({ icon: 'warning', title: 'Thiếu thông tin', text: 'Vui lòng nhập tiêu đề bài viết!', confirmButtonColor: '#0ea5e9' });
                return;
            }

            if (!content.trim()) {
                Swal.fire({ icon: 'warning', title: 'Thiếu thông tin', text: 'Vui lòng nhập nội dung bài viết!', confirmButtonColor: '#0ea5e9' });
                return;
            }

            Swal.fire({
                title: 'Đang cập nhật...',
                text: 'Vui lòng chờ trong giây lát',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            // Gửi AJAX request
            fetch(form.action, {
                method: 'POST', // Laravel Method Spoofing sẽ xử lý PUT qua _method trong form
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(({ status, body }) => {
                if (status === 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: body.message || 'Đã cập nhật bài viết!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        // Nếu server trả về redirect_url thì dùng, không thì reload hoặc về trang chi tiết
                        window.location.href = body.redirect_url || "{{ route('posts.show', $post->slug) }}";
                    });
                } else if (status === 422) {
                    let errorHtml = '<ul class="text-left text-sm text-red-600 list-disc pl-5">';
                    for (const key in body.errors) {
                        errorHtml += `<li>${body.errors[key][0]}</li>`;
                    }
                    errorHtml += '</ul>';
                    Swal.fire({ icon: 'error', title: 'Lỗi nhập liệu', html: errorHtml, confirmButtonColor: '#d33' });
                } else {
                    throw new Error(body.message || 'Có lỗi xảy ra');
                }
            })
            .catch(error => {
                console.error(error);
                // Fallback: Nếu lỗi JSON (do controller trả về redirect thay vì JSON), submit form thường
                if (error instanceof SyntaxError) {
                     form.submit(); 
                } else {
                    Swal.fire({ icon: 'error', title: 'Lỗi', text: 'Không thể kết nối đến máy chủ. Vui lòng thử lại.', confirmButtonColor: '#d33' });
                }
            });
        }
    </script>

    <script src="https://cdn.tiny.cloud/1/{{ config('services.tinymce.key') }}/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#my-editor',
            height: window.innerWidth < 640 ? 300 : 500,
            menubar: false,
            plugins: 'image link lists table code preview',
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