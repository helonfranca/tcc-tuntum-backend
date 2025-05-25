<?php

namespace App\Http\Resources;

use App\Models\Hemocentro;
use App\Models\TipoSanguineo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoacaoResource extends JsonResource
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
            'data_doacao' => $this->data_doacao,
            'quantidade' => $this->quantidade,
            'status' => $this->status,
            'doador' => $this->whenLoaded('doador', function () {
                return [
                    'id' => $this->doador->id,
                    'nome' => $this->doador->user->nome,
                ];
            }),
            'demanda' => $this->whenLoaded('demanda', function () {
                return [
                    'id' => $this->demanda->id,
                    'status' => $this->demanda->status,
                ];
            }),
            'tipo_sanguineo' => [
                'id' => $this->demanda->tipo_sanguineo_id,
                'tipo' => TipoSanguineo::find($this->demanda->tipo_sanguineo_id)?->tipofator,
            ],
            'hemocentro' => [
                'id' => $this->demanda->hemocentro_id,
                'nome' => Hemocentro::find($this->demanda->hemocentro_id)?->nome,
            ],
        ];
    }
}
