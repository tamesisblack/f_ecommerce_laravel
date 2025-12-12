<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Manejo de excepciones de validación
        if ($exception instanceof ValidationException) {
            return response()->json([
                'message' => $exception->getMessage(),
                'statusCode' => 422,
                'errors' => $exception->errors()
            ], 422);
        }

        // Manejo de excepciones HTTP (404, 403, etc.)
        if ($exception instanceof HttpExceptionInterface) {
            return response()->json([
                'message' => $exception->getMessage() ?: 'HTTP Error',
                'statusCode' => $exception->getStatusCode(),
            ], $exception->getStatusCode());
        }

        // Para requests que esperan JSON (API), devolver JSON
        if ($request->expectsJson()) {
            return response()->json([
                'message' => app()->environment('local') ? $exception->getMessage() : 'Internal Server Error',
                'statusCode' => 500,
            ], 500);
        }

        // Para requests web normales, usar el comportamiento por defecto de Laravel
        return parent::render($request, $exception);
    }
}
