<?php

namespace App\Http\Requests\Web\Configuracion;

use Illuminate\Foundation\Http\FormRequest;

class EditarConfiguracionRequest extends FormRequest
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
            'token_min' => 'required|min:1',
            'valor_min' => 'required|min:1',
            'por_comision' => 'required|min:1|max:100',
        ];
    }
}
