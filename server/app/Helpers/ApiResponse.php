<?php

namespace App\Helpers;

class ApiResponse
{
    public static function responseError($code = 500, $message = '', $error = '')
    {
        $response = [
            'status' => $code,
            'error' => $error,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    public static function responseObject($data)
    {

        // if ($data === null || empty($data)) {
        //     // Nếu không có dữ liệu, trả về thông báo lỗi
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'No data found'
        //     ], 404);  // Sử dụng mã trạng thái 404 nếu không tìm thấy dữ liệu
        // }

        $response = [
            'success' => true,
            'status' => 'success',
            'data' => $data
        ];

        return response()->json($response, 200);
    }

    public static function responsePage($page)
    {
        $response = [
            'success' => true,
            'status' => 'success',
            'data' => $page->items(),
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
