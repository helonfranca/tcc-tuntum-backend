<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->route('id');

        return [
            'nome' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'data_nascimento' => 'required|date',
            'email' => "required|string|email|max:255|unique:users,email,{$userId}",
            'sexo' => 'required|string|in:masculino,feminino,outro',
            'cpf' => "required|string|unique:users,cpf,{$userId}|regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/",
            'telefone' => "required|string|unique:users,telefone,{$userId}|regex:/^\(\d{2}\) \d{4,5}-\d{4}$/",
            'tipo_usuario_id' => 'required|exists:tipo_usuario,id',
            'cep' => 'nullable|string',
            'rua' => 'nullable|string',
            'bairro' => 'nullable|string',
            'estado' => 'nullable|string',
            'municipio' => 'nullable|string',
            'numero' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            // Validação do e-mail
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.string' => 'O e-mail deve ser um texto.',
            'email.email' => 'O e-mail deve estar em um formato válido (ex: exemplo@email.com).',
            'email.unique' => 'O e-mail informado já está em uso por outro usuário.',

            // Validação do CPF
            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.string' => 'O CPF deve ser um texto.',
            'cpf.unique' => 'O CPF informado já está cadastrado.',
            'cpf.regex' => 'O CPF deve estar no formato válido (ex: 000.000.000-00).',

            // Validação do telefone
            'telefone.required' => 'O campo telefone é obrigatório.',
            'telefone.string' => 'O telefone deve ser um texto.',
            'telefone.unique' => 'O telefone informado já está cadastrado.',
            'telefone.regex' => 'O telefone deve estar no formato válido (ex: (99) 99999-9999).',
        ];
    }
}
