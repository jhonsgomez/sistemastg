<?php

namespace App\Http\Controllers;

use App\Models\LineaInvestigacion;
use Illuminate\Http\Request;

class LineasInvestigacionController extends Controller
{
    public function getLinea($id)
    {
        $linea_investigacion = LineaInvestigacion::findOrFail($id);
        return response()->json($linea_investigacion);
    }
}
