<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
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
                    'supplier_name' => 'required|string|unique:suppliers,supplier_name,'.$this->id .',ID',
                    'address'=>'string|nullable',
                    'email' => 'email|nullable',
                    'contact_person'=>'string|nullable',
                    'contact_number'=>'string|nullable',
                    'terms'=>'string|nullable',
                    'is_active'=>'integer'
                ];

         
          
    
    }
}
