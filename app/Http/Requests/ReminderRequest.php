<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class ReminderRequest extends FormRequest
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
            'reminder_date'=>'required',
            'title'=>'required|string',
            'notes'=>'string|nullable',
            'person_to_notify_id'=>'required',
        ];
    }

    public function messages(): array
    {
        return [
            'reminder_date.required' => 'Reminder date field is required',
            'title.required' => 'Title field is required',
            // 'notes.required' => 'Notes field is required',
            'person_to_notify_id.required' => 'Person to notify field is required',
        ];
    }
}
