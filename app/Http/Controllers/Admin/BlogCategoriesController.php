<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogCategoriesController extends Controller
{
    public function index()
    {
        $categories = \App\Models\BlogCategory::latest()->paginate(10);
        return view('admin.blog.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.blog.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            
        ]);

        $category = new \App\Models\BlogCategory();
        $category->name = $request->category_name;
        $category->slug = Str::slug($request->category_name);


        $category->save();

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Category created successfully');
    }

    public function edit($id)
    {
        $category = \App\Models\BlogCategory::findOrFail($id);
        return view('admin.blog.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',

        ]);

        $category = \App\Models\BlogCategory::findOrFail($id);
        $category->name = $request->category_name;
        $category->slug = Str::slug($request->category_name);
        

        

        $category->save();

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Category updated successfully');
    }

    public function destroy($id)
    {
        $category = \App\Models\BlogCategory::findOrFail($id);
       
        $category->delete();

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Category deleted successfully');
    }
}