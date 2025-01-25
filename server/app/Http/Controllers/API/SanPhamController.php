<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SanPhamController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ma_san_pham' => 'nullable|string|max:20',
            'ten_san_pham' => 'nullable|string|max:255',
            'chat_lieu' => 'nullable|uuid|exists:chat_lieu,id',
            'kich_co' => 'nullable|uuid|exists:kich_co,id',
            'mau_sac' => 'nullable|uuid|exists:mau_sac,id',
            'don_gia_min' => 'nullable|numeric|min:0',
            'don_gia_max' => 'nullable|numeric|min:0|gte:don_gia_min',
            'trang_thai' => 'nullable|in:dang_hoat_dong,ngung_hoat_dong',
            'sort_by' => 'nullable|in:don_gia,ten_san_pham',
            'sort_order' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        try {

            $sanPham = SanPham::query()
                ->when($request->ma_san_pham, function ($query) use ($request) {
                    $query->where('ma_san_pham', 'like', '%' . $request->ma_san_pham . '%');
                })
                ->when($request->ten_san_pham, function ($query) use ($request) {
                    $query->where('ten_san_pham', 'like', '%' . $request->ten_san_pham . '%');
                })
                ->when($request->chat_lieu, function ($query) use ($request) {
                    $query->where('id_chat_lieu', $request->chat_lieu);
                })
                ->when($request->kich_co, function ($query) use ($request) {
                    $query->where('id_kich_co', $request->kich_co);
                })
                ->when($request->mau_sac, function ($query) use ($request) {
                    $query->where('id_mau_sac', $request->mau_sac);
                })
                ->when($request->don_gia_min, function ($query) use ($request) {
                    $query->where('don_gia', '>=', $request->don_gia_min);
                })
                ->when($request->don_gia_max, function ($query) use ($request) {
                    $query->where('don_gia', '<=', $request->don_gia_max);
                })
                ->when($request->trang_thai, function ($query) use ($request) {
                    $query->where('trang_thai', $request->trang_thai);
                })
                ->when($request->sort_by, function ($query) use ($request) {
                    $sortBy = $request->sort_by;
                    if ($sortBy == 'price_asc') {
                        $query->orderBy('don_gia', 'asc');
                    } elseif ($sortBy == 'price_desc') {
                        $query->orderBy('don_gia', 'desc');
                    }
                })
                ->paginate($request->per_page ?? 10);

            return response()->json([
                'message' => 'Danh sách sản phẩmm',
                'data' => $sanPham
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi thử lại',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
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
