<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
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
    public function render($request, Throwable $exception)
    {
        // تحقق من نوع الاستثناء
        if ($exception instanceof ValidationException) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $exception->errors(),
            ], 422);
        }

        // تحقق من نوع الاستثناء العام
        if ($exception instanceof \Exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }

        // استدعاء الدالة الأصلية لمعالجة الاستثناءات الأخرى
        return parent::render($request, $exception);
    }
}
