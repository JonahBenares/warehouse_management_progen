<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class ReceiveHeadRequest extends FormRequest
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
            'mrecf_no' => 'required|string|unique:receive_head,mrecf_no,'.$this->id,
            'waybill_no'=>'string|nullable',
            'receive_date'=>'required|string',
            'dr_no' => 'required|string',
            'po_no'=>'string|nullable',
            'si_or'=>'string|nullable',
            'pcf'=>'integer',
            'user_id'=>'integer'
        ];
    }
}
