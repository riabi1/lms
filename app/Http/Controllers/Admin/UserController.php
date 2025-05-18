<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;

class UserController extends Controller
{
    public function index()
    {
        // Fetch all users with a count of their purchased courses
        $users = User::withCount('orders')->get();
        // Fetch all categories to map preference IDs to names
        $categories = Category::all()->keyBy('id'); // Key by ID for easy lookup
        
        return view('admin.Users.index', compact('users', 'categories'));
    }
}