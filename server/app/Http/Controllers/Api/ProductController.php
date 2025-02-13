<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\RestApiException;
use App\Helpers\ApiResponse;
use App\Helpers\ProductCodeHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\AttributeRequestBody;
use App\Http\Requests\Product\ProductRequestBody;
use App\Http\Requests\Product\SizeRequestBody;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductResourceList;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Material;
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

        $listSanPham = DB::table('san_pham')
            ->join('thuong_hieu', 'san_pham.id_thuong_hieu', '=', 'thuong_hieu.id')
            ->select('san_pham.id', 'san_pham.ma_san_pham', 'san_pham.ten_san_pham', 'san_pham.mo_ta', 'san_pham.don_gia', 'san_pham.trang_thai', 'thuong_hieu.ten_thuong_hieu');
        // Tìm kiếm theo từ khóa

        if ($request->filled('tuKhoa')) {
            $tuKhoa = '%' . $request->tuKhoa . '%';
            $listSanPham->where(function ($query) use ($tuKhoa) {
                return $query->where('ma_san_pham', 'like', $tuKhoa)->orWhere('ten_san_pham', 'like', $tuKhoa);
            });
        }

        // lọc theo trạng thái
        if ($request->filled('trangThai')) {
            $listSanPham->where('san_pham.trang_thai', '=', $request->trangThai);
        }


        $listSanPham->where(function ($q) use ($request) {
            // Loc theo don donGia
            if ($request->has('don_gia_min') && $request->has('don_gia_max')) {
                $q->whereBetween('don_gia', [$request->don_gia_min, $request->don_gia_max]);
            } elseif ($request->has('don_gia_min')) {
                $q->where('don_gia', '>=', $request->don_gia_min);
            } elseif ($request->has('don_gia_max')) {
                $q->where('don_gia', '<=', $request->don_gia_max);
            }

            //Loc theo id thuong hieu
            if ($request->has('idThuongHieu') && $request->idThuongHieu) {
                $q->where('id_thuong_hieu', $request->idThuongHieu);
            }
        });

        $listSanPham->orderBy('san_pham.ngay_tao', 'desc');
        // Phân trang.
        $responsePagePaginate = $listSanPham->paginate(10, ['*']); // ['*']: để lấy tất cả các cột trong csdl.

        return ApiResponse::responsePage(ProductResource::collection($responsePagePaginate));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function indexAttribute()
    {

        $mauSacs = Color::query()->orderBy('ngay_tao', 'desc')->get();
        $thuongHieus = Brand::query()->orderBy('ngay_tao', 'desc')->get();
        $chatLieus = Material::query()->orderBy('ngay_tao', 'desc')->get();

        $attributeArray = [
            "thuongHieu" => $thuongHieus,
            "mauSac" => $mauSacs,
            "chatLieu" => $chatLieus,
        ];

        return ApiResponse::responseObject($attributeArray);
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
        $sanPhamMoi->don_gia = $request->donGia;
        $sanPhamMoi->trang_thai = $request->trangThai;
        $sanPhamMoi->id_mau_sac = $request->idMauSac;
        $sanPhamMoi->id_chat_lieu = $request->idChatLieu;
        $sanPhamMoi->id_thuong_hieu = $request->idThuongHieu;

        $sanPhamMoi->save();

        // return response()->json($sanPhamMoi);
        return ApiResponse::responseObject($sanPhamMoi);
    }

    public function storeKichCo(SizeRequestBody $request)
    {

        foreach ($request->listKichCo as $kichCo) {
            // Mỗi $kichCo đại diện cho một phần tử trong danh sách kích cỡ ($request->listKichCo).
            $kichCoTonTai = Size::where('ten_kich_co', '=', $kichCo)
                ->where('id_san_pham', '=', $request->id)
                ->first();

            if ($kichCoTonTai) {
                throw new RestApiException('Tên kích cỡ đã tồn tại ' .  $kichCo . ' đã tồn tại trong sản phẩm này');
            }

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
    public function show($id)
    {
        $timSanPham = Product::findOrFail($id); // tự động ném lỗi 404 nếu không tìm thấy
        $listKichCoCuaSanPham = Size::where('id_san_pham', '=', $timSanPham->id)->orderBy('ten_kich_co', 'asc')->get();
        $timSanPham->listKichCo = $listKichCoCuaSanPham;
        // Trả về sản phẩm dưới dạng JSON qua ProductResource
        return ApiResponse::responseObject(new ProductResourceList($timSanPham));
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

    public function updateTrangThaiSanPham(ProductRequestBody $request)
    {
        $timSanPham = Product::find($request->id);

        if ($timSanPham) {
            $timSanPham->trang_thai = $request->trangThai;
            $timSanPham->save();
        }

        $listCuaSanPham = Product::where('id', $request->id)->get();

        return ApiResponse::responseObject($listCuaSanPham);
    }

    public function updateSoluongKichCo(Request $request)
    {
        // tìm id kích cỡ.
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
        $listKichCoCuaSanPham = Size::where('id_san_pham', '=', $request->id_san_pham)->orderBy('ten_kich_co', 'asc')->get();

        return ApiResponse::responseObject($listKichCoCuaSanPham);
    }

    /**
     * Remove the specified resource from storage.
     */
}
