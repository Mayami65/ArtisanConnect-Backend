<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ServiceJob;
use App\Models\User;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    /**
     * Display listing of categories with related counts.
     */
    public function index()
    {
        $categories = Category::all()->map(function ($cat) {
            $cat->jobs_count = ServiceJob::query()->where('category', $cat->name)->count();
            $cat->artisans_count = User::query()->where('role', 'artisan')->where('category', $cat->name)->count();
            return $cat;
        });

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Store a new category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'icon_name' => ['required', 'string', 'max:100'],
            'color_hex' => ['nullable', 'string', 'max:7'],
        ]);

        Category::create([
            'name' => $validated['name'],
            'icon_name' => $validated['icon_name'],
            'color_hex' => $validated['color_hex'] ?? '#a23900',
        ]);

        return back()->with('success', "Category \"{$validated['name']}\" added successfully.");
    }

    /**
     * Update an existing category.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'icon_name' => ['required', 'string', 'max:100'],
            'color_hex' => ['nullable', 'string', 'max:7'],
        ]);

        $category->update([
            'name' => $validated['name'],
            'icon_name' => $validated['icon_name'],
            'color_hex' => $validated['color_hex'] ?? $category->color_hex,
        ]);

        return back()->with('success', "Category \"{$category->name}\" updated successfully.");
    }

    /**
     * Delete a category.
     */
    public function destroy(Category $category)
    {
        $name = $category->name;
        Category::destroy($category->id);

        return back()->with('success', "Category \"{$name}\" removed.");
    }
}
