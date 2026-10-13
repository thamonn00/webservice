<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
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

    public function register()
    {
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => 'กรุณา Login ก่อน แล้วส่ง Header: Authorization: Bearer <token>',
                    'data' => null,
                ], 401);
            }
        });

        $this->renderable(function (AuthorizationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage() ?: 'ไม่มีสิทธิ์ดำเนินการ',
                    'data' => null,
                ], 403);
            }
        });

        $this->renderable(function (HttpExceptionInterface $e, $request) {
            if ($request->is('api/*')) {
                $status = $e->getStatusCode();

                if ($status === 403 || $status === 404) {
                    return response()->json([
                        'status' => false,
                        'message' => $status === 403 ? 'ไม่มีสิทธิ์ดำเนินการ' : 'ไม่พบข้อมูล',
                        'data' => null,
                    ], $status);
                }
            }
        });
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            'status' => false,
            'message' => 'ข้อมูลไม่ถูกต้อง',
            'data' => null,
            'errors' => $exception->errors(),
        ], $exception->status);
    }

    protected function shouldReturnJson($request, Throwable $e)
    {
        return $request->is('api/*') || parent::shouldReturnJson($request, $e);
    }
}
