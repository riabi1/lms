<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{

  public function index()
  {
    $categories = Category::latest()->get();
    return view('admin.backend.category.index', compact('categories'));
  }

  public function create()
  {
    return view('admin.backend.category.create');
  }

  public function store(Request $request)
  {
    $request->validate([
      'category_name' => 'required|string|max:255|unique:categories,category_name',
      'image' => 'required|image|mimes:jpg,png|max:5120',
    ]);

    $imagePath = $request->file('image')->store('categories', 'public');

    Category::create([
      'category_name' => $request->category_name,
      'category_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
      'image' => $imagePath, // Chemin relatif : "categories/..."
    ]);

    return redirect()->route('admin.categories.index')->with([
      'message' => 'Category Inserted Successfully',
      'alert-type' => 'success'
    ]);
  }

  public function edit($id)
  {
    $category = Category::findOrFail($id);
    return view('admin.backend.category.edit', compact('category'));
  }

  public function update(Request $request, $id)
  {
    $category = Category::findOrFail($id);

    $request->validate([
      'category_name' => 'required|string|max:255|unique:categories,category_name,' . $id,
      'image' => 'sometimes|image|mimes:jpg,png|max:5120', // "sometimes" pour rendre l'image optionnelle
    ]);

    if ($request->hasFile('image')) {
      if ($category->image) {
        Storage::disk('public')->delete($category->image); // Supprime l'ancienne image
      }
      $imagePath = $request->file('image')->store('categories', 'public');
      $category->image = $imagePath;
    }

    $category->update([
      'category_name' => $request->category_name,
      'category_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
    ]);

    return redirect()->route('admin.categories.index')->with([
      'message' => 'Category Updated Successfully',
      'alert-type' => 'success'
    ]);
  }

  public function destroy($id)
  {
    $category = Category::findOrFail($id);

    if ($category->image) {
      Storage::disk('public')->delete($category->image); // Supprime l'image
    }

    $category->delete();

    return redirect()->route('admin.categories.index')->with([
      'message' => 'Category Deleted Successfully',
      'alert-type' => 'success'
    ]);
  }
}
