<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Testing\Fluent\Concerns\Has;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'unique_id',
        'image',
        'address',
        'phone',
    ];

    public function courses()
    {
        // Define the relationship with the Course model with pivot UserCourseRelation
        // with additional attributes
        // such as status, enrolled_at, completed_at, and dropped_at
        return $this->belongsToMany(Course::class, 'user_course_relations')
            ->withPivot('status', 'enrolled_at', 'completed_at', 'dropped_at')
            ->withTimestamps();
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

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
}
