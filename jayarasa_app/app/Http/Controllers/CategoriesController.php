<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoriesController extends Controller
{

    /**
     * Index of the resource.
     */
    public function index() : View
    {
        $categories = Categories::orderBy('sort','asc')->get();
        return view('categories.index', compact('categories'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create() : View
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'icon' => 'required',
            'color' => 'required'
        ]);

        $categories_count = Categories::count() + 1;

        Categories::create([
            'nama' => $request->name,
            'icon' => $request->icon,
            'color' => $request->color,
            'sort' => $categories_count
        ]);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(String $uuid) : View
    {
        $category = Categories::findOrFail($uuid);
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, String $uuid)
    {
        $request->validate([
            'name' => 'required',
            'icon' => 'required',
            'color' => 'required'
        ]);
        $category = Categories::findOrFail($uuid);
        $category->update([
            'nama' => $request->name,
            'icon' => $request->icon,
            'color' => $request->color
        ]);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $uuid)
    {
        Categories::findOrFail($uuid)->delete();

        return response()->json(['success' => true,'message' => "Category Has Been Deleted"]);
    }
}
