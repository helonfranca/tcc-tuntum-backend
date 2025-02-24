<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\AuthService;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(UserRequest $request): JsonResponse
    {
        $usuario = $this->authService->register($request->validated());
        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'usuario' => new UserResource($usuario),
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $usuario = $this->authService->login($request->validated());

        if (!$usuario) {
            return response()->json([
                'message' => 'Credenciais inválidas'
            ], 401);
        }

        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'usuario' => new UserResource($usuario),
            'token' => $token,
        ]);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    public function sendResetLink(ForgotPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->sendResetLink($request->email);

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'E-mail de redefinição enviado com sucesso.'])
            : response()->json(['error' => 'E-mail não encontrado ou erro ao enviar.'], 400);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->resetPassword($request->only('email', 'password', 'password_confirmation', 'token'));

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Senha redefinida com sucesso.'])
            : response()->json(['error' => 'Token inválido ou expirado.'], 400);
    }
}
