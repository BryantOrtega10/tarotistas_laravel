<?php

namespace App\Http\Requests\Api\Cliente;

use Illuminate\Foundation\Http\FormRequest;

class RegistroClienteRequest extends FormRequest
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
            'fechaNacimiento' => 'nullable|date',
            'email' => 'required|unique:users,email',
            'password' => 'required_without:tokenAuth',
            'repeatPassword' => 'required_without:tokenAuth|same:password',
            'tokenAuth' => 'required_without:password',
            'authProvider' => 'required_without:password',
        ];
    }
}
