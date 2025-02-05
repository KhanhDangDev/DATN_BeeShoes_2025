<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Product extends Model
{
    use HasUuids;

    // bỏ tự đông tăng của khóa chính id.
    public $incrementing = false;

    // khai báo kiểu dữ liệu của uuid.
    public $keyType = 'string';

    protected $table = 'san_pham';

    protected $fillable = [
        'ma_san_pham',
        'ten_san_pham',
        'mo_ta',
        'ngay_tao',
        'don_gia',
        'trang_thai',
        'id_mau_sac',
        'id_chat_lieu',
        'id_thuong_hieu',
    ];

    // Chuyển đổi dữ liệu
    protected function casts(): array
    {
        return [
            'don_gia' => 'float',
        ];
    }


    // Quy ước ngay_tao đổi qua múi giờ mới, định dạng mới.
    public function getNgayTaoAttribute($value)
    {
        return Carbon::parse($value)->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s');
    }

    public function setNgayTaoAttribute($value)
    {
        // Nếu giá trị ngày không phải là null hoặc đã được định dạng, chuyển đổi ngày sang dạng chuẩn (Y-m-d H:i:s)
        $this->attributes['ngay_tao'] = Carbon::parse($value)->timezone('Asia/Ho_Chi_Minh')->format('Y-m-d H:i:s');
    }
}
