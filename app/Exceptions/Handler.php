<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    // ── Exceptions that are never reported to logs ─────────────
    protected $dontReport = [
        //
    ];

    // ── Inputs that are never included in exception context ────
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    // ── Register exception handling callbacks ───────────────────
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // You can add Sentry, Bugsnag, or other error
            // tracking services here in production
        });

        // ── Handle ModelNotFoundException (404) ─────────────────
        // When route model binding fails to find a record
        // e.g. /boards/999 where board 999 does not exist
        $this->renderable(function (ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The requested resource was not found.',
                ], 404);
            }

            return response()->view('errors.404', [], 404);
        });

        // ── Handle NotFoundHttpException (404) ──────────────────
        // When a route simply does not exist
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The requested URL was not found.',
                ], 404);
            }

            return response()->view('errors.404', [], 404);
        });

        // ── Handle AccessDeniedHttpException (403) ──────────────
        // When a Policy returns false and authorize() is called
        $this->renderable(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'You do not have permission to perform this action.',
                ], 403);
            }

            return response()->view('errors.403', [], 403);
        });

        // ── Handle TokenMismatchException (419) ─────────────────
        // When the CSRF token has expired
        $this->renderable(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your session has expired. Please refresh and try again.',
                ], 419);
            }

            return response()->view('errors.419', [], 419);
        });

        // ── Handle ValidationException ───────────────────────────
        // When a Form Request fails validation
        // For JSON requests return errors as structured JSON
        $this->renderable(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The given data was invalid.',
                    'errors'  => $e->errors(),
                ], 422);
            }
            // For normal requests let Laravel handle it
            // (redirects back with errors in session)
        });

        // ── Handle AuthenticationException (401) ─────────────────
        // When an unauthenticated user tries to access a
        // protected route — redirect to login for web,
        // return JSON for API/AJAX requests
        $this->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to perform this action.',
                ], 401);
            }

            return redirect()->guest(route('login'));
        });

        // ── Handle generic HttpException ─────────────────────────
        // Catches any other HTTP error codes thrown via abort()
        $this->renderable(function (HttpException $e, Request $request) {
            $statusCode = $e->getStatusCode();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'An error occurred.',
                ], $statusCode);
            }

            // Render specific error view if it exists,
            // otherwise fall back to a generic error view
            $view = "errors.{$statusCode}";

            if (view()->exists($view)) {
                return response()->view($view, [
                    'exception' => $e,
                ], $statusCode);
            }

            // Fallback to 500 page for unknown HTTP errors
            return response()->view('errors.500', [
                'exception' => $e,
            ], $statusCode);
        });
    }
}
