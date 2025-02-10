<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{

    use HasUuids;

    public $incrementing = false;

    // khai báo kiểu dữ liệu của uuid.
    public $keyType = 'string';

    protected $table = 'thuong_hieu';

    protected $fillable = [
        'ma_thuong_hieu',
        'ten_thuong_hieu',
        'ngay_tao',
        'trang_thai'
    ];


    // Quy ước ngay_tao đổi qua múi giờ mới, định dạng mới.
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s');
    }
}
