<?php

namespace App\Traits;

use App\Helpers\ApiResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Collection;

trait ApiDataTrait
{
    public function getAllData(Model $model, $message = "Danh sách ", $relations = [])
    {
        try {

            $data = $model::with($relations)->get();

            if ($data->isEmpty()) {

                return response()->json([
                    'message' => 'Khong tim thay du lieu',
                    'data' => []
                ], Response::HTTP_NOT_FOUND);
            }

            return ApiResponse::responseObject($data, Response::HTTP_OK, $message);
        } catch (\Exception $e) {
            return ApiResponse::responseError(500, $e->getMessage(), $message);
        }
    }

    public function getDataById(Model $model, $id, $relations = [], $message = "Sản phẩm")
    {
        try {
            $data = $model::with($relations)->findOrFail($id);

            if (!$data) {
                return response()->json([
                    'message' => 'Khong tim thay du lieu',
                    'data' => []
                ], Response::HTTP_NOT_FOUND);
            }

            return ApiResponse::responseObject($data, Response::HTTP_OK, $message);
        } catch (\Exception $e) {

            return ApiResponse::responseError(500, $e->getMessage(), $message);
        }
    }
    public function processUpdateStatus($model, $id, $status)
    {
        try {

            $data = $model::find($id);

            if (!$data) {
                return response()->json([
                    'message' => 'Không tìm thấy dữ liệu',
                ], Response::HTTP_NOT_FOUND);
            }

            if (!in_array($status, ["dang_hoat_dong", "ngung_hoat_dong"])) {
                return response()->json([
                    'message' => 'Trạng thái không hợp lệ',
                ], Response::HTTP_BAD_REQUEST);
            }

            $data->trang_thai = $status;
            $data->save();

            return ApiResponse::responseSuccess("Cập nhật trạng thái thành công");
        } catch (\Exception $e) {
            \Log::error("Lỗi: " . $e->getMessage());

            return ApiResponse::responseError(500, $e->getMessage());
        }
    }
}
