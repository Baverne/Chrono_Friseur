<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:100',
            'description' => 'nullable|string|max:2000',
            'date' => [
                'required',
                Rule::date()->format('Y-m-d H:i'),
            ],
            'tags' => 'nullable|array',
            'tags.*.id' => 'distinct|exists:tags',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nom',
        ];
    }
}
