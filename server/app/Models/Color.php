<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Color extends Model
{

    // bỏ tính năng tự động tăng của id.
    public $incrementing = false;

    // khai báo kiểu dữ liệu của uuid.
    public $keyType = 'string';

    protected $table = 'mau_sac';

    protected $fillable = [
        'ma_mau_sac',
        'ten_mau_sac',
        'ngay_tao',
        'trang_thai'
    ];

    public function getNgayTaoAttribute($value)
    {
        return Carbon::parse($value)->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s');
    }
}
