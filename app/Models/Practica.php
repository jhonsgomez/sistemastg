<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Practica extends Model
{

    use HasFactory;
    protected $fillable = [
        'user_id',
        'tipo_solicitud_id',
        'data',
        'estado',
        'vencido',
        'deshabilitado'
    ];

     // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con el tipo de solicitud (practicas_fase_0)
    public function tipoSolicitud()
    {
        return $this->belongsTo(TipoSolicitud::class);
    }
}
