<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KichCo extends Model
{
    protected $table = 'kich_co';

    protected $fillable = [
        'ma_kich_co',
        'ten_kich_co',
        'ngay_tao',
        'trang_thai'
    ];

    public function sanPham()
    {
        return $this->hasMany(SanPham::class, 'id_kich_co');
    }
}
