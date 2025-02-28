<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcionamento extends Model
{
    use HasFactory;

    protected $table = 'funcionamento';
    protected $fillable = [
        'hora_abertura', 'hora_fechamento', 'hemocentro_id'
    ];

    public function hemocentro()
    {
        return $this->belongsTo(Hemocentro::class);
    }

    public function diasSemanas()
    {
        return $this->hasMany(DiasSemana::class);
    }
}
