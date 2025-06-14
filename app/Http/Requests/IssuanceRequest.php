<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IssuanceRequest extends FormRequest
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
            'mreqf_no' => 'required|string',
            'request_head_id' => 'required',
            'issuance_date'=>'required|string',
            'issuance_time' => 'required|string',
            'pr_no' => 'string',
            'department_id' => 'integer|nullable',
            'department_name' => 'string|nullable',
            'enduse_id' => 'integer|nullable',
            'enduse_name' => 'string|nullable',
            'purpose_id' => 'integer|nullable',
            'purpose_name' => 'string|nullable',
            'mif_no'=>'required|string|unique:issuance_head,mif_no',
            'user_id' => 'required',
            'prepared_by' => 'required',
            'prepared_by_name' => 'required',
            'prepared_by_pos' => 'nullable',
            'remarks'=>'string|nullable'
        ];
    }
}
