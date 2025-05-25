<?php

namespace App\Http\Controllers;

use App\Http\Services\DoacaoService;
use App\Http\Requests\DoacaoRequest;

class DoacaoController extends Controller
{
    protected DoacaoService $doacaoService;

    public function __construct(DoacaoService $doacaoService)
    {
        $this->doacaoService = $doacaoService;
    }

    public function index()
    {
        return $this->doacaoService->listDoacoes();
    }

    public function store(DoacaoRequest $request)
    {
        return $this->doacaoService->createDoacao($request);
    }

    public function show($id)
    {
        return $this->doacaoService->showDoacao($id);
    }

    public function update(DoacaoRequest $request, $id)
    {
        return $this->doacaoService->updateDoacao($request, $id);
    }

    public function destroy($id)
    {
        return $this->doacaoService->deleteDoacao($id);
    }

    public function proximaDoacaoDisponivel()
    {
        return $this->doacaoService->proximaDoacaoDisponivelPorUsuario();
    }
}
