<?php

namespace App\Http\Services;

use App\Models\Demanda;
use App\Models\Doador;
use App\Http\Resources\DemandaResource;
use App\Http\Requests\DemandaRequest;
use App\Models\TipoSanguineo;
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
            $user = auth()->user();
            $isHemocentroUser = false;

            $query = Demanda::with(['tipoSanguineo', 'hemocentro']);

            if ($user && isset($user->cnes) && !empty(trim($user->cnes))) {
                $isHemocentroUser = true;
            }

            if (!$isHemocentroUser) {
                $query->whereHas('hemocentro', function ($q) {
                    $q->where('is_Active', 1);
                });

                $query->where('status', 'aberta');
            }

            if (request()->has('tipo_sanguineo')) {
                $tipoDoador = request('tipo_sanguineo');
                $tiposReceptoresCompativeis = $this->getTiposReceptoresCompativeis($tipoDoador);

                if (!empty($tiposReceptoresCompativeis)) {
                    $query->whereHas('tipoSanguineo', function ($q) use ($tiposReceptoresCompativeis) {
                        $q->whereIn('tipofator', $tiposReceptoresCompativeis);
                    });
                } else {
                    return response()->json(DemandaResource::collection([]));
                }
            }

            if (request()->has('hemocentro_id')) {
                $hemocentroId = request('hemocentro_id');
                $query->where('hemocentro_id', $hemocentroId);
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
        if (!$demanda->tipoSanguineo) {
            return;
        }

        $tipoReceptor = $demanda->tipoSanguineo->tipofator;
        $tiposDoadoresCompativeisStrings = $this->getTiposDoadoresCompativeisParaReceptor($tipoReceptor);

        if (empty($tiposDoadoresCompativeisStrings)) {
            return;
        }

        // Buscar os IDs dos tipos sanguíneos dos doadores compatíveis
        $idsTiposSanguineosDoadores = TipoSanguineo::whereIn('tipofator', $tiposDoadoresCompativeisStrings)
            ->pluck('id')
            ->toArray();

        if (empty($idsTiposSanguineosDoadores)) {
            return;
        }

        // 3. Buscar os doadores aptos com os tipos sanguíneos compatíveis
        $doadores = Doador::with(['user', 'tipoSanguineo'])
            ->whereIn('tipo_sanguineo_id', $idsTiposSanguineosDoadores)
            ->where('apto', true)
            ->get();

        foreach ($doadores as $doador) {
            if ($doador->user && $doador->user->email) {
                Mail::to($doador->user->email)->send(new NovaDemandaEmail($demanda, $doador));
            }
        }
    }

    /**
     * Retorna uma lista de tipos sanguíneos que podem receber do tipo doador especificado.
     *
     * @param string $tipoDoador O tipo sanguíneo do doador (ex: "A+", "O-")
     * @return array Lista de tipos sanguíneos receptores compatíveis.
     */
    private function getTiposReceptoresCompativeis(string $tipoDoador): array
    {
        $compatibilidade = [
            'O-' => ['O-', 'O+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+'],
            'O+' => ['O+', 'A+', 'B+', 'AB+'],
            'A-' => ['A-', 'A+', 'AB-', 'AB+'],
            'A+' => ['A+', 'AB+'],
            'B-' => ['B-', 'B+', 'AB-', 'AB+'],
            'B+' => ['B+', 'AB+'],
            'AB-' => ['AB-', 'AB+'],
            'AB+' => ['AB+'],
        ];

        return $compatibilidade[$tipoDoador] ?? [];
    }

    /**
     * Retorna uma lista de 'tipofator' de tipos sanguíneos que podem doar para o tipo receptor especificado.
     *
     * @param string $tipoReceptor O 'tipofator' do sangue da demanda (receptor).
     * @return array Lista de 'tipofator' de doadores compatíveis.
     */
    private function getTiposDoadoresCompativeisParaReceptor(string $tipoReceptor): array
    {
        $compatibilidade = [
            'O-'  => ['O-'],
            'O+'  => ['O-', 'O+'],
            'A-'  => ['O-', 'A-'],
            'A+'  => ['O-', 'O+', 'A-', 'A+'],
            'B-'  => ['O-', 'B-'],
            'B+'  => ['O-', 'O+', 'B-', 'B+'],
            'AB-' => ['O-', 'A-', 'B-', 'AB-'],
            'AB+' => ['O-', 'O+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+'],
        ];

        return $compatibilidade[$tipoReceptor] ?? [];
    }
}
