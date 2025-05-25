<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demanda extends Model
{
    use HasFactory;

    protected $table = 'demanda';

    protected $fillable = [
        'tipo_sanguineo_id',
        'hemocentro_id',
        'status',
        'data_inicial',
        'data_final'
    ];

    public function tipoSanguineo()
    {
        return $this->belongsTo(TipoSanguineo::class, 'tipo_sanguineo_id');
    }

    public function hemocentro()
    {
        return $this->belongsTo(Hemocentro::class, 'hemocentro_id');
    }

    public function doacoes()
    {
        return $this->hasMany(Doacao::class, 'demanda_id');
    }
}
