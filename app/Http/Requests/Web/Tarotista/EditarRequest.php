<?php

namespace App\Http\Requests\Web\Tarotista;

use Illuminate\Foundation\Http\FormRequest;

class EditarRequest extends FormRequest
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
            "nombre" => 'required',
            "descripcion_corta" => 'required',
            "horarioInicio" => 'required',
            "horarioFin" => 'required',
            "anios_exp" => 'required',
            "especialidades" => 'array|nullable',
        ];
    }
}
