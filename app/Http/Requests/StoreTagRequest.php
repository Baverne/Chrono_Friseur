<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: changer lors de l'implémentation de User
        return true;
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
