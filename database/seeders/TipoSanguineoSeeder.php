<?php

namespace Database\Seeders;

use App\Models\TipoSanguineo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoSanguineoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoSanguineo::create(['tipofator' => 'A+']);
        TipoSanguineo::create(['tipofator' => 'A-']);
        TipoSanguineo::create(['tipofator' => 'B+']);
        TipoSanguineo::create(['tipofator' => 'B-']);
        TipoSanguineo::create(['tipofator' => 'AB+']);
        TipoSanguineo::create(['tipofator' => 'AB-']);
        TipoSanguineo::create(['tipofator' => 'O+']);
        TipoSanguineo::create(['tipofator' => 'O-']);
    }
}
