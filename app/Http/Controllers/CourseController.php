<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'category' => 'nullable|integer|exists:categories,id',
            'instructor' => 'nullable|integer|exists:instructors,id',
        ]);

        $query = Course::query();

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('course_code', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('instructor') && $request->instructor) {
            $query->where('instructor_id', $request->instructor);
        }

        $courses = $query->with(['category', 'instructor'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Courses retrieved successfully',
            'count' => $courses->count(),
            'courses' => $courses
        ], 200);
    }
}
