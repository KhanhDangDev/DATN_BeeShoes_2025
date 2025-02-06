<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateMaterialRequest;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
        
            $materials = Material::query()->get();

            if ($materials->isEmpty()) {
                return response()->json([
                    'message' => 'Không tìm thấy danh sách màu sắc',
                    'data' => [],
                ], Response::HTTP_OK);
            }
    
           return ApiResponse::responseObject($materials);
    
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
    public function update(UpdateMaterialRequest $request, string $id)
     {
        
         $brand = Material::find($id);
     
        try {
            if (!$brand) {
                return response()->json([
                    'message' => 'Chat liệu không tồn tại.',
                ], Response::HTTP_NOT_FOUND);
            }
        
            if ($request->has('ma_chat_lieu')) {
                $brand->ma_chat_lieu = $request->ma_chat_lieu;
            }
   
            if ($request->has('ten_chat_lieu')) {
                $brand->ten_chat_lieu = $request->ten_chat_lieu;
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
