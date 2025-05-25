<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class DemandaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipo_sanguineo' => $this->whenLoaded('tipoSanguineo', function () {
                return [
                    'id' => $this->tipoSanguineo->id,
                    'tipofator' => $this->tipoSanguineo->tipofator,
                ];
            }),
            'hemocentro' => $this->whenLoaded('hemocentro', function () {
                return [
                    'id' => $this->hemocentro->id,
                    'nome' => $this->hemocentro->nome,
                ];
            }),
            'status' => $this->status,
            'data_inicial' => $this->data_inicial,
            'data_final' => $this->data_final
        ];
    }
}
