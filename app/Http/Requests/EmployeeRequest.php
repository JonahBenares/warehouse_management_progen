<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class EmployeeRequest extends FormRequest
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
            'access'=>'integer',
            'name'=>'required|unique:users,name,'.$this->id,
            'email'=> 'required_if:access,1,email|nullable|unique:users,email,'.$this->id,
            //'email'=> ['unique:users','required_if:access,1', 'nullable','email'],
            'password'=>'required_if:access,1|nullable|min:6|max:10,password,'.$this->id,
            'temp_password'=>'nullable|min:6|max:10',
            'user_type'=>'required_if:access,1,user_type|nullable'.$this->id,
            'department_id'=>'required',
            'position'=>'required|string',
            'contact_no'=>'required|numeric',
            'acknowledge_flag'=>'integer',
            'approved_flag'=>'integer',
            'requested_flag'=>'integer',
            'released_flag'=>'integer',
            'reviewed_flag'=>'integer',
            'delivered_flag'=>'integer',
            'inspected_flag'=>'integer',
            'noted_flag'=>'integer',
            'received_flag'=>'integer',
            'returned_flag'=>'integer',
            'recommend_flag'=>'integer',
            'accepted_flag'=>'integer',
            'rejected_flag'=>'integer',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required_if' => 'Email Address field is required',
            'user_type.required_if' => 'Type field is required',
        ];
    }
}
