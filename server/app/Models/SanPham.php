<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    protected $table = 'san_pham';
    protected $fillable = [
        'ma_san_pham',
        'ten_san_pham',
        'mo_ta',
        'ngay_tao',
        'don_gia',
        'trang_thai',
        'id_mau_sac',
        'id_thuong_hieu',
        'id_kich_co',
        'id_chat_lieu',
    ];

    public function mauSac()
    {
        return $this->belongsTo(MauSac::class, 'id_mau_sac');
    }

    public function thuongHieu()
    {
        return $this->belongsTo(ThuongHieu::class, 'id_thuong_hieu');
    }

    public function kichCo()
    {
        return $this->belongsTo(KichCo::class, 'id_kich_co');
    }

}
