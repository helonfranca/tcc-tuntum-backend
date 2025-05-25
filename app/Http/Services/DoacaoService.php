<?php

namespace App\Http\Services;

use App\Models\Doacao;
use App\Http\Resources\DoacaoResource;
use App\Http\Requests\DoacaoRequest;
use App\Models\Doador;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DoacaoService
{
    /**
     * Lista todas as doações com suas relações.
     *
     * @return JsonResponse Retorna uma resposta JSON contendo todas as doações.
     */
    public function listDoacoes(): JsonResponse
    {
        try {
            $doacoes = DoacaoResource::collection(Doacao::with(['doador.user', 'demanda'])->get());
            return response()->json($doacoes);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao listar doações.'], 500);
        }
    }

    /**
     * Cria uma nova doação com os dados validados.
     *
     * @param DoacaoRequest $request Objeto contendo os dados validados para a doação.
     * @return JsonResponse Retorna a doação criada como resposta JSON.
     */
    public function createDoacao(DoacaoRequest $request): JsonResponse
    {
        try {
            $dados = $request->validated();

            $doador = Doador::find($dados['doador_id']);
            if (!$doador) {
                return response()->json(['error' => 'Doador não encontrado.'], 404);
            }

            // Verifica se há doação pendente
            $erroPendente = $this->verificaDoacaoPendente($doador);
            if ($erroPendente) return $erroPendente;

            // Verifica intervalo entre doações
            $erroIntervalo = $this->verificaIntervaloEntreDoacoes($doador);
            if ($erroIntervalo) return $erroIntervalo;

            $doacao = Doacao::create($dados);
            $doacao->load(['doador.user', 'demanda']);

            return response()->json(new DoacaoResource($doacao), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao criar doação.'], 500);
        }
    }

    /**
     * Verifica se há doação pendente para o doador.
     *
     * @param Doador $doador Objeto doador a ser verificado.
     * @return JsonResponse|null Retorna uma resposta JSON caso haja doação pendente ou null se não houver.
     */
    private function verificaDoacaoPendente(Doador $doador): ?JsonResponse
    {
        $pendente = Doacao::where('doador_id', $doador->id)
            ->where('status', 'pendente')
            ->exists();

        if ($pendente) {
            return response()->json(['error' => 'Já existe uma doação pendente para este doador.'], 422);
        }

        return null;
    }

    /**
     * Verifica o intervalo mínimo entre doações do doador.
     *
     * @param Doador $doador Objeto doador a ser verificado.
     * @return JsonResponse|null Retorna uma resposta JSON caso o intervalo não seja respeitado ou null caso contrário.
     */
    private function verificaIntervaloEntreDoacoes(Doador $doador): ?JsonResponse
    {
        $ultimaDoacao = Doacao::where('doador_id', $doador->id)
            ->where('status', 'confirmada')
            ->latest('updated_at')
            ->first();

        if ($ultimaDoacao) {
            $dataUltima = $ultimaDoacao->updated_at;
            $dataAtual = Carbon::now();

            $intervaloMinimo = $doador->user->sexo === 'feminino' ? 90 : 60;

            if ($dataUltima->diffInDays($dataAtual) < $intervaloMinimo) {
                return response()->json([
                    'error' => "Intervalo mínimo entre doações não respeitado. Próxima doação permitida após " .
                        $dataUltima->addDays($intervaloMinimo)->format('d/m/Y')
                ], 422);
            }
        }
        return null;
    }

    /**
     * Retorna uma doação específica pelo ID.
     *
     * @param int $id ID da doação.
     * @return JsonResponse Retorna a doação encontrada como resposta JSON.
     */
    public function showDoacao($id): JsonResponse
    {
        try {
            $doacao = Doacao::with(['doador.user', 'demanda'])->find($id);

            if (!$doacao) {
                return response()->json(['error' => 'Doação não encontrada.'], 404);
            }

            return response()->json(new DoacaoResource($doacao));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar doação.'], 500);
        }
    }

    /**
     * Atualiza uma doação específica pelo ID com os dados validados.
     *
     * @param DoacaoRequest $request Objeto contendo os dados validados para atualização.
     * @param int $id ID da doação a ser atualizada.
     * @return JsonResponse Retorna a doação atualizada como resposta JSON.
     */
    public function updateDoacao(DoacaoRequest $request, $id): JsonResponse
    {
        try {
            $doacao = Doacao::find($id);

            if (!$doacao) {
                return response()->json(['error' => 'Doação não encontrada.'], 404);
            }

            $dados = $request->validated();
            $doacao->update($dados);
            $doacao->load(['doador.user', 'demanda']);

            return response()->json(new DoacaoResource($doacao));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar doação.'], 500);
        }
    }

    /**
     * Deleta uma doação específica pelo ID.
     *
     * @param int $id ID da doação a ser deletada.
     * @return JsonResponse Retorna uma resposta JSON vazia com código HTTP 204 em caso de sucesso.
     */
    public function deleteDoacao($id): JsonResponse
    {
        try {
            $doacao = Doacao::find($id);

            if (!$doacao) {
                return response()->json(['error' => 'Doação não encontrada.'], 404);
            }

            $doacao->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao excluir doação.'], 500);
        }
    }

    /**
     * Retorna quando o doador poderá doar novamente e quantos dias faltam.
     *
     * @return JsonResponse Retorna a data da próxima doação disponível, os dias restantes e a aptidão atual do doador.
     */
    public function proximaDoacaoDisponivelPorUsuario(): JsonResponse
    {
        try {
            $usuario = auth()->user();

            $doador = Doador::where('usuario_id', $usuario->id)
                ->with('user')
                ->first();

            if (!$doador) {
                return response()->json(['error' => 'Doador não encontrado.'], 404);
            }

            $ultimaDoacao = Doacao::where('doador_id', $doador->id)
                ->where('status', 'confirmada')
                ->latest('updated_at')
                ->first();

            if (!$ultimaDoacao) {
                return response()->json([
                    'mensagem' => 'Você está apto para doar! Encontre um hemocentro próximo e faça a diferença!.',
                    'pode_doar_hoje' => true
                ]);
            }

            $intervaloMinimo = $doador->user->sexo === 'feminino' ? 90 : 60;
            $dataProxima = $ultimaDoacao->updated_at->copy()->addDays($intervaloMinimo);
            $hoje = Carbon::now();
            $diasRestantes = (int)max(0, $hoje->diffInDays($dataProxima, false));

            return response()->json([
                'data_proxima_doacao' => $dataProxima->format('d/m/Y'),
                'dias_restantes' => $diasRestantes,
                'pode_doar_hoje' => $diasRestantes <= 0,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao verificar próxima doação.'], 500);
        }
    }

}
