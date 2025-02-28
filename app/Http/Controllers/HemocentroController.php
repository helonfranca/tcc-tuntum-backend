<?php

namespace App\Http\Controllers;

use App\Http\Services\HemocentroService;
use App\Http\Requests\HemocentroRequest;
use Illuminate\Http\JsonResponse;

class HemocentroController extends Controller
{
    protected HemocentroService $hemocentroService;

    public function __construct(HemocentroService $hemocentroService)
    {
        $this->hemocentroService = $hemocentroService;
    }

    public function index(): JsonResponse
    {
        return $this->hemocentroService->getAllHemocentros();
    }

    public function store(HemocentroRequest $request): JsonResponse
    {
        return $this->hemocentroService->createHemocentro($request);
    }

    public function show(int $id): JsonResponse
    {
        return $this->hemocentroService->getHemocentroById($id);
    }

    public function update(HemocentroRequest $request, int $id): JsonResponse
    {
        return $this->hemocentroService->updateHemocentro($id, $request);
    }

    public function destroy($id): JsonResponse
    {
        return $this->hemocentroService->deleteHemocentro($id);
    }
}
