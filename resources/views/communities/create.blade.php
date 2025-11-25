@extends('layouts.nexus')
@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tạo Cộng Đồng Mới') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('communities.store') }}">
                        @csrf
                        <!-- Tên Community -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Tên Cộng Đồng</label>
                            <input type="text" name="name" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Ví dụ: Lập trình Laravel">
                        </div>

                        <!-- Mô tả -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Mô tả ngắn</label>
                            <textarea name="description" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                        </div>

                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Tạo ngay
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection