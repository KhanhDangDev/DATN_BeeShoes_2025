<?php

namespace App\Traits;

use App\Helpers\ApiResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Collection;

trait ApiDataTrait
{
    public function getAllData(Model $model, $message = "Danh sách ", $relations = [], array $filterableFields = [], array $dates = [])
    {
        try {
            $filters = request()->query();

            $query = $model::with($relations);

            foreach ($filters as $field => $value) {

                if (!empty($value)) {

                    $column = \Str::snake($field);

                    if (in_array($column, $filterableFields)) {

                        if (\Str::startsWith($column, 'ten_')) {

                            $query->where($column, 'like', "%$value%");
                        } else {
                            $query->where($column, $value);
                        }
                    }
                }
            }
            if (!empty($filters['tuKhoa'])) {
                $query->where(function ($q) use ($filters, $filterableFields) {
                    foreach ($filterableFields as $field) {
                        $q->orWhere($field, 'like', "%{$filters['tuKhoa']}%");
                    }
                });
            }
            foreach ($dates as $date) {
                if (isset($filters['start_date']) && isset($filters['end_date'])) {

                    $query->whereBetween($date, [$filters['start_date'], $filters['end_date']]);
                } elseif (isset($filters['from_date'])) {

                    $query->where($date, '>=', $filters['from_date']);
                } elseif (isset($filters['to_date'])) {

                    $query->where($date, '<=', $filters['to_date']);
                }
            }

            $query->orderBy('created_at', 'desc');
            $perPage = request()->query('per_page', 10);
            $data = $query->paginate($perPage);

            // if ($data->isEmpty()) {
            //     return response()->json([
            //         'message' => 'Không tìm thấy dữ liệu',
            //         'data' => []
            //     ], Response::HTTP_NOT_FOUND);
            // }

            return ApiResponse::responsePage($data);
        } catch (\Exception $e) {
            return ApiResponse::responseError(500, $e->getMessage(), $message);
        }
    }

    public function getDataById(Model $model, $id, $relations = [], $message = "Sản phẩm")
    {
        try {
            $data = $model::with($relations)->findOrFail($id);

            // if (!$data) {
            //     return response()->json([
            //         'message' => 'Khong tim thay du lieu',
            //         'data' => []
            //     ], Response::HTTP_NOT_FOUND);
            // }

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
