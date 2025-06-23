<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        // Validate and create user logic
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        // Authentication logic
        if (Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'token' => Auth::user()->createToken('auth_token')->plainTextToken,
                'user' => Auth::user(),
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'Login failed! Invalid credentials',
        ], 401);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        // Logout logic
    }

    /**
     * Handle password reset request.
     */
    public function resetPassword(Request $request)
    {
        // Password reset logic
    }
}
