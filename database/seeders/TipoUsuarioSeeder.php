<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoUsuario;
use App\Models\User;
use App\Models\Endereco;
use Illuminate\Support\Facades\Hash;

class TipoUsuarioSeeder extends Seeder
{
    /**
     * Executa o seeder.
     *
     * @return void
     */
    public function run()
    {
        // Cria o tipo de usuário Administrador
        TipoUsuario::create([
            'id' => 1,
            'descricao' => 'Administrador',
        ]);

        // Cria o tipo de usuário Doador
        TipoUsuario::create([
            'id' => 2,
            'descricao' => 'Doador',
        ]);

        // Cria um endereço para o usuário administrador
        $endereco = Endereco::create([
            'cep' => '12345-678',
            'rua' => 'Rua do Administrador',
            'bairro' => 'Centro',
            'estado' => 'SP',
            'municipio' => 'São Paulo',
            'numero' => 123,
        ]);

        // Cria o usuário administrador
        User::create([
            'nome' => 'Admin',
            'password' => Hash::make('senha123'),
            'data_nascimento' => '1990-01-01',
            'email' => 'admin@example.com',
            'sexo' => 'Masculino',
            'cpf' => '12345678900',
            'telefone' => '11987654321',
            'tipo_usuario_id' => 1, // Administrador
            'endereco_id' => $endereco->id,
        ]);
    }
}
