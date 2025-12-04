<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'cover_image',
        'banner',  // Mới thêm: Ảnh bìa ngang
        'icon',    // Mới thêm: Avatar nhóm
        'user_id'  // Quan trọng: ID người tạo nhóm
    ];

    // Một community có nhiều bài viết
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // Cộng đồng có nhiều thành viên
    public function members()
    {
        return $this->belongsToMany(User::class, 'community_user')->withTimestamps();
    }
}