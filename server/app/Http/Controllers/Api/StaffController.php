<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\NotFoundException;
use App\Exceptions\RestApiException;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Traits\ApiDataTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    use ApiDataTrait;
    // index
    public function index(Request $request)
    {
        return $this->getAllDataStaff(new Staff(), 'Success', [], ['ma_nhan_vien', 'ten_nhan_vien', 'email'], ['created_at', 'ngay_tao']);
    }

    public function createModelInstance($tableName)
    {
        $modelClass = match ($tableName) {
            'nhan_vien' => 'App\Models\Staff',
            default => throw new RestApiException("Model not found for table: $tableName"),
        };

        return new $modelClass();
    }

    public function attributeObject($tableName, $attributeNameVN)
    {
        $messageIsExists = ' đã tồn tại!';
        $attributeObject = [
            'tableName' => $tableName,
            'fieldCode' => 'ma_' . $tableName,
            'fieldName' => 'ten_' . $tableName,
            'codeMessageThrow' => 'Mã ' . $attributeNameVN . $messageIsExists,
            'nameMessageThrow' => 'Tên ' . $attributeNameVN . $messageIsExists,
            'isNotExitsMessageThrow' => 'Không tìm thấy ' . $attributeNameVN . ' này!',
        ];

        return $attributeObject;
    }

    /* storeAttributesCommon (method post)
    *  @params:
    *  $attributeObject: {
    *    tableName,
    *    fieldCode,
    *    fieldName,
    *    codeMessageThrow,
    *    nameMessageThrow
    *  }
    */

    public function storeAttributesCommon($attributeObject, Request $req)
    {
        $tableName = $attributeObject['tableName'];
        $code = $attributeObject['fieldCode'];
        $name = $attributeObject['fieldName'];
        $messageCodeThrow = $attributeObject['codeMessageThrow'];
        $messageNameThrow = $attributeObject['nameMessageThrow'];

        $validatedData = $req->validate([
            'ma_nhan_vien' => 'required|string|unique:nhan_vien,ma_nhan_vien',
            'ten_nhan_vien' => 'required|string',
            'mat_khau' => 'required|string',
            'trang_thai' => 'required|string',
            'email' => 'required|email|unique:nhan_vien,email',
            'so_dien_thoai' => 'required|string|unique:nhan_vien,so_dien_thoai',
            'gioi_tinh' => 'required|in:0,1',
            'ngay_sinh' => 'required|date',
        ], [
            'ma_nhan_vien.required' => 'Mã không được bỏ trống.',
            'ma_nhan_vien.unique' => 'Mã khách hàng đã tồn tại.',
            'ten_nhan_vien.required' => 'Tên không được bỏ trống.',
            'mat_khau.required' => 'Mật khẩu không được bỏ trống.',
            'trang_thai.required' => 'Trạng thái không được bỏ trống.',
            'email.required' => 'Email không được bỏ trống.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã tồn tại.',
            'so_dien_thoai.required' => 'Số điện thoại không được bỏ trống.',
            'so_dien_thoai.unique' => 'Số điện thoại đã tồn tại.',
            'gioi_tinh.required' => 'Giới tính không được bỏ trống.',
            'gioi_tinh.in' => 'Giới tính không hợp lệ.',
            'ngay_sinh.required' => 'Ngày sinh không được bỏ trống.',
            'ngay_sinh.date' => 'Ngày sinh không hợp lệ.',
        ]);

        $modelAttribute = $this->createModelInstance($tableName);
        $modelAttribute->$code = $req->ma_nhan_vien;
        $modelAttribute->$name = $req->ten_nhan_vien;
        $modelAttribute->email = $req->email;
        $modelAttribute->so_dien_thoai = $req->so_dien_thoai;
        $modelAttribute->gioi_tinh = $req->gioi_tinh;
        $modelAttribute->ngay_sinh = $req->ngay_sinh;
        $modelAttribute->mat_khau = $req->mat_khau;
        $modelAttribute->trang_thai = $req->trang_thai;
        $modelAttribute->save();

        return $modelAttribute;
    }

    public function storeStaff(Request $req)
    {
        $attributeObj = $this->attributeObject("nhan_vien", "Nhân viên");
        $createdModel = $this->storeAttributesCommon($attributeObj, $req);
        return ApiResponse::responseObject($createdModel);
    }

    /* uodateAttributesCommon (method put)
    *  @params:
    *  $attributeObject: {
    *    tableName,
    *    fieldCode,
    *    fieldName,
    *    codeMessageThrow,
    *    nameMessageThrow
    *  }
    */

    public function updateAttributesCommon($attributeObject, Request $req)
    {
        $tableName = $attributeObject['tableName'];
        $code = $attributeObject['fieldCode'];
        $name = $attributeObject['fieldName'];
        $messageIsExitsThrow = $attributeObject['isNotExitsMessageThrow'];
    
        // Tìm bản ghi cần cập nhật
        $findAttribute = $this->createModelInstance($tableName)::find($req->id);
        
        if (!$findAttribute) {
            throw new NotFoundException($messageIsExitsThrow);
        }
    
        // Validate đầu vào, bỏ qua kiểm tra trùng lặp nếu không thay đổi
        $validatedData = $req->validate([
            'ma_nhan_vien' => [
                'required',
                'string',
                Rule::unique('nhan_vien', 'ma_nhan_vien')->ignore($req->id)
            ],
            'ten_nhan_vien' => 'required|string',
            'mat_khau' => 'required|string',
            'trang_thai' => 'required|string',
            'email' => [
                'required',
                'email',
                Rule::unique('nhan_vien', 'email')->ignore($req->id)
            ],
            'so_dien_thoai' => [
                'required',
                'string',
                Rule::unique('nhan_vien', 'so_dien_thoai')->ignore($req->id)
            ],
            'gioi_tinh' => 'required|in:0,1',
            'ngay_sinh' => 'required|date',
        ], [
            'ma_nhan_vien.required' => 'Mã không được bỏ trống.',
            'ma_nhan_vien.unique' => 'Mã khách hàng đã tồn tại.',
            'ten_nhan_vien.required' => 'Tên không được bỏ trống.',
            'mat_khau.required' => 'Mật khẩu không được bỏ trống.',
            'trang_thai.required' => 'Trạng thái không được bỏ trống.',
            'email.required' => 'Email không được bỏ trống.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã tồn tại.',
            'so_dien_thoai.required' => 'Số điện thoại không được bỏ trống.',
            'so_dien_thoai.unique' => 'Số điện thoại đã tồn tại.',
            'gioi_tinh.required' => 'Giới tính không được bỏ trống.',
            'gioi_tinh.in' => 'Giới tính không hợp lệ.',
            'ngay_sinh.required' => 'Ngày sinh không được bỏ trống.',
            'ngay_sinh.date' => 'Ngày sinh không hợp lệ.',
        ]);
        
        // Cập nhật dữ liệu
        $findAttribute->update([
            $code => $validatedData['ma_nhan_vien'] ?? $findAttribute->$code, // Giữ nguyên nếu không gửi lên
            $name => $validatedData['ten_nhan_vien'],
            'email' => $validatedData['email'],
            'so_dien_thoai' => $validatedData['so_dien_thoai'],
            'gioi_tinh' => $validatedData['gioi_tinh'],
            'ngay_sinh' => $validatedData['ngay_sinh'],
            'mat_khau' => $validatedData['mat_khau'],
            'trang_thai' => $validatedData['trang_thai'],
        ]);
    
        return $findAttribute;
    }
    

    public function updateStaff(Request $req)
    {
        $attributeObj = $this->attributeObject("nhan_vien", "Nhân viên");
        $updatedModel = $this->updateAttributesCommon($attributeObj, $req);
        return ApiResponse::responseObject($updatedModel);
    }

    // show

    public function showStaff(Request $request)
    {
        return $this->getDataById(new Staff(), $request->id);
    }


}
