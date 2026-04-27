<?php

namespace App\Http\Controllers;

use App\Models\Campo;
use App\Models\Practica;
use App\Models\PracticaValorCampo;
use App\Models\TipoSolicitud;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoadMapPracticaController extends Controller
{
    public function getType($name)
    {
        return TipoSolicitud::where('nombre', $name)->first();
    }

    public function findCampoByName($campos, $name)
    {
        foreach ($campos as $item) {
            if ($item['campo']->name === $name) {
                return $item['valor'];
            }
        }
        return null;
    }

    public function index(Request $request)
{
    try {
        $practica = Practica::with('user.nivel', 'valoresCampos.campo')->findOrFail($request->practica_id);

        // Generar ID formateado
        $codigo_practica = 'PRA-' . str_pad($practica->id, 5, '0', STR_PAD_LEFT);

        // Verificar si la práctica está deshabilitada
        if ($practica->deshabilitado) {
            return redirect()->route('practicas.index')
                ->with('error', 'Esta práctica se encuentra deshabilitada. No se puede acceder al seguimiento.');
        }
        
        $estado = $practica->estado;
        
        // Si está en Pendiente o Rechazada, redirigir
        if (in_array($estado, ['Pendiente', 'Rechazada'])) {
            return redirect()->route('practicas.index')
                ->with('info', 'La práctica aún no ha sido aprobada para iniciar el seguimiento.');
        }
        
        // Obtener campos de la fase actual
        $tipo_estado = 'practicas_' . strtolower(str_replace(' ', '_', $estado));
        $type = TipoSolicitud::where('nombre', $tipo_estado)->first();
        $campos = Campo::where('tipo_solicitud_id', $type->id ?? 0)->get();
        
        // Valores actuales
        $valores = [];
        foreach ($practica->valoresCampos as $vc) {
            $valores[$vc->campo->name] = $vc->valor;
        }
        
        // Flags de submisión
        $submited_fase1 = $valores['submited_fase1'] ?? 'false';
        $submited_fase2 = $valores['submited_fase2'] ?? 'false';
        $submited_fase3 = $valores['submited_fase3'] ?? 'false';
        $submited_fase4 = $valores['submited_fase4'] ?? 'false';
        $submited_fase5 = $valores['submited_fase5'] ?? 'false';
        
        // Obtener director y evaluador actuales
        $director_actual = $valores['director_id'] ?? null;
        $evaluador_actual = $valores['evaluador_id'] ?? null;
        
        $docentes = User::role('docente')->get();
        
        return view('practicas.roadmap', compact(
            'practica', 'estado', 'campos', 'valores',
            'submited_fase1', 'submited_fase2', 'submited_fase3',
            'submited_fase4', 'submited_fase5',
            'director_actual', 'evaluador_actual', 'docentes',
            'codigo_practica' // ID formateado
        ));
    } catch (Exception $e) {
        return redirect()->route('practicas.index')->with('error', 'No se pudo cargar el seguimiento.');
    }
}
    // Aquí irán los métodos para cada fase (fase1, fase2, etc.)
    // Por ahora lo dejamos así para que la ruta no falle.
}