<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::all();
        return response()->json([
            'status' => true,
            'message' => 'Courses retrieved successfully',
            'courses' => $courses
        ], 200);
    }
}
