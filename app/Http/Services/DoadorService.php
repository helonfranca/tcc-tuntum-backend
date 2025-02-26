<?php

namespace App\Http\Services;

use App\Models\Doador;
use App\Http\Resources\DoadorResource;
use App\Http\Requests\DoadorRequest;
use Illuminate\Http\JsonResponse;

class DoadorService
{
    public function listDoadores(): JsonResponse
    {
        try {
            $doadores = DoadorResource::collection(Doador::with('tipoSanguineo')->get());
            return response()->json($doadores);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao listar doadores.'], 500);
        }
    }

    public function createDoador(DoadorRequest $request): JsonResponse
    {
        try {
            $dados = $request->validated();
            $doadorExistente = Doador::where('usuario_id', $dados['usuario_id'])->first();

            if ($doadorExistente) {
                return response()->json(['error' => 'Este usuário já possui um cadastro de doador.'], 422);
            }

            // verifica se algum dos campos é true
            $inapto = $dados['malaria'] || $dados['hiv'] || $dados['droga_ilicita'] || $dados['hepatiteb'] || $dados['hepatitec'];

            // define o valor de 'apto' com base na verificação
            $dados['apto'] = !$inapto;

            $doador = Doador::create($dados);
            $doador->load('tipoSanguineo');

            return response()->json(new DoadorResource($doador), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao criar doador.'], 500);
        }
    }

    public function showDoador($id): JsonResponse
    {
        try {
            $doador = Doador::with(['user', 'tipoSanguineo'])->find($id);

            if (!$doador) {
                return response()->json(['error' => 'Doador não encontrado.'], 404);
            }

            return response()->json(new DoadorResource($doador));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar doador.'], 500);
        }
    }

    public function updateDoador(DoadorRequest $request, $id): JsonResponse
    {
        try {
            $doador = Doador::find($id);

            if (!$doador) {
                return response()->json(['error' => 'Doador não encontrado.'], 404);
            }

            $dados = $request->validated();

            // Verifica se algum dos campos é true
            $inapto = $dados['malaria'] || $dados['hiv'] || $dados['droga_ilicita'] || $dados['hepatiteb'] || $dados['hepatitec'];

            // Define o valor de 'apto' com base na verificação
            $dados['apto'] = !$inapto;

            $doador->update($dados);
            $doador->load('tipoSanguineo');

            return response()->json(new DoadorResource($doador));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar doador.'], 500);
        }
    }

    public function deleteDoador($id): JsonResponse
    {
        try {
            $doador = Doador::find($id);

            if (!$doador) {
                return response()->json(['error' => 'Doador não encontrado.'], 404);
            }

            $doador->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao excluir doador.'], 500);
        }
    }
}
