<?php

namespace App\Http\Services;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected EnderecoService $enderecoService;

    public function __construct(EnderecoService $enderecoService)
    {
        $this->enderecoService = $enderecoService;
    }

    /**
     * Retorna uma lista de usuários com pagina o.
     *
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function listUser(): JsonResponse|AnonymousResourceCollection
    {
        try {
            $usuarios = User::with('endereco')
                ->where('tipo_usuario_id', '!=', 1)
                ->paginate(10);
            return UserResource::collection($usuarios->load('endereco'));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao listar usuários.'], 500);
        }
    }

    /**
     * Retorna um usuário específico.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function showUser(int $id): JsonResponse
    {
        try {
            $usuario = User::find($id);

            if (!$usuario) {
                return response()->json(['message' => 'Usuário não encontrado'], 404);
            }

            return response()->json([
                'user' => new UserResource($usuario->load('endereco'))
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar Usuário.'], 500);
        }
    }

    /**
     * Cria um novo usuário e seu endereço.
     *
     * @param UserRequest $data
     * @return JsonResponse
     */
    public function createUser(UserRequest $data): JsonResponse
    {
        try {
            $data = $data->validated();

            $endereco = $this->enderecoService->create($data);

            $data['password'] = Hash::make($data['password']);
            $data['endereco_id'] = $endereco->id;

            $usuario = User::create($data);

            return response()->json([
                'message' => 'Usuário criado com sucesso',
                'data' => new UserResource($usuario->load('endereco'))
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao criar usuário.'], 500);
        }
    }

    /**
     * Atualiza um usuário e seu endereço.
     *
     * @param int $id
     * @param UserRequest $data
     * @return JsonResponse
     */
    public function updateUser(int $id, UserRequest $data): JsonResponse
    {
        try {

            $usuario = User::find($id);

            if (!$usuario) {
                return response()->json(['message' => 'Usuário não encontrado'], 404);
            }

            $data = $data->validated();

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

            return response()->json([
                'message' => 'Usuário atualizado com sucesso',
                'data' => new UserResource($usuario->load('endereco'))
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar Usuário.'], 500);
        }
    }

    /**
     * Exclui um usuário.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function deleteUser(int $id): JsonResponse
    {
        try {
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

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar usuário.'], 500);
        }
    }
}
