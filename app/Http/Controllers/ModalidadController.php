<?php

namespace App\Http\Controllers;

use App\Models\Modalidad;
use Illuminate\Http\Request;

class ModalidadController extends Controller
{
    public function getModalidad($id)
    {
        $modalidad = Modalidad::findOrFail($id);
        return response()->json($modalidad);
    }
}
