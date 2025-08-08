<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solicitud extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'solicitudes';

    protected $fillable = ['user_id', 'tipo_solicitud_id', 'descripcion', 'estado', 'vencido', 'deshabilitado'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tipoSolicitud()
    {
        return $this->belongsTo(TipoSolicitud::class);
    }

    public function valoresCampos()
    {
        return $this->hasMany(ValorCampo::class);
    }

    // Método para obtener todos los campos asociados a la solicitud
    public function campos()
    {
        return $this->tipoSolicitud->campos;
    }

    // Método para obtener todos los campos con sus respectivos valores
    public function camposConValores()
    {   
        return $this->valoresCampos->map(function ($valorCampo) {
            return [
                'campo' => $valorCampo->campo,
                'valor' => $valorCampo->valor
            ];
        });
    }
}
