<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSizeRequest extends FormRequest
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
        $maThuongHieu = $this->route('kich_co');
        
        $rules = [];
    
        $rules['ma_kich_co'] = 'string|max:20|unique:kich_co,ma_kich_co,' . $maThuongHieu;
    
        if ($this->filled('ten_kich_co')) {
            $rules['ten_kich_co'] = 'required|string|max:255';
        }
    
        if ($this->filled('ngay_tao')) {
            $rules['ngay_tao'] = 'nullable|date';
        }
    
        if ($this->filled('trang_thai')) {
            $rules['trang_thai'] = 'required|in:kich_hoat,ngung_kich_hoat';  
        }
    
        return $rules;
    }
}
