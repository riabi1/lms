<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.category.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name',
            'image' => 'required|image|mimes:jpg,png,jpeg|max:5120',
        ]);

        try {
            $image = $request->file('image');
            $filename = date('YmdHi') . '_' . $image->getClientOriginalName();
            $image->move(public_path('upload/category_images'), $filename); // Stocker dans public

            Category::create([
                'category_name' => $request->category_name,
                'category_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
                'image' => $filename, // Stocker uniquement le nom du fichier
            ]);

            return redirect()->route('admin.categories.index')->with([
                'message' => 'Category Inserted Successfully',
                'alert-type' => 'success'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with([
                'message' => 'Failed to insert category: ' . $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.category.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name,' . $id,
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:5120',
        ]);

        try {
            $data = [
                'category_name' => $request->category_name,
                'category_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
            ];

            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image si elle existe
                if ($category->image && file_exists(public_path('upload/category_images/' . $category->image))) {
                    unlink(public_path('upload/category_images/' . $category->image));
                }

                // Stocker la nouvelle image
                $image = $request->file('image');
                $filename = date('YmdHi') . '_' . $image->getClientOriginalName();
                $image->move(public_path('upload/category_images'), $filename);
                $data['image'] = $filename;
            }

            $category->update($data);

            return redirect()->route('admin.categories.index')->with([
                'message' => 'Category Updated Successfully',
                'alert-type' => 'success'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with([
                'message' => 'Failed to update category: ' . $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);

            // Supprimer l'image si elle existe
            if ($category->image && file_exists(public_path('upload/category_images/' . $category->image))) {
                unlink(public_path('upload/category_images/' . $category->image));
            }

            $category->delete();

            return redirect()->route('admin.categories.index')->with([
                'message' => 'Category Deleted Successfully',
                'alert-type' => 'success'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'message' => 'Failed to delete category: ' . $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
    }
}