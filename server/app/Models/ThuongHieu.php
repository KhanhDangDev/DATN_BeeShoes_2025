<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThuongHieu extends Model
{
    protected $table = 'thuong_hieu';
    protected $fillable = [
        'ma_thuong_hieu',
        'ten_thuong_hieu',
        'ngay_tao',
        'trang_thai'
    ];

    public function sanPham()
    {
        return $this->hasMany(SanPham::class, 'id_thuong_hieu');
    }
}
