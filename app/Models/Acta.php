<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acta extends Model
{
    use HasFactory;

    protected $table = 'actas';

    protected $fillable = [
        'numero',
        'fecha',
        'descripcion',
        'proyecto_id'
    ];

    public function proyecto()
    {
        return $this->belongsTo(Solicitud::class, 'proyecto_id');
    }
}
