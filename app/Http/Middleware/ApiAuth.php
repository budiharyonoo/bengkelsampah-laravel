<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class ApiAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Token not provided.',
            ], 401);
        }

        // Validate token using Sanctum
        $accessToken = PersonalAccessToken::findToken($token);

        if (! $accessToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Invalid token.',
            ], 401);
        }

        // Check if token is expired
        if ($accessToken->expires_at && now()->gt($accessToken->expires_at)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Token has expired.',
            ], 401);
        }

        return $next($request);
    }
}
