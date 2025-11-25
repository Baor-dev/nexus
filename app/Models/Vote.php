<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $fillable = ['user_id', 'value', 'votable_id', 'votable_type'];

    public function votable()
    {
        return $this->morphTo();
    }
}
