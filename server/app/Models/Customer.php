<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

    use HasUuids;

    public $incrementing = false;

    // khai báo kiểu dữ liệu của uuid.
    public $keyType = 'string';

    protected $table = 'khach_hang';

    protected $fillable = [
        'ma_khach_hang',
        'ten_khach_hang',
        'email',
        'so_dien_thoai',
        'gioi_tinh',
        'ngay_sinh'
    ];


    // Quy ước ngay_tao đổi qua múi giờ mới, định dạng mới.
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s');
    }
}
