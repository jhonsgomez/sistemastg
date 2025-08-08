<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ValorCampo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'valores_campos';

    protected $fillable = ['solicitud_id', 'campo_id', 'valor'];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function campo()
    {
        return $this->belongsTo(Campo::class, 'campo_id');
    }

    public function valor()
    {
        return $this->valor;
    }
}
