<?php

namespace App\Http\Requests\Players;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlayerRequest extends FormRequest
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
            'name' => "string",
            'level' => "integer|min:1|max:5",
            'is_goalkeeper' => "boolean",
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.string' => __('O campo de nome precisa ser uma string'),
            'level.integer' => __('O campo de nível precisa ser um número inteiro'),
            'level.min' => __('O campo de nível precisa ser no mínimo 1'),
            'level.max' => __('O campo de nível precisa ser no máximo 5'),
            'is_goalkeeper.boolean' => __('O campo de goleiro precisa ser um booleano'),
        ];
    }
}
