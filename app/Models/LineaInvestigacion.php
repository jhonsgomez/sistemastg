<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LineaInvestigacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lineas_investigacion';

    protected $fillable = ['nombre', 'descripcion'];
}
