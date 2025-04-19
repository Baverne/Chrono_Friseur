<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // TODO: changer lors de l'implémentation de User
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
            'name' => 'required|string|min:2|max:30',
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
