<?php

namespace App\Http\Services;

use App\Models\Demanda;
use App\Models\Doador;
use App\Http\Resources\DemandaResource;
use App\Http\Requests\DemandaRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\NovaDemandaEmail;

class DemandaService
{
    /**
     * Lista todas as demandas, com opção de filtrar por tipo sanguíneo.
     *
     * @return JsonResponse Lista de demandas ou erro.
     */

    public function listDemandas(): JsonResponse
    {
        try {
            $query = Demanda::with(['tipoSanguineo', 'hemocentro']);

            if (request()->has('tipo_sanguineo')) {
                $tipo = request('tipo_sanguineo');

                $query->whereHas('tipoSanguineo', function ($q) use ($tipo) {
                    $q->where('tipofator', $tipo);
                });
            }

            $demandas = DemandaResource::collection($query->get());

            return response()->json($demandas);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao listar demandas.'], 500);
        }
    }

    /**
     * Cria uma nova demanda e notifica doadores.
     *
     * @param DemandaRequest $request Dados validados da demanda.
     * @return JsonResponse Demanda criada ou erro.
     */
    public function createDemanda(DemandaRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $dados = $request->validated();
            $demanda = Demanda::create($dados);

            // Enviar e-mails para doadores com o mesmo tipo sanguíneo
            $this->notificarDoadores($demanda);
            DB::commit();

            $demanda->load(['tipoSanguineo', 'hemocentro']);
            return response()->json(new DemandaResource($demanda), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao criar demanda.'], 500);
        }
    }

    /**
     * Exibe os dados de uma demanda específica.
     *
     * @param int $id ID da demanda.
     * @return JsonResponse Dados da demanda ou erro.
     */
    public function showDemanda($id): JsonResponse
    {
        try {
            $demanda = Demanda::with(['tipoSanguineo', 'hemocentro'])->find($id);

            if (!$demanda) {
                return response()->json(['error' => 'Demanda não encontrada.'], 404);
            }

            return response()->json(new DemandaResource($demanda));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar demanda.'], 500);
        }
    }

    /**
     * Atualiza uma demanda existente.
     *
     * @param DemandaRequest $request Dados validados para atualização.
     * @param int $id ID da demanda.
     * @return JsonResponse Demanda atualizada ou erro.
     */
    public function updateDemanda(DemandaRequest $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $demanda = Demanda::find($id);

            if (!$demanda) {
                return response()->json(['error' => 'Demanda não encontrada.'], 404);
            }

            $dados = $request->validated();
            $demanda->update($dados);
            $demanda->load(['tipoSanguineo', 'hemocentro']);

            DB::commit();

            return response()->json(new DemandaResource($demanda));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao atualizar demanda.'], 500);
        }
    }

    /**
     * Exclui uma demanda pelo ID informado.
     *
     * @param int $id ID da demanda.
     * @return JsonResponse Resposta de sucesso ou erro.
     */
    public function deleteDemanda($id): JsonResponse
    {
        try {
            $demanda = Demanda::find($id);

            if (!$demanda) {
                return response()->json(['error' => 'Demanda não encontrada.'], 404);
            }

            $demanda->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao excluir demanda.'], 500);
        }
    }

    /**
     * Notifica doadores com o mesmo tipo sanguíneo.
     *
     * @param Demanda $demanda Demanda usada como filtro.
     * @return void
     */
    protected function notificarDoadores(Demanda $demanda): void
    {
        $doadores = Doador::with(['user', 'tipoSanguineo'])
            ->where('tipo_sanguineo_id', $demanda->tipo_sanguineo_id)
            ->where('apto', true)
            ->get();

        foreach ($doadores as $doador) {
            Mail::to($doador->user->email)->send(new NovaDemandaEmail($demanda, $doador));
        }
    }
}
