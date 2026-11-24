<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class JwtSessionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the jwt_token exists in the session
        if (!$request->session()->has('jwt_token')) {
            return redirect('/login')->withErrors(['email' => 'Please log in to access the dashboard.']);
        }

        $token = $request->session()->get('jwt_token');

        try {
            // Authenticate the user using the api guard and the token from the session
            Auth::guard('api')->setToken($token)->authenticate();
            
            // Set the default guard to 'api' so Auth::user() works in web controllers
            Auth::shouldUse('api');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('JWT Session Middleware Error: ' . $e->getMessage());
            // Token is invalid or expired
            $request->session()->forget('jwt_token');
            return redirect('/login')->withErrors(['email' => 'Session expired. Please log in again. (Error: ' . $e->getMessage() . ')']);
        }

        return $next($request);
    }
}
