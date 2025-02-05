<?php

namespace App\Http\Requests\Product;

use App\Constants\CommonStatus;
use Illuminate\Foundation\Http\FormRequest;

class SizeRequestBody extends FormRequest
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

        return [
            'id_san_pham' => 'required|string',
            'listKichCo' => 'required|array',  // Kiểm tra xem 'listKichCo' có phải là mảng không.
            'listKichCo.*' => 'required|string|distinct',  // Kiểm tra từng phần tử trong mảng phải là chuỗi và không trùng lặp
        ];
    }

    public function messages()
    {
        return [
            'id_san_pham.required' => 'Id sản phẩm là bắt buộc.',

            'listKichCo.required' => 'Danh sách kích cỡ là bắt buộc.',
            'listKichCo.array' => 'Danh sách kích cỡ phải là một mảng.',
            'listKichCo.*.required' => 'Mỗi kích cỡ không được để trống.',
            'listKichCo.*.string' => 'Kích cỡ phải là chuỗi.',
            'listKichCo.*.distinct' => 'Kích cỡ không được trùng lặp.',
        ];
    }
}
