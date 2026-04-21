<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActaPractica extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'actas_practicas';

    protected $fillable = [
        'practica_id',
        'numero',
        'fecha',
        'descripcion',
    ];

    public function practica()
    {
        return $this->belongsTo(Practica::class);
    }
}