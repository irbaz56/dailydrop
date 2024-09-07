<?php

namespace App\Exceptions;

use Throwable;
use PDOException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        UnauthorizedHttpException::class,
        NotFoundHttpException::class,
        MethodNotAllowedHttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        AuthenticationException::class,
        OAuthServerException::class,
        BadRequestHttpException::class,
        DuplicateException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     *
     * @throws \Exception
     */
    public function report(Throwable $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
      * @param  \Illuminate\Http\Request $request
     * @param  Throwable $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof NotFoundHttpException) {
            return response()->json(
                [
                    'error' => [
                        'type'    => "not_found",
                        'message' => $e->getMessage(),
                    ],
                ],
                404
            );
        }
        if ($e instanceof ModelNotFoundException) {
                return response()->json(
                    [
                        'error' => [
                            'type'    => "bad_request",
                            'message' => "Request " . str_replace("App\\", "", $e->getModel()) . " resource cannot be found",
                        ],
                    ],
                    400
                );
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->json(
                [
                    'error' => [
                        'type'    => "method_not_allowed",
                        'message' => "{$request->method()} Method not allowed for {$request->path()}",
                    ],
                ],
                405
            );
        }

        if ($e instanceof UnauthorizedHttpException || $e instanceof AuthenticationException) {
            if ($e->getCode() && $e->getCode() != 401) {
                return response()->json(
                    [
                        'error' => [
                            'type'    => "unauthorized",
                            'message' => $e->getMessage(),
                            'code'    => $e->getCode(),
                        ],
                    ],
                    401
                );
            }

            if ($e instanceof AuthenticationException) {
                return response()->json(
                    [
                        'error' => [
                            'type'    => "unauthorized",
                            'message' => 'Invalid or expired token',
                            'code'    => 4011,
                        ],
                    ],
                    401
                );
            }

            return response()->json(
                [
                    'error' => [
                        'type'    => "unauthorized",
                        'message' => $e->getMessage(),
                    ],
                ],
                401
            );
        }
        if ($e instanceof \GuzzleHttp\Exception\RequestException) {
            return response()->json(
                [
                    'error' => [
                        'type'    => 'guzzlehttp_exception',
                        'message' => $e->getMessage(),
                    ],
                ],
                400
            );
        }
        if ($e instanceof BadRequestHttpException) {
            return response()->json(
                [
                    'error' => [
                        'type'    => "bad_request",
                        'message' => $e->getMessage(),
                    ],
                ],
                400
            );
        }

        if ($e instanceof ValidationException) {
            return response()->json(
                [
                    'error'  => [
                        'type'    => "validation",
                        'message' => $e->getMessage(),
                    ],
                    'errors' => $e->validator->errors(),
                ],
                400
            );
        }

        if ($e instanceof PDOException) {
        

            return response()->json(
                [
                    'error' => [
                        'type'    => "api_pdo",
                        'message' => $e->getMessage(),
                    ],
                ],
                400
            );
        }

        if ($e instanceof Exception) {
        
            return response()->json(
                [
                    'error' => [
                        'type'    => "api_unhandled_exception",
                        'message' => $e->getMessage(),
                    ],
                ],
                400
            );
        }

        return parent::render($request, $e);
    }
}
