<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $usuarios = User::with('endereco')
            ->where('tipo_usuario_id', '!=', 1)
            ->paginate(10);
        return UserResource::collection($usuarios);
    }

    public function store(UserRequest $request): JsonResponse
    {
        $usuario = $this->userService->createUser($request->validated());
        return response()->json(new UserResource($usuario), 201);
    }

    public function show($id): JsonResponse
    {
        return $this->userService->showUser($id);
    }

    public function update(UserRequest $request, $id): JsonResponse
    {
        $usuario = $this->userService->updateUser($id, $request->validated());

        return response()->json([
            'message' => 'Usuário atualizado com sucesso',
            'data' => new UserResource($usuario)
        ]);
    }

    public function destroy($id): JsonResponse
    {
        return $this->userService->deleteUser($id);
    }
}
