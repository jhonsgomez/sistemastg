<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoDocumento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tipos_documentos';

    protected $fillable = ['nombre', 'tag', 'descripcion'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
