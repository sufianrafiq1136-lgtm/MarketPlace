<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    // shows all categories
    public function index()
    {
        return view('categories.index' );
    }

    // returns categories for the page's client-side rendering
    public function data(): JsonResponse
{
    $categories = Category::latest()->get();

    return response()->json([
        'success' => true,
        'data' => $categories
    ]);
}

    public function create() {}

    public function store(Request $request) {}

    public function show(string $id) {}

    public function edit(string $id) {}

    public function update(Request $request, string $id) {}

    public function destroy(string $id) {}
}
