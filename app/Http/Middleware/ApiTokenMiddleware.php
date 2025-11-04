<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\User;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken() ?? $request->header('X-API-TOKEN');

        if (!$token) {
            return response()->json([
                'error' => 'API token required',
                'message' => 'Please provide API token in Authorization header or X-API-TOKEN header'
            ], 401);
        }

        // Find user by api_token
        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json([
                'error' => 'Invalid API token',
                'message' => 'The provided API token is invalid'
            ], 401);
        }

        // Set authenticated user
        auth()->setUser($user);

        // Continue with request
        $response = $next($request);

        // Add CORS headers for API
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-API-TOKEN, X-Requested-With');

        return $response;
    }
}