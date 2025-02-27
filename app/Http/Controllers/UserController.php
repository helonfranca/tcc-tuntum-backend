<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        return $this->userService->listUser();
    }

    public function store(UserRequest $request): JsonResponse
    {
        return $this->userService->createUser($request);
    }

    public function show($id): JsonResponse
    {
        return $this->userService->showUser($id);
    }

    public function update(UserRequest $request, $id): JsonResponse
    {
        return $this->userService->updateUser($id, $request);
    }

    public function destroy($id): JsonResponse
    {
        return $this->userService->deleteUser($id);
    }
}
