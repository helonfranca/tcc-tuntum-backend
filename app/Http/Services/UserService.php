<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\Endereco;
use Illuminate\Support\Facades\Hash;

class UserService{

    public function showUser($id){
        $usuario = User::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        return $usuario;
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

    public function deleteUser($id): void
    {
        $usuario = User::find($id);
        $usuario->delete();
        if ($usuario->endereco) {
            $usuario->endereco->delete();
        }
    }
}
