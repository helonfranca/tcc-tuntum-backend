<?php

namespace App\Http\Services;

use App\Http\Requests\HemocentroRequest;
use App\Mail\HemocentroCredentialsMail;
use App\Models\Hemocentro;
use App\Models\Funcionamento;
use App\Models\DiasSemana;
use App\Http\Resources\HemocentroResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class HemocentroService
{
    protected PasswordService $passwordService;
    protected EnderecoService $enderecoService;

    public function __construct(EnderecoService $enderecoService, PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
        $this->enderecoService = $enderecoService;
    }

    /**
     * Retorna todos os Hemocentros cadastrados.
     *
     * @return JsonResponse
     */
    public function getAllHemocentros(): JsonResponse
    {
        try {
            $hemocentros = HemocentroResource::collection(Hemocentro::with('endereco', 'funcionamentos.diasSemanas')->get());

            return response()->json(HemocentroResource::collection($hemocentros), 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao listar Hemocentros.'], 500);
        }
    }

    /**
     * Retorna um Hemocentro específico.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getHemocentroById(int $id): JsonResponse
    {
        try {
            $hemocentro = Hemocentro::with(['endereco', 'funcionamentos.diasSemanas'])->findOrFail($id);
            return response()->json([
                'message' => 'Hemocentro encontrado.',
                'data' => new HemocentroResource($hemocentro),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Hemocentro não encontrado.',
            ], 404);
        }
    }

    /**
     * Cria um novo Hemocentro com seu endereço e sua Imagem.
     *
     * @param HemocentroRequest $request
     * @return JsonResponse
     */
    public function createHemocentro(HemocentroRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Cria o endereço
            $endereco = $this->enderecoService->create($data);

            $data['endereco_id'] = $endereco->id;

            $senhaGerada = $this->passwordService->generateSecurePassword(15);
            $data['password'] = Hash::make($senhaGerada);

            // TODO opção provisoria para upload de imagem
            // Upload da imagem (se existir)
            if ($request->hasFile('img')) {
                // Armazena no diretório 'hemocentros' dentro de 'storage/app'
                $data['img'] = $request->file('img')->store('hemocentros');
            }

            $hemocentro = Hemocentro::create($data);

            if (isset($data['funcionamentos'])) {
                foreach ($data['funcionamentos'] as $funcionamentoData) {
                    $funcionamento = Funcionamento::create([
                        'hora_abertura' => $funcionamentoData['hora_abertura'],
                        'hora_fechamento' => $funcionamentoData['hora_fechamento'],
                        'hemocentro_id' => $hemocentro->id,
                    ]);

                    foreach ($funcionamentoData['dias_semana'] as $diaSemana) {
                        DiasSemana::create([
                            'dia_semana' => $diaSemana,
                            'funcionamento_id' => $funcionamento->id,
                        ]);
                    }
                }
            }

            Mail::to($data['email'])->send(new HemocentroCredentialsMail($data['nome'], $data['email'], $senhaGerada));

            return response()->json([
                'message' => 'Hemocentro criado com sucesso.',
                'data' => new HemocentroResource($hemocentro->load('endereco', 'funcionamentos.diasSemanas')),
            ], 201);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Erro ao criar Hemocentro',
            ], 404);
        }
    }

    /**
     * Atualiza um Hemocentro.
     *
     * @param int $id
     * @param HemocentroRequest $request
     * @return JsonResponse
     */
    public function updateHemocentro(int $id, HemocentroRequest $request): JsonResponse
    {
        try {
            // TODO verificar o pq não receber requisição form-data
            $data = $request->validated();

            $hemocentro = Hemocentro::findOrFail($id);

            $endereco = $this->enderecoService->update($id, $data);

            $hemocentro->endereco->update([
                'cep' => $data['cep'],
                'rua' => $data['rua'],
                'bairro' => $data['bairro'],
                'estado' => $data['estado'],
                'municipio' => $data['municipio'],
                'numero' => $data['numero'],
            ]);

            $data['endereco_id'] = $endereco->id;

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            //TODO opção provisoria para upload de imagem
            // Verifica se há um novo arquivo de imagem
            if ($request->hasFile('img')) {
                // Deleta a imagem antiga, se houver
                if ($hemocentro->img) {
                    Storage::delete($hemocentro->img);
                }

                // Armazena a nova imagem
                $data['img'] = $request->file('img')->store('hemocentros');
            }

            $hemocentro->update($data);

            if (isset($data['funcionamentos'])) {
                $hemocentro->funcionamentos()->delete();

                foreach ($data['funcionamentos'] as $funcionamentoData) {
                    $funcionamento = Funcionamento::create([
                        'hora_abertura' => $funcionamentoData['hora_abertura'],
                        'hora_fechamento' => $funcionamentoData['hora_fechamento'],
                        'hemocentro_id' => $hemocentro->id,
                    ]);

                    foreach ($funcionamentoData['dias_semana'] as $diaSemana) {
                        DiasSemana::create([
                            'dia_semana' => $diaSemana,
                            'funcionamento_id' => $funcionamento->id,
                        ]);
                    }
                }
            }

            return response()->json([
                'message' => 'Hemocentro atualizado com sucesso.',
                'data' => new HemocentroResource($hemocentro->load('endereco', 'funcionamentos.diasSemanas')),
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Erro ao atualizar o Hemocentro.',
            ], 404);
        }
    }

    /**
     * Exclui um Hemocentro.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function deleteHemocentro(int $id): JsonResponse
    {
        try {
            $hemocentro = Hemocentro::findOrFail($id);
            $hemocentro->funcionamentos()->delete();
            $hemocentro->delete();

            return response()->json([
                'message' => 'Hemocentro removido com sucesso.',
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Erro ao deletar o Hemocentro.',
            ], 404);
        }
    }
}
