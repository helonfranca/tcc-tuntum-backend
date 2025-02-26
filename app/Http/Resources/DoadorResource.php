<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoadorResource extends JsonResource
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
            'apto' => $this->apto,
            'malaria' => $this->malaria,
            'hiv' => $this->hiv,
            'droga_ilicita' => $this->droga_ilicita,
            'hepatiteb' => $this->hepatiteb,
            'hepatitec' => $this->hepatitec,
            'usuario_id' => $this->usuario_id,
            'tipo_sanguineo' => $this->whenLoaded('tipoSanguineo', function () {
                return [
                    'id' => $this->tipoSanguineo->id,
                    'tipo' => $this->tipoSanguineo->tipofator,
                ];
            }),
        ];
    }
}
