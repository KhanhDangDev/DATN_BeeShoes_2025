<?php

namespace App\Helpers;

class ProductCodeHelper
{
    public static function taoMaSanPham($model, $maSanPham)
    {

        $ketQua = $maSanPham;

        // Lấy bản ghi mới nhất của model, sắp xếp theo trường 'created_at' giảm dần và chỉ lấy bản ghi đầu tiên
        $modelMoiNhat = $model->orderBy('created_at', 'desc')->first();

        if ($modelMoiNhat) {
            $chuSo = substr($modelMoiNhat->ma_san_pham, 2); // vi du: 'SKU0007' -> '0007'

            // Tăng chuỗi số lên 1 và đệm bằng số 0 ở phía trước để đảm bảo độ dài là 4 ký tự (ví dụ: '0007' -> '0008')
            $chuSoMoi = str_pad((int) $chuSo + 1, 4, 0, STR_PAD_LEFT);

            $ketQua = $ketQua . $chuSoMoi;
        } else {
            $ketQua = $ketQua . "0001";
        }

        return $ketQua;
    }
}
