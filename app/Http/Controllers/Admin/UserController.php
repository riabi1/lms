<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        // Fetch all users with a count of their purchased courses
        $users = User::withCount('orders')->get();
        
        return view('admin.Users.index', compact('users'));
    }
}