<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandRequest extends FormRequest
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
        $maThuongHieu = $this->route('thuong_hieu');
        
        $rules = [];
    
        $rules['ma_thuong_hieu'] = 'string|max:20|unique:thuong_hieu,ma_thuong_hieu,' . $maThuongHieu;
    
        if ($this->filled('ten_thuong_hieu')) {
            $rules['ten_thuong_hieu'] = 'required|string|max:255';
        }
    
        if ($this->filled('ngay_tao')) {
            $rules['ngay_tao'] = 'nullable|date';
        }
    
        if ($this->filled('trang_thai')) {
            $rules['trang_thai'] = 'required|in:kich_hoat,ngung_kich_hoat';  
        }
    
        return $rules;
    }
    
    /**
     * Get the custom validation messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'ma_thuong_hieu.required' => 'Mã thương hiệu là bắt buộc.',
            'ma_thuong_hieu.unique' => 'Mã thương hiệu đã tồn tại.',
            'ten_thuong_hieu.required' => 'Tên thương hiệu là bắt buộc.',
            'ngay_tao.date' => 'Ngày tạo phải là định dạng ngày hợp lệ.',
            'trang_thai.required' => 'Trạng thái là bắt buộc.',
            'trang_thai.in' => 'Trạng thái phải là "kich_hoat" hoặc "ngung_kich_hoat".',
        ];
    }
}
