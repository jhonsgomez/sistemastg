<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Historico extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'historico';

    protected $fillable = [
        'periodo_academico',
        'codigo_tg',
        'nivel',
        'estudiante',
        'correo',
        'documento',
        'celular',
        'modalidad',
        'titulo',
        'director',
        'evaluador',
        'autores',
        'inicio_tg',
        'aprobacion_propuesta',
        'final_tg'
    ];
}
