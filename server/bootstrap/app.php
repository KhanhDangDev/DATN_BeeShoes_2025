<?php

use App\Exceptions\NotFoundException;
use App\Exceptions\RestApiException;
use App\Helpers\ApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exception) {
        // RestApiException
        $exception->renderable(function (RestApiException $exception) {
            return ApiResponse::responseError(
                400,
                'bad_request',
                $exception->getMessage(),
            );
        });

        // NotFoundHttpException
        $exception->renderable(function (NotFoundHttpException $exception) {
            return ApiResponse::responseError(
                404,
                'end_point_not_found',
                'Đường dẫn api không tồn tại.'
            );
        });

        // NotFoundException
        $exception->renderable(function (NotFoundException $exception) {
            return ApiResponse::responseError(
                404,
                'not_found',
                $exception->getMessage(),
            );
        });

        // Exception
        $exception->renderable(function (Exception $exception) {
            return ApiResponse::responseError(
                500,
                'server_error',
                $exception->getMessage(),
            );
        });
    })->create();
