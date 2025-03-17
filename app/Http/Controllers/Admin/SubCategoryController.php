<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;

class SubCategoryController extends Controller
{
  public function index()
  {
    $subcategories = SubCategory::latest()->get();
    return view('admin.backend.subcategory.index', compact('subcategories'));
  }

  public function create()
  {
    $categories = Category::latest()->get();
    return view('admin.backend.subcategory.create', compact('categories'));
  }

  public function store(Request $request)
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

    return redirect()->route('admin.subcategories.index')->with([
      'message' => 'SubCategory Inserted Successfully',
      'alert-type' => 'success'
    ]);
  }

  public function show($id)
  {
    $subcategory = SubCategory::findOrFail($id);
    return view('admin.backend.subcategory.show', compact('subcategory'));
  }

  public function edit($id)
  {
    $categories = Category::latest()->get();
    $subcategory = SubCategory::findOrFail($id);
    return view('admin.backend.subcategory.edit', compact('categories', 'subcategory'));
  }

  public function update(Request $request, $id)
  {
    $request->validate([
      'category_id' => 'required|exists:categories,id',
      'subcategory_name' => 'required|string|max:255|unique:sub_categories,subcategory_name,' . $id,
    ]);

    SubCategory::findOrFail($id)->update([
      'category_id' => $request->category_id,
      'subcategory_name' => $request->subcategory_name,
      'subcategory_slug' => strtolower(str_replace(' ', '-', $request->subcategory_name)),
    ]);

    return redirect()->route('admin.subcategories.index')->with([
      'message' => 'SubCategory Updated Successfully',
      'alert-type' => 'success'
    ]);
  }

  public function destroy($id)
  {
    SubCategory::findOrFail($id)->delete();

    return redirect()->route('admin.subcategories.index')->with([
      'message' => 'SubCategory Deleted Successfully',
      'alert-type' => 'success'
    ]);
  }

  // public function getSubcategories($category_id)
  // {
  //   $subcategories = SubCategory::where('category_id', $category_id)->get(['id', 'subcategory_name']);
  //   return response()->json($subcategories);
  // }
}
