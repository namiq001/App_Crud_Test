<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // burda utype admin olmagin yoxlayiriq eger utype admin dirse sorquya davam etmeye icaze verir 
        if (Auth::check() && Auth::user()->utype === 'ADMIN') {
            return $next($request);
        } else {
            // admin deyilse icaze vermir
            return redirect()->route('login')->with('error', 'You must be an administrator to access this page.');
        }
    }
}
