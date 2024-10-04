<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthAdminOrStaff
{
    // public function handle(Request $request, Closure $next)
    // {
    //     if (Auth::check() && (Auth::user()->usertype === 'ADMIN' || Auth::user()->usertype === 'STAFF')) {
    //         return $next($request);
    //     }

    //     return redirect()->route('login');
    // }
}
