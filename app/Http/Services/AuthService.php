<?php

namespace App\Http\Services;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\HemocentroResource;
use App\Http\Resources\UserResource;
use App\Mail\ResetPasswordMail;
use App\Models\Hemocentro;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class AuthService
{
    protected EnderecoService $enderecoService;

    public function __construct(EnderecoService $enderecoService)
    {
        $this->enderecoService = $enderecoService;
    }

    /**
     * Registra um novo usuário e seu endereço.
     *
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function register(UserRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Cria o endereço
            $endereco = $this->enderecoService->create($data);

            // Criptografa a senha
            $data['password'] = Hash::make($data['password']);

            // Associa o endereço ao usuário
            $data['endereco_id'] = $endereco->id;

            // Cria o usuário
            $usuario = User::create($data);

            $usuario->load('endereco');

            $token = $usuario->createToken('auth_token')->plainTextToken;

            return response()->json([
                'usuario' => new UserResource($usuario),
                'token' => $token,
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao criar usuário.'], 500);
        }
    }

    /**
     * Realiza o login do usuário.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();

            // Verifica se é um usuário comum
            $usuario = User::where('email', $credentials['email'])->first();
            $tipo = 'web'; // Padrão para usuário comum

            // Se não encontrou na tabela `users`, tenta na `hemocentros`
            if (!$usuario) {
                $usuario = Hemocentro::where('email', $credentials['email'])->first();
                if ($usuario) {
                    // Se for um hemocentro, define o tipo como 'hemocentro'
                    $tipo = 'hemocentro';
                }
            }

            // Se não encontrar ou a senha estiver errada, retorna erro
            if (!$usuario || !Hash::check($credentials['password'], $usuario->password)) {
                return response()->json([
                    'message' => 'Credenciais inválidas. Por favor, verifique e tente novamente.'
                ], 401);
            }

            // Verifica o tipo de usuário: 'admin' ou 'user' caso seja um usuário comum
            if ($tipo === 'web') {
                $tipoUsuario = $usuario->tipo_usuario_id == 1 ? 'admin' : 'user'; // 1 é admin, 2 é user comum
            } else {
                // Para hemocentro, pode não haver necessidade de 'admin' ou 'user', apenas 'hemocentro'
                $tipoUsuario = 'hemocentro';
            }

            // Define o tempo de expiração do token com base no "Lembrar de mim"
            $expiration = $request->rememberMe ? now()->addDays(30) : now()->addHour();

            // Cria um token com permissão baseada no tipo do usuário e tempo de expiração
            $token = $usuario->createToken('auth_token', [$tipoUsuario], $expiration)->plainTextToken;

            // Carrega as relações necessárias com base no tipo de usuário
            if ($tipo === 'web') {
                $usuario->loadMissing('endereco');
                $resource = new UserResource($usuario);
            } else {
                $usuario->loadMissing('endereco', 'funcionamentos.diasSemanas');
                $resource = new HemocentroResource($usuario);
            }

            return response()->json([
                'type' => $tipoUsuario, // Tipo dinâmico: admin, user ou hemocentro
                'usuario' => $resource,
                'token' => $token,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao fazer login.'], 500);
        }
    }

    /**
     * Envia um e-mail com um link de redefinição de senha ao usuário.
     *
     * @param string $email
     * @return JsonResponse
     */
    public function sendResetLink(String $email): JsonResponse
    {
        try {
            // Encontre o usuário pelo e-mail
            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json(['error' => 'Usuário não encontrado.'], 404);
            }

            // Gera o token de redefinição de senha
            $token = Password::createToken($user);

            // Envia o e-mail personalizado
            Mail::to($email)->send(new ResetPasswordMail($token, $email));

            return Password::RESET_LINK_SENT
                ? response()->json(['message' => 'E-mail de redefinição enviado com sucesso.'])
                : response()->json(['error' => 'E-mail não encontrado ou erro ao enviar.'], 400);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao enviar Link.'], 500);
        }
    }

    /**
     * Redefine a senha do usuário usando o token de redefinição.
     *
     * @param array $data
     * @return JsonResponse
     */
    public function resetPassword(array $data): JsonResponse
    {
        try {
            $status = Password::reset($data, function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            });

            if ($status === Password::PASSWORD_RESET) {
                return response()->json(['message' => 'Senha redefinida com sucesso.']);
            }

            return response()->json(['error' => 'Token inválido ou expirado.'], 400);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao redefinir senha.'], 500);
        }
    }
}
