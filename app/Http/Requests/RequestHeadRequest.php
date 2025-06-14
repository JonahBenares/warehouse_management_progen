<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class RequestHeadRequest extends FormRequest
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
            'mreqf_no' => 'required|string|unique:request_head,mreqf_no'.$this->id .',ID',
            'request_date'=>'required|string',
            'request_time'=>'required|string',
            'request_type'=>'required|string',
            'pr_no'=>'string|nullable',
            'department_id'=>'string|nullable',
            'department_name'=>'string|nullable',
            'enduse_id'=>'string|nullable',
            'enduse_name'=>'string|nullable',
            'purpose_id'=>'string|nullable',
            'purpose_name'=>'string|nullable',
            'remarks'=>'string|nullable',
            'user_id'=>'required|integer'
        ];
    }
}
