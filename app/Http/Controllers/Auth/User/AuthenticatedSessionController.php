<?php
namespace App\Http\Controllers\Auth\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('User.login');
    }
  
    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
            
            // Merge guest cart into authenticated user's cart
            $cartController = new CartController();
            $cartController->mergeGuestCart();
            
            // Check for redirect query parameter
            $intendedUrl = $request->query('redirect', route('dashboard'));
            $request->session()->put('url.intended', $intendedUrl);
            
            return redirect()->intended($intendedUrl)->with('cart_added_message', 'Cart items have been merged successfully.');
        }
        return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}