<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'maSanPham' => $this->ma_san_pham,
            'tenSanPham' => $this->ten_san_pham,
            'moTa' => $this->mo_ta,
            'ngayTao' => $this->ngay_tao,
            'donGia' => $this->don_gia,
            'trangThai' => $this->trang_thai,
            'idMauSac' => $this->id_mau_sac,
            'idChatLieu' => $this->id_chat_lieu,
            'idThuongHieu' => $this->id_thuong_hieu,
            'listKichCo' => $this->listKichCo
        ];
    }
}
