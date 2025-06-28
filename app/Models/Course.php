<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'course_code',
        'description',
        'time_description',
        'syllabus',
        'credits',
        'semester',
        'instructor_id',
        'category_id',
        'image',
    ];
    /**
     * Get the instructor associated with the course.
     */
    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
    /**
     * Get the category associated with the course.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
