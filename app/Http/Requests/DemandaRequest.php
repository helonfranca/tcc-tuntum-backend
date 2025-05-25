<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DemandaRequest extends FormRequest
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
            'tipo_sanguineo_id' => 'required|exists:tipo_sanguineo,id',
            'hemocentro_id' => 'required|exists:hemocentro,id',
            'status' => ['required', 'string', Rule::in(['aberta', 'finalizada'])],
            'data_inicial' => 'required|date',
            'data_final' => 'nullable|date|after_or_equal:data_inicial',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_sanguineo_id.required' => 'O tipo sanguíneo é obrigatório.',
            'tipo_sanguineo_id.exists' => 'O tipo sanguíneo selecionado é inválido.',
            'hemocentro_id.required' => 'O hemocentro é obrigatório.',
            'hemocentro_id.exists' => 'O hemocentro selecionado é inválido.',
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'O status selecionado é inválido. Os valores permitidos são: aberta ou finalizada .',
            'data_inicial.required' => 'A data inicial é obrigatória.',
            'data_inicial.date' => 'A data inicial deve ser uma data válida.',
            'data_final.date' => 'A data final deve ser uma data válida.',
            'data_final.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.',
        ];
    }
}
