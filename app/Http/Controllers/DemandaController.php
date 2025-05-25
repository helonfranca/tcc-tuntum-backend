<?php

namespace App\Http\Controllers;

use App\Http\Services\DemandaService;
use App\Http\Requests\DemandaRequest;

class DemandaController extends Controller
{
    protected DemandaService $demandaService;

    public function __construct(DemandaService $demandaService)
    {
        $this->demandaService = $demandaService;
    }

    public function index()
    {
        return $this->demandaService->listDemandas();
    }

    public function store(DemandaRequest $request)
    {
        return $this->demandaService->createDemanda($request);
    }

    public function show($id)
    {
        return $this->demandaService->showDemanda($id);
    }

    public function update(DemandaRequest $request, $id)
    {
        return $this->demandaService->updateDemanda($request, $id);
    }

    public function destroy($id)
    {
        return $this->demandaService->deleteDemanda($id);
    }
}
