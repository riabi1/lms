<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:admin', 'verified']); // Ensure only admins access
  }

  public function AllCategory()
  {
    $categories = Category::latest()->get();
    return view('admin.backend.category.all_category', compact('categories'));
  }

  public function AddCategory()
  {
    return view('admin.backend.category.add_category');
  }

  public function StoreCategory(Request $request)
  {
    $request->validate([
      'category_name' => 'required|string|max:255|unique:categories,category_name',
      'image' => 'required|image|mimes:jpg,png|max:5120',
    ]);

    $image = $request->file('image');
    $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
    $save_path = 'upload/category/' . $name_gen;
    $image->move(public_path('upload/category'), $name_gen); // Move the file directly
    $save_url = $save_path;

    Category::create([
      'category_name' => $request->category_name,
      'category_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
      'image' => $save_url,
    ]);

    return redirect()->route('admin.all.category')->with([
      'message' => 'Category Inserted Successfully',
      'alert-type' => 'success'
    ]);
  }

  public function EditCategory($id)
  {
    $category = Category::findOrFail($id);
    return view('admin.backend.category.edit_category', compact('category'));
  }

  public function UpdateCategory(Request $request)
  {
    $cat_id = $request->id;
    $category = Category::findOrFail($cat_id);

    $request->validate([
      'category_name' => 'required|string|max:255|unique:categories,category_name,' . $cat_id,
      'image' => 'nullable|image|mimes:jpg,png|max:5120',
    ]);

    if ($request->hasFile('image')) {
      // Delete old image if it exists
      if (File::exists(public_path($category->image))) {
        File::delete(public_path($category->image));
      }

      $image = $request->file('image');
      $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
      $save_path = 'upload/category/' . $name_gen;
      $image->move(public_path('upload/category'), $name_gen); // Move the file directly
      $save_url = $save_path;

      $category->update([
        'category_name' => $request->category_name,
        'category_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
        'image' => $save_url,
      ]);

      return redirect()->route('admin.all.category')->with([
        'message' => 'Category Updated with Image Successfully',
        'alert-type' => 'success'
      ]);
    } else {
      $category->update([
        'category_name' => $request->category_name,
        'category_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
      ]);

      return redirect()->route('admin.all.category')->with([
        'message' => 'Category Updated Successfully',
        'alert-type' => 'success'
      ]);
    }
  }

  public function DeleteCategory($id)
  {
    $category = Category::findOrFail($id);

    if (File::exists(public_path($category->image))) {
      File::delete(public_path($category->image));
    }

    $category->delete();

    return redirect()->route('admin.all.category')->with([
      'message' => 'Category Deleted Successfully',
      'alert-type' => 'success'
    ]);
  }

  // SubCategory Methods (unchanged since they don’t involve images)
  public function AllSubCategory()
  {
    $subcategories = SubCategory::latest()->get();
    return view('admin.backend.subcategory.all_subcategory', compact('subcategories'));
  }

  public function AddSubCategory()
  {
    $categories = Category::latest()->get();
    return view('admin.backend.subcategory.add_subcategory', compact('categories'));
  }

  public function StoreSubCategory(Request $request)
  {
    $request->validate([
      'category_id' => 'required|exists:categories,id',
      'subcategory_name' => 'required|string|max:255|unique:sub_categories,subcategory_name',
    ]);

    SubCategory::create([
      'category_id' => $request->category_id,
      'subcategory_name' => $request->subcategory_name,
      'subcategory_slug' => strtolower(str_replace(' ', '-', $request->subcategory_name)),
    ]);

    return redirect()->route('admin.all.subcategory')->with([
      'message' => 'SubCategory Inserted Successfully',
      'alert-type' => 'success'
    ]);
  }

  public function EditSubCategory($id)
  {
    $categories = Category::latest()->get();
    $subcategory = SubCategory::findOrFail($id);
    return view('admin.backend.subcategory.edit_subcategory', compact('categories', 'subcategory'));
  }

  public function UpdateSubCategory(Request $request)
  {
    $subcat_id = $request->id;
    $request->validate([
      'category_id' => 'required|exists:categories,id',
      'subcategory_name' => 'required|string|max:255|unique:sub_categories,subcategory_name,' . $subcat_id,
    ]);

    SubCategory::findOrFail($subcat_id)->update([
      'category_id' => $request->category_id,
      'subcategory_name' => $request->subcategory_name,
      'subcategory_slug' => strtolower(str_replace(' ', '-', $request->subcategory_name)),
    ]);

    return redirect()->route('admin.all.subcategory')->with([
      'message' => 'SubCategory Updated Successfully',
      'alert-type' => 'success'
    ]);
  }

  public function DeleteSubCategory($id)
  {
    SubCategory::findOrFail($id)->delete();

    return redirect()->route('admin.all.subcategory')->with([
      'message' => 'SubCategory Deleted Successfully',
      'alert-type' => 'success'
    ]);
  }
}
