<?php

namespace App\Http\Requests\Web\Paquetes;

use Illuminate\Foundation\Http\FormRequest;

class EditarPaquetesRequest extends FormRequest
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
            'imagen' => 'nullable|image',
            'tokens' => 'required',
            'valor' => 'required',
            'estado' => 'required'
        ];
    }
}
