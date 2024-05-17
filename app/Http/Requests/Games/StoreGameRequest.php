<?php

namespace App\Http\Requests\Games;

use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
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
            'date' => 'required|date',
            'players_per_team' => 'required|integer|min:1',
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
            'date.required' => __('O campo de data é obrigatório'),
            'date.date' => __('O campo de data precisa ser uma data válida.'),
            'players_per_team.required' => __('O campo de jogadores por time é obrigatório'),
            'players_per_team.integer' => __('O campo de jogadores por time precisa ser um número inteiro'),
            'players_per_team.min' => __('O campo de jogadores por time precisa ser no mínimo 1'),
        ];
    }
}
