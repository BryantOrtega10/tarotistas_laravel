<?php

namespace App\Http\Requests\Web\Pagos;

use Illuminate\Foundation\Http\FormRequest;

class GenerarPagoRequest extends FormRequest
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
            'valor' => 'required|min:1',
            'descripcion' => 'required|string',
        ];
    }
}
