<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{

    // bỏ tự động tăng của id.
    public $incrementing = false;

    // khai báo kiểu dữ liệu uuid.
    protected $keyType = 'string';

    protected $table = 'kich_co';

    protected $fillable = [
        'id_san_pham',
        'ten_kich_co',
        'so_luong_ton',
        'trang_thai'
    ];
}
