<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ItemCategoryRequest extends FormRequest
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
                'category_code' => 'required|string|unique:item_categories,category_code,'.$this->id,
                'prefix' => 'required|string|unique:item_categories,prefix,'.$this->id,
                'category_name' => 'required|string|unique:item_categories,category_name,'.$this->id
            ];

        
    }

    public function messages(): array
    {
        return [
            'category_code.required' => 'Category code field is required.',
            'category_code.unique' => 'Category code is already existing.',
            'prefix.required' => 'Prefix field is required.',
            'prefix.unique' => 'Prefix is already existing.',
            'category_name.requirednless' => 'Category name field is required.',
            'category_name.unique' => 'category_name is already existing.',
        ];
    }

    
}
