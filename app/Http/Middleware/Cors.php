<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get origin from request
        $origin = $request->headers->get('Origin');
        
        // Allowed origins (bisa diubah ke config atau env jika perlu)
        $allowedOrigins = [
            'https://app.bengkelsampah.com',
            'http://localhost',
            'http://localhost:8080',
            'http://127.0.0.1:8080',
        ];
        
        // Determine allowed origin
        // If origin exists and is in allowed list, use it (enables credentials)
        // If no origin header (same-origin request), use wildcard
        // If origin exists but not in list, use wildcard (for development flexibility)
        $allowedOrigin = '*';
        $useCredentials = false;
        
        if ($origin && in_array($origin, $allowedOrigins)) {
            $allowedOrigin = $origin;
            $useCredentials = true;
        } elseif (!$origin) {
            // Same-origin request, no CORS needed but we'll still set headers
            $allowedOrigin = '*';
            $useCredentials = false;
        }

        // Handle OPTIONS request (preflight)
        if ($request->isMethod('OPTIONS')) {
            $response = response('', 204)
                ->header('Access-Control-Allow-Origin', $allowedOrigin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, Accept, Origin')
                ->header('Access-Control-Max-Age', '86400');
            
            // CRITICAL: Only add credentials if NOT using wildcard
            if ($useCredentials) {
                $response->header('Access-Control-Allow-Credentials', 'true');
            }
            
            return $response;
        }

        $response = $next($request);

        // Add CORS headers to response
        $response->header('Access-Control-Allow-Origin', $allowedOrigin);
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
        $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, Accept, Origin');
        
        // CRITICAL: Only add credentials header if NOT using wildcard
        // Browser will reject response if both wildcard (*) and credentials (true) are present
        if ($useCredentials) {
            $response->header('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }
}

