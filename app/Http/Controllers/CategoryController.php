<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    // Show the category management page
    public function index()
    {
        return view('categories.index');
    }

    // Return all categories for client-side rendering
    public function data(): JsonResponse
    {
        $categories = Category::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
            'errors' => null,
        ]);
    }

    // Create a new category
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $slug = Str::slug($validated['name']);

        if (Category::where('slug', $slug)->exists()) {
            throw ValidationException::withMessages([
                'name' => 'A category with this name already exists.',
            ]);
        }

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return response()->json([
            'success' => true,
            'data' => $category,
            'errors' => null,
        ], 201);
    }

    // Return one category for editing
    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $category,
            'errors' => null,
        ]);
    }

    // Update an existing category
    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
        ]);

        if (Category::where('slug', $validated['slug'])->whereKeyNot($category->id)->exists()) {
            throw ValidationException::withMessages([
                'slug' => 'This slug is already in use.',
            ]);
        }

        $category->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $category->fresh(),
            'errors' => null,
        ]);
    }

    // Delete a category
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'errors' => null,
        ]);
    }
}
