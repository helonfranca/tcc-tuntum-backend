<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Hemocentro extends Model
{
    use HasApiTokens, HasFactory;

    protected $table = 'hemocentro';
    protected $fillable = [
        'nome', 'cnes', 'email', 'password', 'telefone', 'endereco_id', 'img', 'is_active'
    ];

    public function endereco()
    {
        return $this->belongsTo(Endereco::class);
    }

    public function funcionamentos()
    {
        return $this->hasMany(Funcionamento::class);
    }
}
