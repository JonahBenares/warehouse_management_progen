<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReceiveDetailRequest extends FormRequest
{
    use CommonRules;
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
      
        return  [
            'pr_no' => 'required|string',
            'inspected_id'=> 'required_unless:pr_no,WH Stocks',
            'enduse_id' => 'required_unless:pr_no,WH Stocks',
            'purpose_id'=>'required_unless:pr_no,WH Stocks',
            'department_id'=>'required_unless:pr_no,WH Stocks',
            
            
        ];
    }

    public function messages(): array
    {
        return [
            'pr_no.required' => 'PR number field is required.',
            'inspected_id.required_unless' => 'Inspected by field is required unless the PR Number is Warehouse Stocks.',
            'enduse_id.required_unless' => 'Enduse field is required unless the PR Number is Warehouse Stocks.',
            'purpose_id.required_unless' => 'Purpose field is required unless the PR Number is Warehouse Stocks.',
            'department_id.required_unless' => 'Department field is required unless the PR Number is Warehouse Stocks.',
        ];
    }

}
