<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\InstructorController;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/instructors', [InstructorController::class, 'index']);
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/enrolled', [CourseController::class, 'enrolledCourses']);
    Route::get('/courses/un-enrolled', [CourseController::class, 'unEnrolledCourses']);
    Route::post('courses/check-conflicts', [CourseController::class, 'checkConflicts']);
    Route::post('courses/confirm-registration', [CourseController::class, 'confirmRegistration']);
    Route::get('courses/routine', [CourseController::class, 'routine']);
    Route::get('courses/{course}', [CourseController::class, 'courseDetails']);
});
