<?php
namespace App\Http\Controllers\Auth\Admin;

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
        return view('admin.admin-register');
    }

    /**
     * Handle an incoming admin registration request.
     */
    public function store(Request $request)
    {
      // Define the secret code (you can move this to config or .env)
      $secretCode = env('ADMIN_SECRET_CODE', 'admin123'); // Default: 'admin123'

      // Validate the request
      $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
        'secret_code' => ['required', 'string', function ($attribute, $value, $fail) use ($secretCode) {
          if ($value !== $secretCode) {
            $fail('The secret code is incorrect.');
          }
        }],
        'password' => ['required', 'string', 'confirmed'],
      ]);

      // Create the admin user
      $admin = Admin::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
      ]);

        event(new Registered($admin)); 

        return redirect()->route('admin.verification.notice')->with('status', 'Veuillez vérifier votre email pour compléter l’inscription.');
    }
}