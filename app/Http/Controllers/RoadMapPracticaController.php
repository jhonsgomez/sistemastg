<?php

namespace App\Http\Controllers;

use App\Models\ActaPractica;
use App\Models\Campo;
use App\Models\Practica;
use App\Models\PracticaValorCampo;
use App\Models\TipoSolicitud;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class RoadMapPracticaController extends Controller
{
    public function getType($name)
    {
        return TipoSolicitud::where('nombre', $name)->first();
    }

    public function index(Request $request)
{
    try {
        $practica = Practica::with('user.nivel', 'valoresCampos.campo')->findOrFail($request->practica_id);

        $codigo_practica = 'PRA-' . str_pad($practica->id, 5, '0', STR_PAD_LEFT);

        if ($practica->deshabilitado) {
            return redirect()->route('practicas.index')
                ->with('error', 'Esta práctica se encuentra deshabilitada. No se puede acceder al seguimiento.');
        }

        $estado = $practica->estado;

        if (str_contains($estado, 'Fase')) {
            $estado_array = explode(' ', $estado);
            $fase_actual = (int) $estado_array[1];
        } else {
            $fase_actual = 0;
        }

        if (in_array($estado, ['Pendiente', 'Rechazada'])) {
            return redirect()->route('practicas.index')
                ->with('info', 'La práctica aún no ha sido aprobada para iniciar el seguimiento.');
        }

        // ✅ Cargar TODOS los valores de campos (incluyendo los de Fase 1)
        $valores = [];
        foreach ($practica->valoresCampos as $vc) {
            $valores[$vc->campo->name] = $vc->valor;
        }

        // ✅ Para depuración: registrar en log
        \Log::info('Roadmap - Valores cargados:', [
            'practica_id' => $practica->id,
            'estado' => $practica->estado,
            'tipo_solicitud_id' => $practica->tipo_solicitud_id,
            'submited_fase1' => $valores['submited_fase1'] ?? 'no existe',
            'doc_fdc126' => isset($valores['doc_fdc126']) ? 'presente' : 'ausente'
        ]);

        $submited_fase1 = $valores['submited_fase1'] ?? 'false';
        $submited_fase2 = $valores['submited_fase2'] ?? 'false';
        $submited_fase3 = $valores['submited_fase3'] ?? 'false';
        $submited_fase4 = $valores['submited_fase4'] ?? 'false';
        $submited_fase5 = $valores['submited_fase5'] ?? 'false';

        $director_actual = $valores['director_id'] ?? null;
        $evaluador_actual = $valores['evaluador_id'] ?? null;
        $docentes = User::role('docente')->get();

        return view('practicas.roadmap', compact(
            'practica', 'fase_actual', 'valores',
            'submited_fase1', 'submited_fase2', 'submited_fase3',
            'submited_fase4', 'submited_fase5',
            'director_actual', 'evaluador_actual', 'docentes',
            'codigo_practica'
        ));
    } catch (Exception $e) {
        \Log::error('Error en roadmap: ' . $e->getMessage());
        return redirect()->route('practicas.index')->with('error', 'No se pudo cargar el seguimiento.');
    }
}

    /**
     * FASE 1 - Estudiante: Envío del formato F-DC-126
     */
    public function storeFase1(Request $request)
{
    $validator = Validator::make($request->all(), [
        'practica_id' => 'required|exists:practicas,id',
        'doc_fdc126' => 'required|file|mimes:doc,docx|max:5120',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $practica = Practica::findOrFail($request->practica_id);

    // ✅ CORREGIDO: Permitir si está en Fase 1 O si está en Pendiente (recién aprobada)
    if (!in_array($practica->estado, ['Fase 1', 'Pendiente'])) {
        return response()->json(['error' => 'La práctica no está en la fase correspondiente'], 422);
    }

    $tipo_fase1 = $this->getType('practicas_fase_1');
    $campos_fase1 = Campo::where('tipo_solicitud_id', $tipo_fase1->id)->get();

    // Guardar archivo (con overwrite)
    if ($request->hasFile('doc_fdc126')) {
        $campoDoc = $campos_fase1->where('name', 'doc_fdc126')->first();
        $valorExistente = PracticaValorCampo::where('practica_id', $practica->id)
            ->where('campo_id', $campoDoc->id)
            ->first();
        
        if ($valorExistente && $valorExistente->valor) {
            Storage::disk('public')->delete($valorExistente->valor);
        }
        
        $path = $request->file('doc_fdc126')->store('practicas/fase1', 'public');
        PracticaValorCampo::updateOrCreate(
            ['practica_id' => $practica->id, 'campo_id' => $campoDoc->id],
            ['valor' => $path]
        );
    }

    // Guardar es_institucional
    $campoEsInstitucional = $campos_fase1->where('name', 'es_institucional')->first();
    $esInstitucional = $request->has('es_institucional') ? 'true' : 'false';
    PracticaValorCampo::updateOrCreate(
        ['practica_id' => $practica->id, 'campo_id' => $campoEsInstitucional->id],
        ['valor' => $esInstitucional]
    );

    // Guardar nombre de empresa
    $campoNombreEmpresa = $campos_fase1->where('name', 'nombre_empresa')->first();
    if ($esInstitucional === 'true') {
        $nombreEmpresa = 'Unidades Tecnológicas de Santander';
    } else {
        $nombreEmpresa = $request->nombre_empresa;
    }

    if ($campoNombreEmpresa && $nombreEmpresa) {
        PracticaValorCampo::updateOrCreate(
            ['practica_id' => $practica->id, 'campo_id' => $campoNombreEmpresa->id],
            ['valor' => $nombreEmpresa]
        );
    }

    // ✅ CRUCIAL: Marcar como enviada
    $campoSubmited = $campos_fase1->where('name', 'submited_fase1')->first();
    PracticaValorCampo::updateOrCreate(
        ['practica_id' => $practica->id, 'campo_id' => $campoSubmited->id],
        ['valor' => 'true']
    );

    // ✅ CRUCIAL: Cambiar el estado a 'Fase 1' (para que DataTable muestre "Fase 1 - Comité")
    $practica->estado = 'Fase 1';
    $practica->save();

    // ✅ También actualizar tipo_solicitud_id a Fase 1
    $tipoFase1 = TipoSolicitud::where('nombre', 'practicas_fase_1')->first();
    if ($tipoFase1 && $practica->tipo_solicitud_id != $tipoFase1->id) {
        $practica->tipo_solicitud_id = $tipoFase1->id;
        $practica->save();
    }

    return response()->json(['success' => 'Documentos enviados correctamente']);
}

    /**
     * FASE 1 - Ver detalles (para estudiantes y admin)
     */
    public function getFase1Details(Request $request)
    {
        try {
            $practica = Practica::with('valoresCampos.campo')->findOrFail($request->practica_id);

            $valores = [];
            foreach ($practica->valoresCampos as $vc) {
                $valores[$vc->campo->name] = $vc->valor;
            }

            $esInstitucional = ($valores['es_institucional'] ?? 'false') === 'true';
            $nombreEmpresa = $valores['nombre_empresa'] ?? 'No especificada';
            $docFdc126 = $valores['doc_fdc126'] ?? null;
            $respuestaComite = $valores['respuesta_comite_fase1'] ?? null;

            return response()->json([
                'success' => true,
                'es_institucional' => $esInstitucional,
                'nombre_empresa' => $nombreEmpresa,
                'doc_fdc126' => $docFdc126,
                'respuesta_comite' => $respuestaComite,
                'fecha_envio' => $practica->updated_at->format('d/m/Y H:i')
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al cargar los detalles'], 500);
        }
    }

    /**
     * FASE 1 - Comité/Admin: Responder solicitud
     */
    public function replyFase1(Request $request)
{
    $validator = Validator::make($request->all(), [
        'practica_id' => 'required|exists:practicas,id',
        'estado' => 'required|in:Aprobada,Rechazada',
        'nro_acta' => 'required|string',
        'fecha_acta' => 'required|date',
        'respuesta' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $practica = Practica::findOrFail($request->practica_id);

    // Solo permitir responder si está en Fase 1
    if ($practica->estado !== 'Fase 1') {
        return response()->json(['error' => 'La práctica no está en la fase correspondiente'], 422);
    }

    // Guardar la respuesta del comité
    $tipo_fase1 = $this->getType('practicas_fase_1');
    $campoRespuesta = Campo::where('tipo_solicitud_id', $tipo_fase1->id)
        ->where('name', 'respuesta_comite_fase1')
        ->first();

    if ($campoRespuesta) {
        PracticaValorCampo::updateOrCreate(
            ['practica_id' => $practica->id, 'campo_id' => $campoRespuesta->id],
            ['valor' => $request->respuesta]
        );
    }

    // Crear acta
    ActaPractica::create([
        'practica_id' => $practica->id,
        'numero' => $request->nro_acta,
        'fecha' => $request->fecha_acta,
        'descripcion' => $request->respuesta,
    ]);

    // ========== LÓGICA PRINCIPAL ==========
    if ($request->estado === 'Aprobada') {
        // ✅ APROBADA: Cambiar a Fase 2
        $practica->estado = 'Fase 2';
        
        // Cambiar el tipo_solicitud_id a Fase 2
        $tipoFase2 = TipoSolicitud::where('nombre', 'practicas_fase_2')->first();
        if ($tipoFase2) {
            $practica->tipo_solicitud_id = $tipoFase2->id;
        }
        
        $practica->save();
        
        // Enviar correo de aprobación al estudiante
        // Mail::to($practica->user->email)->send(new PracticaFase1AprobadaMail($practica, $request->respuesta));
        
    } else {
        // ❌ RECHAZADA: Volver a Fase 1 (para que el estudiante pueda reenviar)
        $practica->estado = 'Fase 1';
        // NO cambiar tipo_solicitud_id, sigue siendo Fase 1
        $practica->save();
        
        // IMPORTANTE: Resetear submited_fase1 a 'false' para que el estudiante pueda enviar de nuevo
        $tipo_fase1 = $this->getType('practicas_fase_1');
        $campoSubmited = Campo::where('tipo_solicitud_id', $tipo_fase1->id)
            ->where('name', 'submited_fase1')
            ->first();
        
        if ($campoSubmited) {
            PracticaValorCampo::updateOrCreate(
                ['practica_id' => $practica->id, 'campo_id' => $campoSubmited->id],
                ['valor' => 'false']  // ← Resetear para que pueda enviar de nuevo
            );
        }
        
        // Enviar correo de rechazo al estudiante
        // Mail::to($practica->user->email)->send(new PracticaFase1RechazadaMail($practica, $request->respuesta));
    }

    return response()->json([
        'success' => 'Respuesta enviada correctamente', 
        'nuevo_estado' => $practica->estado,
        'tipo_solicitud_id' => $practica->tipo_solicitud_id
    ]);
}
}