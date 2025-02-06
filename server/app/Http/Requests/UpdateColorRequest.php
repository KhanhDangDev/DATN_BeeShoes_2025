<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateColorRequest extends FormRequest
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
    
        $rules['ma_mau_sac'] = 'string|max:20|unique:mau_sac,ma_mau_sac,' . $maThuongHieu;
    
        if ($this->filled('ten_mau_sac')) {
            $rules['ten_mau_sac'] = 'required|string|max:255';
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
