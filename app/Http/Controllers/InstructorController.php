<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    public function index(Request $request)
    {
        $instructors = Instructor::all();
        return response()->json([
            'status' => true,
            'message' => 'Instructors retrieved successfully',
            'count' => $instructors->count(),
            'instructors' => $instructors
        ], 200);
    }
}
