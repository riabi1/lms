<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminRegisteredUserController extends Controller
{
    /**
     * Display the admin registration view.
     */
    public function create()
    {
        return view('auth.admin-register');
    }

    /**
     * Handle an incoming admin registration request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($admin)); // Envoie l'email de vérification, comme pour les utilisateurs

        return redirect()->route('admin.verification.notice')->with('status', 'Veuillez vérifier votre email pour compléter l’inscription.');
    }
}