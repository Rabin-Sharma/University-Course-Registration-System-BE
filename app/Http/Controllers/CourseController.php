<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        if ($request->has('search') && $request->search)
        {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('course_code', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category') && $request->category)
        {
            $query->where('category_id', $request->category);
        }

        if ($request->has('instructor') && $request->instructor)
        {
            $query->where('instructor_id', $request->instructor);
        }

        $courses = $query->with(['category', 'instructor', 'timeStamps'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Courses retrieved successfully',
            'count' => $courses->count(),
            'courses' => $courses
        ], 200);
    }

    public function enrolledCourses(Request $request)
    {
        $user = User::find(Auth::id());

        $courses = $user->courses()
            ->withPivot('status', 'enrolled_at', 'completed_at', 'dropped_at')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Enrolled courses retrieved successfully',
            'count' => $courses->count(),
            'courses' => $courses
        ], 200);
    }

    public function checkConflicts(Request $request)
    {
        $user = User::find(Auth::id());

        if (!$user)
        {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }

        $selectedCourseID = $request->courseIds ?? [];

        if (empty($selectedCourseID))
        {
            return response()->json([
                'status' => false,
                'message' => 'No courses selected for conflict check',
            ], 400);
        }

        $selectedCourses = Course::whereIn('id', $selectedCourseID)
            ->with('timeStamps')
            ->get();

        //check if enrolled courses have conflicts by comparing their time slots
        $enrolledCourses = $user->courses()
            ->with('timeStamps')
            ->get();

        $checkedCourses = $enrolledCourses->merge($selectedCourses);

        $conflicts = [];

        foreach ($checkedCourses as $course1)
        {
            foreach ($checkedCourses as $course2)
            {
                if ($course1->id >= $course2->id)
                {
                    continue;
                }
                foreach($course1->timeStamps as $time1)
                {
                    foreach($course2->timeStamps as $time2)
                    {
                        if($time1->day != $time2->day) continue;
                        $start1 = Carbon::createFromFormat('H:i:s', $time1->start_time);
                        $end1 = Carbon::createFromFormat('H:i:s', $time1->end_time);
                        $start2 = Carbon::createFromFormat('H:i:s', $time2->start_time);
                        $end2 = Carbon::createFromFormat('H:i:s', $time2->end_time);
                        $overlaps = $start1 < $end2 && $start2 < $end1;

                        if($overlaps)
                        {
                            $overlapStart = $start1->greaterThan($start2) ? $start1 : $start2;
                            $overlapEnd = $end1->lessThan($end2) ? $end1 : $end2;

                            $conflicts[] = [
                                'course1' => $course1->course_code,
                                'course2' => $course2->course_code,
                                'day' => $time1->day,
                                'reason' => "Overlap time range: " . $overlapStart->format('H:i:s') . " - " . $overlapEnd->format('H:i:s')." on ". $time1->day,
                            ];
                        }
                    }
                }

            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Conflicted courses retrieved successfully',
            'count' => $enrolledCourses->count(),
            // 'courses' => $enrolledCourses,
            'conflicts' => $conflicts??[],
        ], 200);
    }
}
