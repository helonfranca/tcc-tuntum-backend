<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Doador extends Model
{
    use HasFactory;

    protected $table = 'doador';

    protected $fillable = [
        'apto',
        'malaria',
        'hiv',
        'droga_ilicita',
        'hepatiteb',
        'hepatitec',
        'usuario_id',
        'tipo_sanguineo_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function tipoSanguineo()
    {
        return $this->belongsTo(TipoSanguineo::class, 'tipo_sanguineo_id');
    }
}
