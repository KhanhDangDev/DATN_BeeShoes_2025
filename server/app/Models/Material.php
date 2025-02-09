<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{

    use HasUuids;
    // bỏ tự động tăng của khóa chính.
    public $incrementing = false;

    // khai báo kiểu dữ liệu của uuid.
    protected $keyWord = 'string';

    protected $table = 'chat_lieu';

    protected $fillable = [
        'ma_chat_lieu',
        'ten_chat_lieu',
        'ngay_tao',
        'trang_thai'
    ];

    public function getNgayTaoAttribute($value)
    {
        return Carbon::parse($value)->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s');
    }
}
