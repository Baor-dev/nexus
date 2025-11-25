<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'cover_image'];

    // Một community có nhiều bài viết
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

}
