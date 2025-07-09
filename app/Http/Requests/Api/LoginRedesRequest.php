<?php

namespace App\Http\Requests\Api;

class LoginRedesRequest extends ApiRequest
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
            'name' => 'nullable',
            'email' => 'required',
            'provider_id' => 'required',
            'provider' => 'required',
            'fecha_nacimiento' => 'nullable'
        ];
    }

}
