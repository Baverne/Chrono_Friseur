<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|array:fr',
            'name.fr' => 'required|string|max:255',
            'color' => 'required|string|hex_color|max:7',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nom',
            'name.fr' => 'nom',
            'color' => 'couleur',
        ];
    }
}
