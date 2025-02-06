<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\UpdateBrandRequest;

class BrandController extends Controller
{
    public function index()
    {
        try {
        
            $brands = Brand::get();

            if ($brands->isEmpty()) {
                return response()->json([
                    'message' => 'Không tìm thấy danh sách thương hiệu ',
                    'data' => [],
                ], Response::HTTP_OK);
            }
    
        return ApiResponse::responseObject($brands);

        } catch (\Exception $e) {
            \Log::error('Lỗi: ' . $e->getMessage());
    
        return ApiResponse::responseError(500, 'server_error', $e->getMessage());

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    
     public function update(UpdateBrandRequest $request, string $id)
     {
        
         $brand = Brand::find($id);
     
        try {
            if (!$brand) {
                return response()->json([
                    'message' => 'Thương hiệu không tồn tại.',
                ], Response::HTTP_NOT_FOUND);
            }
        
            if ($request->has('ma_thuong_hieu')) {
                $brand->ma_thuong_hieu = $request->ma_thuong_hieu;
            }
   
            if ($request->has('ten_thuong_hieu')) {
                $brand->ten_thuong_hieu = $request->ten_thuong_hieu;
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
     
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
