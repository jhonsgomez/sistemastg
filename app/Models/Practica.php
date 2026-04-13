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
        'data'
    ];
}
