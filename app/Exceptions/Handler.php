<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Constants\MessageConstants;

class Handler extends ExceptionHandler
{
    /**
     * The list of inputs that should not be flashed to the session
     * on validation exceptions for security reasons.
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
            // Implement logic for reporting exceptions (e.g., logging, sending to a monitoring service)
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        // Custom handling for MethodNotAllowedHttpException
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'success' => false,
                'errors' => ['Method not allowed: ' . $exception->getMessage()]
            ], 405);
        }

        // Custom handling for ModelNotFoundException
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'errors' => [MessageConstants::NOT_FOUND]
            ], 404);
        }

        // Default handling for other exceptions
        return parent::render($request, $exception);
    }
}
