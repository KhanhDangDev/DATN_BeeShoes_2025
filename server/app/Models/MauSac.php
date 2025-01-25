<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MauSac extends Model
{
    protected $fillable = [
        'id_mau_sac',
        'ten_id_mau_sac',
        'ngay_tao',
        'trang_thai'
    ];

    public function sanPham()
    {
        return $this->hasMany(SanPham::class, 'id_id_mau_sac');
    }
}
