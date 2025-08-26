<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportGuestGoalsRequest extends FormRequest
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
            'goals' => ['required', 'array', 'max:20'],
            'goals.*.goal' => ['required', 'string', 'max:255'],
            'goals.*.createdAt' => ['nullable', 'date'],
            'goals.*.tasks' => ['sometimes', 'array'],
            'goals.*.tasks.*.task' => ['required', 'string', 'max:255'],
            'goals.*.tasks.*.status' => ['nullable', 'in:未着手,進行中,完了'],
            'goals.*.tasks.*.createdAt' => ['nullable', 'date'],
        ];
    }
}
