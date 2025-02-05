<?php

namespace App\Http\Requests\Product;

use App\Constants\CommonStatus;
use Illuminate\Foundation\Http\FormRequest;

class AttributeRequestBody extends FormRequest
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

        if ($this->route()->getActionMethod() === 'storeKichCo') {
            $rules['tenKichCo'] = 'required|string|max:20|unique:kich_co, ten_kich_co';
            $rules['trangThai'] = [CommonStatus::CommonStatusArray(), 'required'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'tenKichCo.required' => 'Tên kích cỡ không được bỏ trống',
            'tenKichCo.unique' => 'Tên kích cỡ đã tồn tại',
            'tenKichCo.max' => 'Tên kích cỡ chứa tối đa 20 kí tự',
            'tenKichCo.string' => 'Tên kích cỡ phải là chữ cái',

            'trangThai.required' => 'Trạng thái không được bỏ trống',
            'trangThai.in' => 'Trạng thái sản phẩm không hợp lệ',

        ];
    }
}
