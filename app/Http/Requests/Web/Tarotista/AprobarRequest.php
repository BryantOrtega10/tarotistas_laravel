<?php

namespace App\Http\Requests\Web\Tarotista;

use Illuminate\Foundation\Http\FormRequest;

class AprobarRequest extends FormRequest
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
            "nombre" => 'nullable',
            "descripcion_corta" => 'nullable',
            "horarioInicio" => 'nullable',
            "horarioFin" => 'nullable',
            "anios_exp" => 'nullable',
            "especialidades" => 'array|nullable',
        ];
    }
}
