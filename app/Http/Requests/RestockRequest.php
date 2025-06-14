<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestockRequest extends FormRequest
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
            'mrs_no'=>'required|string|nullable|unique:restock_head,mrs_no',
            'source_pr'=>'required|string|nullable',
            'destination'=>'required|string|nullable',
            'date'=>'required|string|nullable',
            'time'=>'required|string|nullable',
        ];
    }
}
