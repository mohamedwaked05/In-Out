<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{

    public function handle(Request $request, Closure $next, string $role): Response
    {
 try {
        // your existing code
    } catch (\Exception $e) {
        dd($e->getMessage()); // This will show the error
    }
        // Check if user is authenticated and has the required role
        if (!Auth::check() || Auth::user()->role !== $role) {
            // Redirect based on user's actual role
            return match(Auth::user()->role ?? null) {
                'manager' => redirect()->route('manager.dashboard'),
                'employee' => redirect()->route('employee.dashboard'),
                default => redirect('/dashboard'),
            };
        }

        return $next($request);
    }
}
