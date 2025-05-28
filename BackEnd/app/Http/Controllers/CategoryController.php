<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = News::distinct()->pluck('category');
        return response()->json($categories);
    }
}
