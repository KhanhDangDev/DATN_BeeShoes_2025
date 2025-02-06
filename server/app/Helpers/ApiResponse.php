<?php

namespace App\Helpers;

class ApiResponse
{
    public static function responseError($code = 500, $errorMessage = '', $message = '')
    {
        $response = [
            'status' => $code,
            'message' => $message,
            'error_message' => $errorMessage,
        ];

        return response()->json($response, $code);
    }

    public static function responseObject($data, $statusCode = 200, $message = 'success')
    {
        $response = [
            'code' => $statusCode,
            'message' => $message,
            'data' => $data
        ];

        return response()->json($response, $statusCode);
    }

    public static function responsePage($page)
    {
        $response = [
            'success' => true,
            'status' => 'success',
            'data' => $page->item(),
            'page' => [
                'currentPage' => $page->currentPage(),
                'lastPage' => $page->lastPage(),
                'pageSize' => $page->perPage(), // số bản ghi mỗi trang
                'totalElement' => $page->total() // tổng số bản ghi
            ]

        ];
        return response()->json($response, 200);
    }

    public static function responseSuccess($message = '')
    {
        $response = [
            'status' => 'success',
            'message' => $message
        ];

        return response()->json($response, 200);
    }
}
