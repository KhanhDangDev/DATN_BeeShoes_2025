<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            return ApiResponse::responseError(
                404,
                'end_point_not_found',
                'Đường dẫn api không tồn tại.'
            );
        } else if ($exception instanceof RestApiException) {
            return ApiResponse::responseError(
                400,
                'bad_request',
                $exception->getMessage(),
            );
        } else if ($exception instanceof NotFoundException) {
            return ApiResponse::responseError(
                404,
                'not_found',
                $exception->getMessage(),
            );
        } else if ($exception instanceof Exception) {
            return ApiResponse::responseError(
                500,
                'server_error',
                $exception->getMessage(),
            );
        }

        // Nếu không phải lỗi trên, gọi phương thức render() mặc định của Laravel để xử lý các lỗi còn lại
        return parent::render($request, $exception);
    }
}
