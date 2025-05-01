<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // Get all categories
    public function index()
    {
        return response()->json(Category::all(), Response::HTTP_OK);
    }

    // Get single category by ID
    public function show($id)
    {
        $category = Category::orderBy('created_at', 'asc')->get();

        if (!$category) {
            return response()->json(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($category, Response::HTTP_OK);
    }

    // Create category
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($request->only(['name', 'description']));

        return response()->json($category, Response::HTTP_CREATED);
    }

    // Update category
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        $category->update($request->only(['name', 'description']));

        return response()->json($category, Response::HTTP_OK);
    }

    // Delete category
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully'], Response::HTTP_OK);
    }
}
