<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Api\ApiRequest;

class RegistroRequest extends ApiRequest
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
            'nombre' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required',
            'repeatPassword' => 'required|same:password',
            'fecha_nacimiento' => 'nullable'

        ];
    }
}
