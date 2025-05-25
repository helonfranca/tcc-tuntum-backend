<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doacao extends Model
{
    use HasFactory;

    protected $table = 'doacao';

    protected $fillable = [
        'data_doacao',
        'quantidade',
        'doador_id',
        'status',
        'demanda_id',
    ];

    public function doador()
    {
        return $this->belongsTo(Doador::class, 'doador_id');
    }

    public function demanda()
    {
        return $this->belongsTo(Demanda::class, 'demanda_id');
    }
}
