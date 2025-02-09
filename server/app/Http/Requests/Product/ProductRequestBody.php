<?php

namespace App\Http\Requests\Product;

use App\Constants\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequestBody extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $rules = [];

        if ($this->route()->getActionMethod() === "store") {

            // Quy tắc xác thực khi lưu mới sản phẩm
            $rules = [
                'maSanPham' => 'required|string|max:20|unique:san_pham,ma_san_pham',
                'tenSanPham' => 'required|string|max:100|unique:san_pham,ten_san_pham',
                'trangThai' => 'required|' . Rule::in(ProductStatus::ProductStatusArray()), // Gộp lại
                'idThuongHieu' => 'required|string',
            ];
        }

        if ($this->route()->getActionMethod() === "update") {
            $rules = [
                'maSanPham' => 'required|string|max:20',
                'tenSanPham' => 'required|string|max:100',
                'trangThai' => 'required|' . Rule::in(ProductStatus::ProductStatusArray()), // Gộp lại
                'idMauSac' => 'required|string',
                'idChatLieu' => 'required|string',
                'idThuongHieu' => 'required|string',
            ];
        }

        if ($this->route()->getActionMethod() === "updateTrangThaiSanPham") {
            $rules = [
                'id' => 'required|string',
                'trangThai' => 'required|' . Rule::in(ProductStatus::ProductStatusArray()), // Gộp lại
            ];
        }


        return $rules;
    }



    public function messages()
    {
        return [
            'maSanPham.required' => 'Mã sản phẩm không được bỏ trống.',
            'maSanPham.max' => 'Mã sản phẩm được phép tối đa 20 kí tự.',
            'maSanPham.unique' => 'Mã sản phẩm đã tồn tại.',
            'maSanPham.string' => 'Mã sản phẩm phải là chữ cái.',

            'tenSanPham.required' => 'Tên sản phẩm không được bỏ trống.',
            'tenSanPham.max' => 'Tên sản phẩm được phép tối đa 100 kí tự.',
            'tenSanPham.unique' => 'Tên sản phẩm đã tồn tại',
            'tenSanPham.string' => 'Tên sản phẩm phải là chữ cái.',

            'trangThai.in' => 'Trạng thái sản phẩm không hợp lệ.',
            'trangThai.required' => 'Trạng thái sản phẩm là bắt buộc.',

            'idThuongHieu.required' => 'Thương hiệu không được bỏ trống.',
        ];
    }
}
