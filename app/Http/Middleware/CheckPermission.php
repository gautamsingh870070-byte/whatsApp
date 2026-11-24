<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        $user = Auth::user();

        if (!$user) {
            return $this->forbiddenResponse($request, 'Unauthenticated.');
        }

        if (!$user->hasPermission($permission)) {
            return $this->forbiddenResponse($request, 'You do not have permission to perform this action.');
        }

        return $next($request);
    }

    private function forbiddenResponse(Request $request, $message)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $message
            ], 403);
        }

        abort(403, $message);
    }
}
