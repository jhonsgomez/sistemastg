<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoSolicitud extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tipos_solicitudes';

    protected $fillable = ['nombre', 'descripcion'];

    public function campos()
    {
        return $this->hasMany(Campo::class);
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }
}
