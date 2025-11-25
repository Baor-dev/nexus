<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CommunityController extends Controller
{
    // Hiển thị danh sách các cộng đồng
    public function index()
    {
        $communities = Community::all();
        return view('communities.index', compact('communities'));
    }

    // Hiển thị form tạo mới
    public function create()
    {
        return view('communities.create');
    }

    // Xử lý lưu dữ liệu
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:communities|max:50',
            'description' => 'required|max:255',
        ]);

        Community::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            // Tạm thời chưa xử lý upload ảnh bìa ở đây để đơn giản hóa
        ]);

        return redirect()->route('communities.index')->with('success', 'Tạo cộng đồng thành công!');
    }
    
    // Xem chi tiết một cộng đồng
    public function show($slug)
    {
        $community = Community::where('slug', $slug)->firstOrFail();
        $posts = $community->posts()->latest()->paginate(10);
        return view('communities.show', compact('community', 'posts'));
    }
}