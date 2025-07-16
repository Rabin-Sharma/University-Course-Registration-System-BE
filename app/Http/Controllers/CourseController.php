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

    public function unEnrolledCourses(Request $request)
    {
        $user = User::find(Auth::id());

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }

        $courses = Course::whereDoesntHave('students', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['category', 'instructor', 'timeStamps'])->get();

        return response()->json([
            'status' => true,
            'message' => 'Unenrolled courses retrieved successfully',
            'count' => $courses->count(),
            'courses' => $courses
        ], 200);
    }

    public function enrolledCourses(Request $request)
    {
        $user = User::find(Auth::id());

        $courses = $user->courses()
            ->withPivot('status', 'enrolled_at', 'completed_at', 'dropped_at')
            ->with(['category', 'instructor', 'timeStamps'])
            ->get();
        $count = $courses->count();
        $credits = $courses->sum('credits');
        $conflictCount = 0;

        return response()->json([
            'status' => true,
            'message' => 'Enrolled courses retrieved successfully',
            'count' => $count,
            'credits' => $credits,
            'conflictCount' => $conflictCount,
            'courses' => $courses
        ], 200);
    }

    public function checkConflicts(Request $request)
    {
        $user = User::find(Auth::id());

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }

        $selectedCourseID = $request->courseIds ?? [];

        if (empty($selectedCourseID)) {
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

        foreach ($checkedCourses as $course1) {
            foreach ($checkedCourses as $course2) {
                if ($course1->id >= $course2->id) {
                    continue;
                }
                foreach ($course1->timeStamps as $time1) {
                    foreach ($course2->timeStamps as $time2) {
                        if ($time1->day != $time2->day) continue;
                        $start1 = Carbon::createFromFormat('H:i:s', $time1->start_time);
                        $end1 = Carbon::createFromFormat('H:i:s', $time1->end_time);
                        $start2 = Carbon::createFromFormat('H:i:s', $time2->start_time);
                        $end2 = Carbon::createFromFormat('H:i:s', $time2->end_time);
                        $overlaps = $start1 < $end2 && $start2 < $end1;

                        if ($overlaps) {
                            $overlapStart = $start1->greaterThan($start2) ? $start1 : $start2;
                            $overlapEnd = $end1->lessThan($end2) ? $end1 : $end2;

                            $conflicts[] = [
                                'course1' => $course1->course_code,
                                'course2' => $course2->course_code,
                                'day' => $time1->day,
                                'reason' => "Overlap time range: " . $overlapStart->format('H:i:s') . " - " . $overlapEnd->format('H:i:s') . " on " . $time1->day,
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
            'conflicts' => $conflicts ?? [],
        ], 200);
    }

    public function confirmRegistration(Request $request)
    {
        $user = User::find(Auth::id());
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }

        // Validate the request
        $request->validate([
            'courseIds' => 'required|array',
            'courseIds.*' => 'exists:courses,id',
        ]);

        //sync the courses with the user
        foreach ($request->courseIds as $courseId) {
            $user->courses()->syncWithoutDetaching([
                $courseId => [
                    'status' => 'enrolled',
                    'enrolled_at' => now(),
                ]
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => 'Courses registered successfully',
        ], 200);
    }


    public function routine(Request $request)
    {
        $user = User::find(Auth::id());

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }

        $enrolledCourses = $user->courses()
            ->with(['instructor', 'timeStamps' => function ($query) {
                $query->orderBy('start_time', 'desc');
            }])
            ->get();


        $routine = [];
        $days = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        for ($time = 8; $time <= 20; $time++) {
            foreach ($days as $day) {
                $routine[$time][$day] = [];

                foreach ($enrolledCourses as $course) {
                    $matched = $course->timeStamps->first(function ($ts) use ($time, $day) {
                        $start = Carbon::parse($ts->start_time)->hour;
                        return $ts->day === $day &&
                            $start == $time;
                    });

                    if ($matched) {
                        $routine[$time][$day] = [
                            'course_code' => $course->course_code,
                            'course_name' => $course->name,
                            'instructor' => $course->instructor->name,
                            'start_time' => Carbon::parse($matched->start_time)->format('H:i'),
                            'end_time' => Carbon::parse($matched->end_time)->format('H:i'),
                        ];
                    }
                }
            }
        }

        return response()->json([
            'status' => true,
            'routine' => $routine,
        ]);
    }
}
