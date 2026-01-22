<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception): Response
    {
        // Force JSON response for validation errors on API routes
        if ($exception instanceof ValidationException && $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'data' => $exception->errors(),
                'timestamp' => now()->toIso8601String(),
            ], $exception->status);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, AuthenticationException $exception): Response
    {
        // Always return JSON for API routes
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage() ?: 'Unauthenticated',
                'data' => null,
                'timestamp' => now()->toIso8601String(),
            ], 401);
        }

        return redirect()->guest(route('login'));
    }

    /**
     * Convert a validation exception into a JSON response.
     */
    protected function invalidJson($request, ValidationException $exception): Response
    {
        return response()->json([
            'success' => false,
            'message' => $exception->getMessage(),
            'data' => $exception->errors(),
            'timestamp' => now()->toIso8601String(),
        ], $exception->status);
    }
}
