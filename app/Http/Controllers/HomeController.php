<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('index', compact('categories'));
    }
    public function categoryShow($slug)
    {
        $category = Category::where('slug', $slug)->where('status', 1)->firstOrFail();

        $children = Category::where('parent_id', $category->id)
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('category', compact('category', 'children'));
    }

}
