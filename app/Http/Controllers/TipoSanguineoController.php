<?php

namespace App\Http\Controllers;

use App\Models\TipoSanguineo;

class TipoSanguineoController extends Controller
{
    /**
     * Retorna todos os tipos sanguíneos.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $tiposSanguineos = TipoSanguineo::all();

        return response()->json($tiposSanguineos);
    }
}
