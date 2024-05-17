<?php

namespace App\Http\Requests\Invites;

use App\Enums\InviteStatus;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInviteRequest extends FormRequest
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
            'player_id' => 'exists:players,id',
            'status' => 'in:' . join(",", InviteStatus::values()),
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
            'player_id.exists' => __('O jogador informado não existe.'),
            'status.in' => __('O status do convite precisa ser um dos seguintes valores: ' . join(", ", InviteStatus::values())),
        ];
    }
}
