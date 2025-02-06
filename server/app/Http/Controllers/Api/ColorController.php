<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateColorRequest;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ColorController extends Controller
{

    public function index()
    {
        try {

            $colors = Color::query()->get();
            
            if ($colors->isEmpty()) {
                return response()->json([
                    'message' => 'Không tìm thấy danh sách màu sắc',
                    'data' => [],
                ], Response::HTTP_OK);
            }

            return ApiResponse::responseObject($colors);

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
    public function update(UpdateColorRequest $request, string $id)
    {
       
        $brand = Color::find($id);
    
       try {
           if (!$brand) {
               return response()->json([
                   'message' => 'Mau sac không tồn tại.',
               ], Response::HTTP_NOT_FOUND);
           }
       
           if ($request->has('ma_mau_sac')) {
               $brand->ma_mau_sac = $request->ma_mau_sac;
           }
  
           if ($request->has('ten_mau_sac')) {
               $brand->ten_mau_sac = $request->ten_mau_sac;
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
