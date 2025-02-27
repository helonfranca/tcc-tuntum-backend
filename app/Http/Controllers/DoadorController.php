<?php

namespace App\Http\Controllers;

use App\Http\Services\DoadorService;
use App\Http\Requests\DoadorRequest;

class DoadorController extends Controller
{
    protected DoadorService $doadorService;

    public function __construct(DoadorService $doadorService)
    {
        $this->doadorService = $doadorService;
    }

    public function index()
    {
        return $this->doadorService->listDoadores();
    }

    public function store(DoadorRequest $request)
    {
        return $this->doadorService->createDoador($request);
    }

    public function show($id)
    {
        return $this->doadorService->showDoador($id);
    }

    public function update(DoadorRequest $request, $id)
    {
        return $this->doadorService->updateDoador($request, $id);
    }

    public function destroy($id)
    {
        return $this->doadorService->deleteDoador($id);
    }
}
