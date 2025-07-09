<?php

namespace App\Http\Requests\Api\Tarotista\Chats;

use App\Http\Requests\Api\ApiRequest;


class EnviarChatRequest extends ApiRequest
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
            'mensaje' => 'required'
        ];
    }
}
