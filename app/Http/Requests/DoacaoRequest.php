<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DoacaoRequest extends FormRequest
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
            'data_doacao' => 'date',
            'quantidade' => 'numeric|min:0',
            'doador_id' => 'required|exists:doador,id',
            'status' => ['required', 'string', Rule::in(['pendente', 'confirmada'])],
            'demanda_id' => 'required|exists:demanda,id'
        ];
    }

    public function messages(): array
    {
        return [
            'quantidade.numeric' => 'A quantidade deve ser um número.',
            'quantidade.min' => 'A quantidade não pode ser negativa.',
            'doador_id.required' => 'O doador é obrigatório.',
            'doador_id.exists' => 'O doador selecionado é inválido.',
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'O status selecionado é inválido. Os valores permitidos são: pendente ou confirmada .',
            'demanda_id.required' => 'A demanda é obrigatória.',
            'demanda_id.exists' => 'A demanda selecionada é inválida.',
        ];
    }
}
