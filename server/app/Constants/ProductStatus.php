<?php

namespace App\Constants;

class ProductStatus
{
    const DANG_KINH_DOANH = 'dang_kinh_doanh';
    const NGUNG_KINH_DOANH = 'ngung_kinh_doanh';

    // Nếu muốn trả về tất cả trạng thái dưới dạng mảng
    public static function ProductStatusArray()
    {
        return [
            self::DANG_KINH_DOANH,
            self::NGUNG_KINH_DOANH
        ];
    }
}
