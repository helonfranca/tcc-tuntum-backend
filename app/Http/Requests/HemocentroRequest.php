<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HemocentroRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $hemocentroId = $this->route('id');
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            // Dados do hemocentro
            'nome' => 'required|string|max:255',
            'cnes' => "required|string|unique:hemocentro,cnes,{$hemocentroId}|max:45",
            'email' => "required|string|email|max:255|unique:hemocentro,email,{$hemocentroId}",
            'password' => $isUpdate ? 'sometimes|nullable|string|min:8' : 'string|min:8',
            'telefone' => "required|string|unique:hemocentro,telefone,{$hemocentroId}|regex:/^\(\d{2}\) \d{4,5}-\d{4}$/",

            // Dados do endereço
            'cep' => 'string|max:45',
            'rua' => 'string|max:255',
            'bairro' => 'string|max:45',
            'estado' => 'string|max:45',
            'municipio' => 'string|max:45',
            'numero' => 'nullable|integer',
            //'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Horários de funcionamento
            'funcionamentos' => 'nullable',
            'funcionamentos.*.hora_abertura' => 'required_with:funcionamentos|date_format:H:i',
            'funcionamentos.*.hora_fechamento' => 'required_with:funcionamentos|date_format:H:i',
            'funcionamentos.*.dias_semana' => 'required_with:funcionamentos',
            'funcionamentos.*.dias_semana.*' => 'in:domingo,segunda-feira,terça-feira,quarta-feira,quinta-feira,sexta-feira,sábado',
        ];
    }

    public function messages()
    {
        return [
            // Validação do nome
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.string' => 'O nome deve ser um texto.',
            'nome.max' => 'O nome não pode ter mais de 255 caracteres.',

            // Validação do CNES
            'cnes.required' => 'O campo CNES é obrigatório.',
            'cnes.string' => 'O CNES deve ser um texto.',
            'cnes.unique' => 'O CNES informado já está em uso por outro hemocentro.',
            'cnes.max' => 'O CNES não pode ter mais de 45 caracteres.',

            // Validação do e-mail
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.string' => 'O e-mail deve ser um texto.',
            'email.email' => 'O e-mail deve estar em um formato válido (ex: exemplo@email.com).',
            'email.unique' => 'O e-mail informado já está em uso por outro hemocentro.',

            // Validação da senha
            'password.string' => 'A senha deve ser um texto.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',

            // Validação do telefone
            'telefone.required' => 'O campo telefone é obrigatório.',
            'telefone.string' => 'O telefone deve ser um texto.',
            'telefone.unique' => 'O telefone informado já está em uso por outro hemocentro.',
            'telefone.regex' => 'O telefone deve estar no formato válido (ex: (99) 99999-9999).',

            // Validação do endereço
            'cep.required' => 'O campo CEP é obrigatório.',
            'cep.string' => 'O CEP deve ser um texto.',
            'cep.max' => 'O CEP não pode ter mais de 45 caracteres.',

            'rua.required' => 'O campo rua é obrigatório.',
            'rua.string' => 'A rua deve ser um texto.',
            'rua.max' => 'A rua não pode ter mais de 255 caracteres.',

            'bairro.required' => 'O campo bairro é obrigatório.',
            'bairro.string' => 'O bairro deve ser um texto.',
            'bairro.max' => 'O bairro não pode ter mais de 45 caracteres.',

            'estado.required' => 'O campo estado é obrigatório.',
            'estado.string' => 'O estado deve ser um texto.',
            'estado.max' => 'O estado não pode ter mais de 45 caracteres.',

            'municipio.required' => 'O campo município é obrigatório.',
            'municipio.string' => 'O município deve ser um texto.',
            'municipio.max' => 'O município não pode ter mais de 45 caracteres.',

            'numero.integer' => 'O número deve ser um valor inteiro.',

            // Validação dos funcionamento
            'funcionamentos.*.hora_abertura.required_with' => 'O horário de abertura é obrigatório para cada funcionamento.',
            'funcionamentos.*.hora_abertura.date_format' => 'O horário de abertura deve estar no formato HH:MM.',
            'funcionamentos.*.hora_fechamento.required_with' => 'O horário de fechamento é obrigatório para cada funcionamento.',
            'funcionamentos.*.hora_fechamento.date_format' => 'O horário de fechamento deve estar no formato HH:MM.',
            'funcionamentos.*.dias_semana.required_with' => 'Os dias da semana são obrigatórios para cada funcionamento.',
            'funcionamentos.*.dias_semana.*.in' => 'O dia da semana deve ser um dos seguintes: domingo, segunda-feira, terça-feira, quarta-feira, quinta-feira, sexta-feira, sábado.',
        ];
    }
}
