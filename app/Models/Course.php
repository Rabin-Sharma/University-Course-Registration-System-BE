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

    public function students()
    {
        // Define the relationship with the User model with pivot UserCourseRelation
        // with additional attributes such as status, enrolled_at, completed_at, and dropped_at
        return $this->belongsToMany(User::class, 'user_course_relations')
            ->withPivot('status', 'enrolled_at', 'completed_at', 'dropped_at')
            ->withTimestamps();
    }

    public function timeStamps()
    {
        // Define the relationship with the TimeStamp model
        return $this->hasMany(TimeStamp::class);
    }
}
