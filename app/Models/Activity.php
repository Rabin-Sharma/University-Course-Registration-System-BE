<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = ['user_id', 'title', 'description'];
    protected $appends = ['time'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
