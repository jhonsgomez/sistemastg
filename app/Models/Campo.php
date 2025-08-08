<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'campos';

    protected $fillable = ['tipo_solicitud_id', 'label', 'name', 'type', 'placeholder', 'required'];

    public function tipoSolicitud()
    {
        return $this->belongsTo(TipoSolicitud::class);
    }

    public function valoresCampos()
    {
        return $this->hasMany(ValorCampo::class, 'campo_id');
    }
}
