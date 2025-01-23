<?php

namespace App\Constants;

class CommonStatus
{
    const DANG_HOAT_DONG = 'dang_hoat_dong';
    const NGUNG_HOAT_DONG = 'ngung_hoat_dong';

    // Nếu muốn trả về tất cả trạng thái dưới dạng mảng
    public static function CommonStatusArray()
    {
        return [
            self::DANG_HOAT_DONG,
            self::NGUNG_HOAT_DONG
        ];
    }
}
