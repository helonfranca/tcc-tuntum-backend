<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'data_nascimento' => $this->data_nascimento,
            'sexo' => $this->sexo,
            'cpf' => $this->cpf,
            'telefone' => $this->telefone,
            'tipo_usuario_id' => $this->tipo_usuario_id,
            'endereco' => $this->endereco ? [
                'cep' => $this->endereco->cep,
                'rua' => $this->endereco->rua,
                'bairro' => $this->endereco->bairro,
                'estado' => $this->endereco->estado,
                'municipio' => $this->endereco->municipio,
                'numero' => $this->endereco->numero,
            ] : null,
        ];
    }
}

