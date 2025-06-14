<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class ItemsRequest extends FormRequest
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
            'item_category_id'=>'required',
            'item_sub_category_id'=>'required',
            'item_description'=>'required|string',
            'pn_no'=>'required|unique:items,pn_no,'.$this->id
        ];
    }

    public function messages(): array
    {
        return [
            'item_category_id.required' => 'Category field is required',
            'item_sub_category_id.required' => 'Subcategory field is required',
            'item_description.required' => 'Item Description field is required',
            'pn_no.required' => 'PN No. field is required',
        ];
    }
}
