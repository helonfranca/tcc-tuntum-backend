<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoadorRequest extends FormRequest
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
    public function rules()
    {
        return [
            'malaria' => 'required|boolean',
            'hiv' => 'required|boolean',
            'droga_ilicita' => 'required|boolean',
            'hepatiteb' => 'required|boolean',
            'hepatitec' => 'required|boolean',
            'usuario_id' => 'required|exists:users,id',
            'tipo_sanguineo_id' => 'required|exists:tipo_sanguineo,id',
        ];
    }
    public function messages()
    {
        return [
            // Validação do e-mail
            'usuario_id.exists' => 'Não exite registro desse usuário.',
            'malaria.requerid' => 'O campo malária é obrigatório.',
            'hiv.requerid' => 'O campo HIV é obrigatório.',
            'hepatiteb.requerid' => 'O campo hepatite B é obrigatório.',
            'hepatitec.requerid' => 'O campo hepatite C é obrigatório.'
        ];
    }

}
