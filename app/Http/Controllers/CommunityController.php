<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CommunityController extends Controller
{
    // TRANG KHÁM PHÁ (EXPLORE)
    public function index(Request $request)
    {
        $query = Community::withCount('posts');
        if ($search = $request->query('q')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }
        $communities = $query->orderByDesc('posts_count')->paginate(12);
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
            'icon' => 'nullable|image|max:2048',       // Validate icon
            'cover_image' => 'nullable|image|max:4096', // Validate banner
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'user_id' => auth()->id(),
        ];

        // Xử lý upload Icon
        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('communities/icons', 'public');
        }

        // Xử lý upload Cover Image
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('communities/covers', 'public');
        }

        Community::create($data);

        return redirect()->route('communities.index')->with('success', 'Tạo cộng đồng thành công!');
    }
    
    // Xem chi tiết
    public function show(Request $request, $slug)
    {
        $community = Community::where('slug', $slug)->firstOrFail();
        
        $query = $community->posts()
            ->with(['user', 'community', 'votes'])
            ->withCount('comments')
            ->withSum('votes', 'value');

        // LOGIC TÌM KIẾM BÀI VIẾT TRONG CỘNG ĐỒNG
        if ($search = $request->query('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $posts = $query->latest()->paginate(10);

        return view('communities.show', compact('community', 'posts'));
    }

    // MỚI: Hiển thị form chỉnh sửa (Đã sửa quyền cho Admin)
    public function edit(Community $community)
    {
        // Cho phép nếu là Admin HOẶC là người tạo (Owner)
        if (auth()->user()->role !== 'admin' && auth()->id() !== $community->user_id) {
            abort(403, 'Bạn không có quyền chỉnh sửa cộng đồng này');
        }
        return view('communities.edit', compact('community'));
    }

    // MỚI: Xử lý cập nhật (Đã sửa quyền cho Admin)
    public function update(Request $request, Community $community)
    {
        // Cho phép nếu là Admin HOẶC là người tạo (Owner)
        if (auth()->user()->role !== 'admin' && auth()->id() !== $community->user_id) {
            abort(403, 'Bạn không có quyền chỉnh sửa cộng đồng này');
        }

        $request->validate([
            'name' => 'required|max:50|unique:communities,name,' . $community->id,
            'description' => 'required|max:255',
            'icon' => 'nullable|image|max:2048',
            'cover_image' => 'nullable|image|max:4096',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ];

        // Upload Icon
        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('communities/icons', 'public');
        }

        // Upload Cover Image
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('communities/covers', 'public');
        }

        $community->update($data);

        return redirect()->route('communities.show', $community->slug)->with('success', 'Cập nhật thông tin nhóm thành công!');
    }

    // Toggle Join
    public function toggleJoin(Community $community)
    {
        $user = auth()->user();
        $result = $user->joinedCommunities()->toggle($community->id);
        $joined = count($result['attached']) > 0;
        $message = $joined ? 'Chào mừng bạn gia nhập cộng đồng!' : 'Bạn đã rời khỏi cộng đồng.';
        
        return response()->json([
            'joined' => $joined,
            'members_count' => $community->members()->count(),
            'message' => $message
        ]);
    }
}