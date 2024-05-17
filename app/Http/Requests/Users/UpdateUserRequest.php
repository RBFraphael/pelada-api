<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $user = request()->route('user');

        return [
            'name' => 'string',
            'email' => 'string|email|unique:users,email,' . $user->id,
            'password' => 'string|min:8|confirmed',
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
            'name.string' => __("O nome de usuário deve ser uma string"),
            'email.email' => __("Email deve ser um email válido"),
            'email.unique' => __("Email já está em uso"),
            'password.min' => __("Senha deve ter no mínimo 8 caracteres"),
            'password.confirmed' => __("As senhas não coincidem"),
        ];
    }
}
