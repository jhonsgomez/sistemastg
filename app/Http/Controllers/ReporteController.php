<?php

namespace App\Http\Controllers;

use App\Mail\ReportesMail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ReporteController extends Controller
{
    public function enviarReporte(Request $request)
    {
        $mensaje = null;
        try {
            $validator = Validator::make($request->all(), [
                'mensaje_warning' => 'required',
            ], [
                'mensaje_warning.required' => 'La descripción del reporte es obligatoria',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $mensaje = $request->mensaje_warning;

            $modulo = $request->modulo ?? 'PROYECTOS DE GRADO';

            $this->sendEmail($mensaje, $modulo);

            return response()->json(['mensaje' => $mensaje], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function sendEmail($mensaje, $modulo = 'PROYECTOS DE GRADO')
    {
        try {
            $asunto_correo = 'PQRSD - SOFTWARE DE ' . $modulo;

            $cuerpo_correo = [
                'mensaje' => $mensaje,
                'email' => auth()->user()->email,
                'modulo' => $modulo,
            ];

            Mail::queue(new ReportesMail($asunto_correo, $cuerpo_correo));
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }
}
