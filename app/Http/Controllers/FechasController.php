<?php

namespace App\Http\Controllers;

use App\Models\Fecha;
use Exception;
use Illuminate\Http\Request;
use stdClass;

class FechasController extends Controller
{
    public function getFechas($periodo)
    {
        try {
            $fechas = Fecha::where('periodo', '=', $periodo)->first();
            return $fechas ? response()->json($fechas->fechas) : response()->json([]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }
}
