<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HemocentroResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'cnes' => $this->cnes,
            'email' => $this->email,
            'telefone' => $this->telefone,
            'img' => asset('storage/' . $this->img),
            'endereco' => $this->whenLoaded('endereco', function () {
                return [
                    'cep' => $this->endereco->cep,
                    'rua' => $this->endereco->rua,
                    'bairro' => $this->endereco->bairro,
                    'estado' => $this->endereco->estado,
                    'municipio' => $this->endereco->municipio,
                    'numero' => $this->endereco->numero,
                ];
            }),
            'funcionamentos' => $this->whenLoaded('funcionamentos', function () {
                return $this->funcionamentos->map(function ($funcionamento) {
                    return [
                        'hora_abertura' => $funcionamento->hora_abertura,
                        'hora_fechamento' => $funcionamento->hora_fechamento,
                        'dias_semana' => $funcionamento->relationLoaded('diasSemanas')
                            ? $funcionamento->diasSemanas->pluck('dia_semana')
                            : [],
                    ];
                });
            }),
        ];
    }
}
