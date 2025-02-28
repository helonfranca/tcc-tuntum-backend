<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiasSemana extends Model
{
    use HasFactory;

    protected $table = 'dias_semanas';
    protected $fillable = [
        'dia_semana', 'funcionamento_id'
    ];

    public function funcionamento()
    {
        return $this->belongsTo(Funcionamento::class);
    }
}
