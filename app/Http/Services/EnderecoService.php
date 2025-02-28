<?php

namespace App\Http\Services;

use App\Models\Endereco;

class EnderecoService
{
    /**
     * Cria um novo endereço baseado nos dados fornecidos.
     *
     * @param array $data
     * @return Endereco
     */
    public function create(array $data): Endereco
    {
        return Endereco::create([
            'cep' => $data['cep'],
            'rua' => $data['rua'],
            'bairro' => $data['bairro'],
            'estado' => $data['estado'],
            'municipio' => $data['municipio'],
            'numero' => $data['numero'],
        ]);
    }

    public function update(int $id, array $data): Endereco
    {
        $endereco = Endereco::findOrFail($id);
        $endereco->update([
            'cep' => $data['cep'],
            'rua' => $data['rua'],
            'bairro' => $data['bairro'],
            'estado' => $data['estado'],
            'municipio' => $data['municipio'],
            'numero' => $data['numero'],
        ]);

        return $endereco;
    }
}
