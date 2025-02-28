<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use App\Models\Hemocentro;

class LoginRequest extends FormRequest
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
            'email' => 'required|string',
            'password' => 'required|string',
        ];
    }

    /**
     * Customizando a validação para verificar se o email existe em users ou hemocentros.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $email = $this->input('email');

            $existeUsuario = User::where('email', $email)->exists();
            $existeHemocentro = Hemocentro::where('email', $email)->exists();

            if (!$existeUsuario && !$existeHemocentro) {
                $validator->errors()->add('email', 'Não existe nenhuma conta associada a esse e-mail.');
            }
        });
    }

    public function messages()
    {
        return [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
        ];
    }
}
