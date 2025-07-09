<?php

namespace App\Http\Requests\Api\Cliente\Perfil;

use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class ActualizarPerfilClienteRequest extends ApiRequest
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
        $cliente = $this->attributes->get('cliente');

        return [
            'nombre' => 'required',
            'fecha_nacimiento' => 'nullable',
            'email' => ['nullable', Rule::unique("users","email")->ignore($cliente->fk_user)],
            'password' => 'nullable',
            'repeatPassword' => 'nullable|same:password',
        ];
    }
}
