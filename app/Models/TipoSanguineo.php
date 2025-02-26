<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoSanguineo extends Model
{
    use HasFactory;

    protected $table = 'tipo_sanguineo';

    protected $fillable = [
        'tipofator',
    ];
}
