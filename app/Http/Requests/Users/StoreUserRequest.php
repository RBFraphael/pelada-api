<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
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
            'name.required' => __("O nome de usuário é obrigatório"),
            'name.string' => __("O nome de usuário deve ser uma string"),
            'email.required' => __("Email é obrigatório"),
            'email.email' => __("Email deve ser um email válido"),
            'email.unique' => __("Email já está em uso"),
            'password.required' => __("Senha é obrigatória"),
            'password.min' => __("Senha deve ter no mínimo 8 caracteres"),
            'password.confirmed' => __("As senhas não coincidem"),
        ];
    }
}
