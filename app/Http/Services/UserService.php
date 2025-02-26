<?php

namespace App\Http\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Endereco;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserService
{

    public function showUser($id): JsonResponse
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        // Retorna a resposta com o usuário formatado pela resource
        return response()->json([
            'user' => new UserResource($usuario)
        ]);
    }

    public function createUser(array $data): User
    {
        $endereco = Endereco::create([
            'cep' => $data['cep'],
            'rua' => $data['rua'],
            'bairro' => $data['bairro'],
            'estado' => $data['estado'],
            'municipio' => $data['municipio'],
            'numero' => $data['numero'],
        ]);
        $data['password'] = Hash::make($data['password']);
        $data['endereco_id'] = $endereco->id;

        return User::create($data);
    }

    public function updateUser($id, array $data): User
    {
        $usuario = User::find($id);
        $usuario->endereco->update([
            'cep' => $data['cep'],
            'rua' => $data['rua'],
            'bairro' => $data['bairro'],
            'estado' => $data['estado'],
            'municipio' => $data['municipio'],
            'numero' => $data['numero'],
        ]);

        // Atualiza o usuário
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $usuario->update($data);

        return $usuario;
    }

    public function deleteUser($id): JsonResponse
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuário não encontrado'
            ], 404);
        }

        $usuario->delete();
        if ($usuario->endereco) {
            $usuario->endereco->delete();
        }

        return response()->json([
            'message' => 'Usuário deletado com sucesso'
        ], 204);
    }
}
