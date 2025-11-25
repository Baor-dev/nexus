<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id', 'community_id', 'title', 'slug', 
        'description', 'content', 'thumbnail', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function votes()
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    public function comments() {
    // Chỉ lấy các comment cha (cấp 1) để hiển thị ban đầu
    return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function bookmarkedBy()
    {
        return $this->belongsToMany(User::class, 'bookmarks')->withTimestamps();
    }
}
