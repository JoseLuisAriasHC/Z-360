<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                if ($e instanceof HttpResponseException) {
                    return response()->json([
                        'success' => false,
                        'error'   => 'No autenticado. Debes iniciar sesión o enviar un token válido.'
                    ], 401);
                }

                if ($e instanceof NotFoundHttpException) {
                    return response()->json([
                        'success' => false,
                        'error'   => 'El recurso solicitado no existe.'
                    ], 404);
                }

                if ($e instanceof ValidationException) {
                    return response()->json([
                        'success' => false,
                        'errors' => $e->errors()
                    ], 422);
                }

                $status = 500;
                $message = 'Error en la API';

                if ($e instanceof HttpExceptionInterface) {
                    $status = $e->getStatusCode();
                    $message = $e->getMessage() ?: 'Error HTTP';
                }

                \Log::error('Excepción capturada', [
                    'tipo' => get_class($e),
                    'mensaje' => $e->getMessage(),
                    'status' => method_exists($e, 'getStatusCode') ? $e->getCode() : null,
                ]);

                return response()->json([
                    'success' => false,
                    'error' => $message,
                ], $status);
            }
        });
    }
}
