<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSizeRequest;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SizeController extends Controller
{

    public function index()
    {
        try {

            $sizes = Size::query()->get();

            if ($sizes->isEmpty()) {
                return response()->json([
                    'message' => 'Không tìm thấy kích thuocsw.',
                    'data' => [],
                ], Response::HTTP_OK);
            }

            return ApiResponse::responseObject($sizes);

        } catch (\Exception $e) {
            \Log::error('Lỗi: ' . $e->getMessage());

            return ApiResponse::responseError(500, 'server_error', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function update(UpdateSizeRequest $request, string $id)
    {
       
        $brand = Size::find($id);
    
       try {
           if (!$brand) {
               return response()->json([
                   'message' => 'Kich co không tồn tại.',
               ], Response::HTTP_NOT_FOUND);
           }
       
           if ($request->has('ma_kich_co')) {
               $brand->ma_kich_co = $request->ma_kich_co;
           }
  
           if ($request->has('ten_ma_kich_co')) {
               $brand->ten_ma_kich_co = $request->ten_ma_kich_co;
           }
      
           if ($request->has('ngay_tao')) {
               $brand->ngay_tao = $request->ngay_tao;
           }
       
           if ($request->has('trang_thai')) {
               $brand->trang_thai = $request->trang_thai;
           }
       
           $brand->save();
       
           return ApiResponse::responseObject($brand);
       } catch (\Exception $e) {
           \Log::error('Lỗi: ' . $e->getMessage());
   
           return ApiResponse::responseError(500, 'server_error', $e->getMessage());
       }
    }

    public function destroy(string $id)
    {
        //
    }
}
