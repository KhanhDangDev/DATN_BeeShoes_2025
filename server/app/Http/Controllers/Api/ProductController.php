<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\RestApiException;
use App\Helpers\ApiResponse;
use App\Helpers\ProductCodeHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\AttributeRequestBody;
use App\Http\Requests\Product\ProductRequestBody;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // sử dụng DB Facades để thực hiện sql
use App\Models\Product;
use App\Models\Size;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $listSanPham = Product::query();



        // Tìm kiếm theo từ khóa
        if ($request->filled('tuKhoa')) {
            $tuKhoa = '%' . $request->tuKhoa . '%';
            $listSanPham->where(function ($query) use ($tuKhoa) {
                return $query->where('ma_san_pham', 'like', $tuKhoa)->orWhere('ten_san_pham', 'like', $tuKhoa);
            });
        }

        // lọc theo trạng thái
        if ($request->filled('trangThai')) {
            $listSanPham->where('trang_thai', '=', $request->trangThai);
        }

        // Sắp xếp sản phẩm mới nhất
        $listSanPham->orderBy('ngay_tao', 'desc');

        // Phân trang.
        $responsePagePaginate = $listSanPham->paginate(10, ['*']); // ['*']: để lấy tất cả các cột trong csdl.

        return ApiResponse::responsePage(ProductResource::collection($responsePagePaginate), $responsePagePaginate);
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
    public function store(ProductRequestBody $request)
    {
        $maSanPhamDaTonTai = Product::where('ma_san_pham', '=', $request->maSanPham)->first();


        if ($maSanPhamDaTonTai) {
            throw new RestApiException('Mã sản phẩm đã tồn tại!');
        }

        // $maMoi = ProductCodeHelper::taoMaSanPham(Product::query(), 'SKU');

        $sanPhamMoi = new Product();

        // $sanPhamMoi->ma_san_pham = $maMoi;
        $sanPhamMoi->ma_san_pham =  $request->maSanPham;
        $sanPhamMoi->ten_san_pham = $request->tenSanPham;
        $sanPhamMoi->mo_ta = $request->moTa;
        $sanPhamMoi->ngay_tao = $request->ngayTao;
        $sanPhamMoi->don_gia = $request->donGia;
        $sanPhamMoi->trang_thai = $request->trangThai;
        $sanPhamMoi->id_mau_sac = $request->idMauSac;
        $sanPhamMoi->id_chat_lieu = $request->idChatLieu;
        $sanPhamMoi->id_thuong_hieu = $request->idThuongHieu;

        $sanPhamMoi->save();

        // return response()->json($sanPhamMoi);
        return ApiResponse::responseObject($sanPhamMoi);
    }

    public function storeKichCo(AttributeRequestBody $request)
    {
        foreach ($request->listKichCo as $kichCo) {
            if (is_string($kichCo) && !empty($kichCo)) { // Kiểm tra nếu là chuỗi và không rỗng.
                $kichCoMoi = new Size();
                $kichCoMoi->ten_kich_co = $kichCo;
                $kichCoMoi->id_san_pham = $request->id; // Lấy id trên đường dẫn.
                $kichCoMoi->save();
            }
        }

        // Lấy danh sách các kích cỡ của sản phẩm dựa trên ID sản phẩm từ yêu cầu
        $listKichCoCuaSanPham = Size::where('id_san_pham', '=', $request->id)->orderBy('ten_kich_co', 'asc')->get();

        return ApiResponse::responseObject($listKichCoCuaSanPham);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $timSanPham = Product::findOrFail($id); // tự động ném lỗi 404 nếu không tìm thấy

        // Trả về sản phẩm dưới dạng JSON qua ProductResource
        return ApiResponse::responseObject(new ProductResource($timSanPham));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequestBody $request)
    {
        // json_decode() chuyển đổi chuỗi JSON thành một đối tượng hoặc mảng PHP.
        // $data = json_decode($request->data);

        // $timSanPham = Product::find($data->id);

        // if (!$timSanPham) {
        //     throw new RestApiException('Không tìm thấy sản phẩm có id là ' . $data->id);
        // }

        $timSanPham = Product::find($request->id);

        if (!$timSanPham) {
            throw new RestApiException('Không tìm thấy sản phẩm có id là ' . $request->id);
        }

        if ($request->tenSanPham !== $timSanPham->ten_san_pham) {
            $tenPhamDaTonTai = Product::where('ten_san_pham', '=', $request->tenSanPham)->first();

            if ($tenPhamDaTonTai) {
                throw new RestApiException("Tên sản phẩm này tồn tại!");
            }
        }

        if ($request->maSanPham !== $timSanPham->ma_san_pham) {
            $maPhamDaTonTai = Product::where('ma_san_pham', '=', $request->maSanPham)->first();

            if ($maPhamDaTonTai) {
                throw new RestApiException("Mã sản phẩm này tồn tại!");
            }
        }


        $timSanPham->ma_san_pham = $request->maSanPham;
        $timSanPham->ten_san_pham = $request->tenSanPham;
        $timSanPham->mo_ta = $request->moTa;
        $timSanPham->ngay_tao = $request->ngayTao;
        $timSanPham->don_gia = $request->donGia;
        $timSanPham->trang_thai = $request->trangThai;
        $timSanPham->id_mau_sac = $request->idMauSac;
        $timSanPham->id_chat_lieu = $request->idChatLieu;
        $timSanPham->id_thuong_hieu = $request->idThuongHieu;

        $timSanPham->update();

        return ApiResponse::responseObject($timSanPham);
    }

    public function updateSoluongKichCo(Request $request)
    {
        $timKichCo = Size::find($request->id);

        if (!$timKichCo) {
            throw new RestApiException('Không tìm thấy kích cỡ có id là ' . $request->id);
        }

        // Nếu tìm thấy kích cỡ, cập nhật số lượng tồn
        if ($timKichCo) {
            $timKichCo->so_luong_ton = $request->soLuongTon;
            $timKichCo->save();
        }

        // Lấy danh sách các kích cỡ của sản phẩm dựa trên ID sản phẩm từ yêu cầu
        $listKichCoCuaSanPham = Size::where('id_san_pham', '=', $request->id)->orderBy('ten_kich_co', 'asc')->get();

        return ApiResponse::responseObject($listKichCoCuaSanPham);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $timSanPham = Product::find($id);

        if (!$timSanPham) {
            throw new RestApiException('Không tìm thấy sản phẩm có id là ' . $id);
        }

        $timSanPham->delete();
        return ApiResponse::responseSuccess('Sản phẩm đã được xóa thành công là ' . $id);
    }
}
