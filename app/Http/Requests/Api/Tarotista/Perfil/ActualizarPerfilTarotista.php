<?php

namespace App\Http\Requests\Api\Tarotista\Perfil;

use App\Http\Requests\Api\ApiRequest;


class ActualizarPerfilTarotista extends ApiRequest
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
            'descripcionCorta' => 'nullable',
            'horarioInicio' => 'nullable',
            'horarioFin' => 'nullable',
            'aniosExp' => 'nullable',
            'pais' => 'nullable',
            'especialidades' => 'nullable|array',
        ];
    }
}
