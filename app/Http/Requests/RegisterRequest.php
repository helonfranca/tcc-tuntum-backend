<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nome' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'data_nascimento' => 'required|date',
            'email' => 'required|string|email|max:255|unique:users',
            'sexo' => 'required|string',
            'cpf' => 'required|string|unique:users',
            'telefone' => 'required|string|unique:users',
            'tipo_usuario_id' => 'required|exists:tipo_usuario,id',

            // Campos do endereço
            'cep' => 'required|string',
            'rua' => 'required|string',
            'bairro' => 'required|string',
            'estado' => 'required|string',
            'municipio' => 'required|string',
            'numero' => 'required|integer',
        ];
    }
}
