<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:255'],
            'status_id' => ['required', 'numeric', 'exists:statuses,id'],
            'due_date' => ['required', 'date', 'after:' . now()->addMinutes(5)],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'due_date.after' => 'The due date must be a future date.',
        ];
    }
}