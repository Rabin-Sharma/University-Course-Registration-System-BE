<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json([
            'status' => true,
            'message' => 'Categories retrieved successfully',
            'count' => $categories->count(),
            'categories' => $categories,
        ], 200);
    }
}
