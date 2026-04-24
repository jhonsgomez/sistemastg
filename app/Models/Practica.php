<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Practica extends Model{

    use HasFactory, SoftDeletes;

    protected $table = 'practicas';

    protected $fillable = [
        'user_id',
        'tipo_solicitud_id',
        'estado',
        'vencido',
        'deshabilitado'
    ];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con el tipo de solicitud (practicas_fase_0)
    public function tipoSolicitud()
    {
        return $this->belongsTo(TipoSolicitud::class);
    }

    // Relación con los valores de campos dinámicos
    public function valoresCampos()
    {
        return $this->hasMany(PracticaValorCampo::class);
    }

    // Método para obtener todos los campos con sus respectivos valores
    public function camposConValores()
{
    if (!$this->relationLoaded('valoresCampos')) {
        $this->load('valoresCampos.campo');
    }

    return $this->valoresCampos->map(function ($valorCampo) {
        return [
            'campo' => $valorCampo->campo->name ?? null,
            'valor' => $valorCampo->valor
        ];
    });
}
}