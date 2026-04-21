<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PracticaValorCampo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'practica_valores_campos';

    protected $fillable = [
        'practica_id',
        'campo_id',
        'valor',
    ];

    public function campo()
    {
        return $this->belongsTo(Campo::class);
    }

    public function practica()
    {
        return $this->belongsTo(Practica::class);
    }
}