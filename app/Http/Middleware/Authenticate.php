<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Determine the guard based on the request path
        if ($request->is('admin/*')) {
            return route('admin.login');
        } elseif ($request->is('instructor/*')) {
            return route('instructor.login');
        }

        // Default to user login for other routes
        return route('login');
    }
}