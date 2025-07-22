<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        //get the authenticated user recent activities
        $user = $request->user();
        $activities = $user->activities()->latest()->get();

        return response()->json([
            'status' => true,
            'message' => 'Activities retrieved successfully',
            'activities' => $activities,
        ]);
    }
}
