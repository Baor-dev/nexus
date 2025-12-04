<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'github_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // Một user có nhiều lượt vote
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function bookmarks()
    {
        return $this->belongsToMany(Post::class, 'bookmarks')->withTimestamps();
    }

    // 1. Tính tổng điểm Karma (Tổng vote của các bài viết user đó đăng)
    // Lưu ý: Để tối ưu, ta nên eager load 'posts.votes' khi query, 
    // hoặc tốt nhất là tạo cột 'karma' riêng trong DB. 
    // Nhưng để làm nhanh, ta tạm dùng query trực tiếp (lưu ý hiệu năng khi scale lớn).
    public function getKarmaAttribute()
    {
        // Cách đơn giản: Lấy tổng vote của tất cả bài viết
        // Nếu muốn nhanh hơn, hãy dùng withSum ở Controller và lấy $this->posts_sum_votes_value
        return $this->posts()->withSum('votes', 'value')->get()->sum('votes_sum_value'); 
    }

    // 2. Logic Huy Hiệu (Badge)
    public function getBadgeAttribute()
    {
        $score = $this->karma; // Gọi hàm trên

        if ($score >= 200) {
            return '<span class="bg-yellow-100 text-yellow-800 text-[10px] font-bold px-1.5 py-0.5 rounded border border-yellow-300">👑 Huyền Thoại</span>';
        } elseif ($score >= 50) {
            return '<span class="bg-red-100 text-red-800 text-[10px] font-bold px-1.5 py-0.5 rounded border border-red-300">🔥 Chuyên Gia</span>';
        } elseif ($score >= 10) {
            return '<span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-1.5 py-0.5 rounded border border-blue-300">🔷 Tích Cực</span>';
        } else {
            return '<span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-1.5 py-0.5 rounded border border-gray-300">🌱 Tập Sự</span>';
        }
    }

    // User tham gia nhiều cộng đồng
    public function joinedCommunities()
    {
        return $this->belongsToMany(Community::class, 'community_user')->withTimestamps();
    }
}
