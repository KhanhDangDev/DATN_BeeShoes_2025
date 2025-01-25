<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SanPhamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $sanPham = QueryBuilder::for(SanPham::class)
                ->allowedFilters([
                    AllowedFilter::exact('ma_san_pham'),
                    AllowedFilter::exact('id_mau_sac'),
                    AllowedFilter::exact('id_thuong_hieu'),
                    AllowedFilter::partial('ten_san_pham'),
                    AllowedFilter::scope('don_gia_min'),
                    AllowedFilter::scope('don_gia_max'),
                ])
                ->allowedSorts('don_gia', 'ten_san_pham')
                ->paginate($request->input('page', 10));

            return response()->json([
                'message' => 'Danh sách sản phẩm',
                'data' => $sanPham
            ],Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi thử lại',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
