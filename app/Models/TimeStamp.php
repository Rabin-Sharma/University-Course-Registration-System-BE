<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeStamp extends Model
{
    protected $fillable = [
        'course_id',
        'day',
        'start_time',
        'end_time',
    ];
    /**
     * Get the course associated with the time stamp.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
