<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
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
            'tipo_usuario' => $this->tipoUsuario,
            'endereco' => $this->endereco,
        ];
    }
}

