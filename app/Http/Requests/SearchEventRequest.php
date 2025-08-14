<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SearchEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user() !== null;
    }

    public function rules(): array
    {
        return [
            'fulltext' => 'sometimes|nullable|string|min:2|max:255',
            'with_tags' => 'sometimes|nullable|array',
            'with_tags.*.id' => 'distinct|exists:tags',
            'without_tags' => 'sometimes|nullable|array',
            'without_tags.*' => 'not_in:with_tags',
            'without_tags.*.id' => 'distinct|exists:tags',
            'after' => 'sometimes|nullable|date|date_format:Y-m-d H:i',
            'before' => 'sometimes|nullable|date|date_format:Y-m-d H:i|after_or_equal:after',
        ];
    }

    public function attributes(): array
    {
        return [
            'fulltext' => 'texte',
            'with_tags' => 'avec',
            'without_tags' => 'sans',
            'after' => 'après',
            'before' => 'avant',
        ];
    }
}
