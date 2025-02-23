<?php

namespace App\Http\Services;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use App\Models\Endereco;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class AuthService
{
    /**
     * Registra um novo user e seu endereço.
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        // Cria o endereço
        $endereco = Endereco::create([
            'cep' => $data['cep'],
            'rua' => $data['rua'],
            'bairro' => $data['bairro'],
            'estado' => $data['estado'],
            'municipio' => $data['municipio'],
            'numero' => $data['numero'],
        ]);

        // Criptografa a senha
        $data['password'] = Hash::make($data['password']);

        // Associa o endereço ao usuário
        $data['endereco_id'] = $endereco->id;

        // Cria o usuário
        return User::create($data);
    }

    /**
     * Realiza o login do usuário.
     *
     * @param array $credentials
     * @return User|null
     */
    public function login(array $credentials): ?User
    {
        // Busca o usuário pelo email
        $usuario = User::where('email', $credentials['email'])->first();

        // Verifica se o usuário existe e se a senha está correta
        if (!$usuario || !Hash::check($credentials['password'], $usuario->password)) {
            return null;
        }

        return $usuario;
    }

    public function sendResetLink($email)
    {
        // Envia o link de redefinição usando a classe Password do Laravel
        $status = Password::sendResetLink(['email' => $email]);

        // Se o link foi enviado com sucesso, envia o e-mail personalizado
        if ($status === Password::RESET_LINK_SENT) {
            // Encontre o usuário
            $user = User::where('email', $email)->first();

            // Se o usuário for encontrado, gere o token e envie o e-mail
            if ($user) {
                $token = \Illuminate\Support\Facades\Password::createToken($user);

                // Envia o e-mail personalizado com o token
                Mail::to($email)->send(new ResetPasswordMail($token, $email));
            }
        }

        return $status;
    }

    public function resetPassword(array $data): string
    {
        return Password::reset($data, function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();
        });
    }
}
