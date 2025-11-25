@extends('layouts.nexus')
@section('content')
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Danh sách Cộng đồng
            </h2>
            <a href="{{ route('communities.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                + Tạo Cộng Đồng
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($communities as $community)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                        <div class="p-6">
                            <h3 class="font-bold text-lg mb-2">
                                <a href="{{ route('communities.show', $community->slug) }}" class="text-blue-600 hover:underline">
                                    c/{{ $community->name }}
                                </a>
                            </h3>
                            <p class="text-gray-600 text-sm mb-4">{{ $community->description }}</p>
                            <a href="{{ route('communities.show', $community->slug) }}" class="text-sm text-gray-500 hover:text-gray-700">
                                Xem chi tiết &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection