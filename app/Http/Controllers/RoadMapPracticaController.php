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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Fecha;
use App\Services\PracticaMailService;
use App\Services\PracticaService;


class RoadMapPracticaController extends Controller
{
    /**
     * Obtener el tipo de solicitud por nombre
     */

    protected $practicaMailService;

    protected $practicaService;

    public function __construct(
        PracticaMailService $practicaMailService,
        PracticaService $practicaService
    ) {
        $this->practicaMailService =
            $practicaMailService;

        $this->practicaService =
            $practicaService;
    }

    private function getType($nombre)
    {
        return TipoSolicitud::where('nombre', $nombre)->firstOrFail();
    }

    public function index(Request $request)
{
    // Obtener el periodo actual
    $periodoActual = session('periodo_academico', '2026-1');

    // Buscar las fechas para el periodo actual
    $fechasData = Fecha::where('periodo', $periodoActual)->first();

    $fechas = [];
    
    if ($fechasData) {
        $fechasArray = $fechasData->fechas;
        $fechas = [
            'fecha_inicio_banco' => $fechasArray['fecha_inicio_banco'] ?? 'No definida',
            'fecha_fin_banco' => $fechasArray['fecha_fin_banco'] ?? 'No definida',
            'fecha_inicio_proyectos' => $fechasArray['fecha_inicio_proyectos'] ?? 'No definida',
            'fecha_fin_proyectos' => $fechasArray['fecha_fin_proyectos'] ?? 'No definida',
            'fecha_aprobacion_propuesta' => $fechasArray['fecha_aprobacion_propuesta'] ?? 'No definida',
        ];
    } else {
        $fechas = [
            'fecha_inicio_banco' => '2026-01-30',
            'fecha_fin_banco' => '2026-09-30',
            'fecha_inicio_proyectos' => '2026-02-09',
            'fecha_fin_proyectos' => '2026-09-30',
            'fecha_aprobacion_propuesta' => '2026-09-30',
        ];
    }
    
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

        // Cargar TODOS los valores de campos
        $valores = [];
        foreach ($practica->valoresCampos as $vc) {
            $valores[$vc->campo->name] = $vc->valor;
        }

        // Variables para Fase 1, 2, 3, 4 y 5
        $submited_fase1 = $valores['submited_fase1'] ?? 'false';
        $submited_fase2 = $valores['submited_fase2'] ?? 'false';
        $submited_fase3 = $valores['submited_fase3'] ?? 'false';
        $submited_fase4 = $valores['submited_fase4'] ?? 'false';
        $submited_fase5 = $valores['submited_fase5'] ?? 'false';
        
        // Variables para Fase 3 - Director
        $estado_director_fase3 = $valores['estado_director_fase3'] ?? '';
        
        // Variables para Fase 4 - Evaluador
        $estado_evaluador_fase4 = $valores['estado_evaluador_fase4'] ?? '';

        $director_actual = $valores['director_id'] ?? null;
        $evaluador_actual = $valores['evaluador_id'] ?? null;
        $docentes = User::role('docente')->get();

        // Log para depuración
        \Log::info('Roadmap - Variables cargadas:', [
            'practica_id' => $practica->id,
            'estado' => $practica->estado,
            'fase_actual' => $fase_actual,
            'submited_fase3' => $submited_fase3,
            'estado_director_fase3' => $estado_director_fase3,
            'submited_fase4' => $submited_fase4,
            'estado_evaluador_fase4' => $estado_evaluador_fase4
        ]);

        return view('practicas.roadmap', compact(
            'practica', 
            'fase_actual', 
            'valores',
            'submited_fase1', 
            'submited_fase2', 
            'submited_fase3',
            'submited_fase4', 
            'submited_fase5',
            'estado_director_fase3',
            'estado_evaluador_fase4',
            'director_actual', 
            'evaluador_actual', 
            'docentes',
            'codigo_practica', 
            'fechas'
        ));
        
    } catch (Exception $e) {
        \Log::error('Error en roadmap: ' . $e->getMessage());
        return redirect()->route('practicas.index')->with('error', 'No se pudo cargar el seguimiento.');
    }
}

public function indexDirector()
{
    return view('practicas.index', [
        'rol_especifico' => 'director_practica'
    ]);
}

public function indexEvaluador()
{
    return view('practicas.index', [
        'rol_especifico' => 'evaluador_practica'
    ]);
}

// Si también necesitas para codirector
public function indexCodirector()
{
    return view('practicas.index', [
        'rol_especifico' => 'codirector_practica'
    ]);
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

    // CRUCIAL: Marcar como enviada
    $campoSubmited = $campos_fase1->where('name', 'submited_fase1')->first();
    PracticaValorCampo::updateOrCreate(
        ['practica_id' => $practica->id, 'campo_id' => $campoSubmited->id],
        ['valor' => 'true']
    );

    // CRUCIAL: Cambiar el estado a 'Fase 1' (para que DataTable muestre "Fase 1 - Comité")
    $practica->estado = 'Fase 1';
    $practica->save();

    // También actualizar tipo_solicitud_id a Fase 1
    $tipoFase1 = TipoSolicitud::where('nombre', 'practicas_fase_1')->first();
    if ($tipoFase1 && $practica->tipo_solicitud_id != $tipoFase1->id) {
        $practica->tipo_solicitud_id = $tipoFase1->id;
        $practica->save();
    }

    // ENVIAR CORREO  FASE 1 - DESCOMENTAR PARA
    // $this->practicaMailService->sendFase1($practica);

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
        'respuesta_fase1' => 'required|string',
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
            ['valor' => $request->respuesta_fase1]
        );
    }

    // Crear acta
    ActaPractica::create([
        'practica_id' => $practica->id,
        'numero' => $request->nro_acta,
        'fecha' => $request->fecha_acta,
        'descripcion' => $request->respuesta_fase1,
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
     //   $this->practicaMailService->sendRespuestaFase1($practica, $request);
    } else {
        // RECHAZADA: Volver a Fase 1 (para que el estudiante pueda reenviar)
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
    
    // $this->practicaMailService->sendRespuestaFase1($practica, $request);
     
     }

    return response()->json([
        'success' => 'Respuesta enviada correctamente', 
        'nuevo_estado' => $practica->estado,
        'tipo_solicitud_id' => $practica->tipo_solicitud_id
    ]);
}


    /**
 * FASE 2 - Estudiante: Envío de documentos de pago
 */
public function storeFase2(Request $request)
{
    $validator = Validator::make($request->all(), [
        'practica_id' => 'required|exists:practicas,id',
        'liquidacion_pago' => 'required|file|mimes:pdf|max:5120',
        'soporte_pago' => 'required|file|mimes:pdf|max:5120',
    ], [
        'liquidacion_pago.required' => 'La liquidación de pago es obligatoria.',
        'liquidacion_pago.mimes' => 'La liquidación debe ser un archivo PDF.',
        'liquidacion_pago.max' => 'La liquidación no puede superar los 5MB.',
        'soporte_pago.required' => 'El soporte de pago es obligatorio.',
        'soporte_pago.mimes' => 'El soporte de pago debe ser un archivo PDF.',
        'soporte_pago.max' => 'El soporte de pago no puede superar los 5MB.',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $practica = Practica::findOrFail($request->practica_id);

    // Verificar que esté en Fase 2
    if (!in_array($practica->estado, ['Fase 2'])) {
        return response()->json(['error' => 'La práctica no está en la fase correspondiente'], 422);
    }

    $tipo_fase2 = $this->getType('practicas_fase_2');
    $campos_fase2 = Campo::where('tipo_solicitud_id', $tipo_fase2->id)->get();

    // 1. Guardar liquidación de pago
    if ($request->hasFile('liquidacion_pago')) {
        $campoLiquidacion = $campos_fase2->where('name', 'liquidacion_pago')->first();
        
        $valorExistente = PracticaValorCampo::where('practica_id', $practica->id)
            ->where('campo_id', $campoLiquidacion->id)
            ->first();
        
        if ($valorExistente && $valorExistente->valor) {
            Storage::disk('public')->delete($valorExistente->valor);
        }
        
        $path = $request->file('liquidacion_pago')->store('practicas/fase2', 'public');
        PracticaValorCampo::updateOrCreate(
            ['practica_id' => $practica->id, 'campo_id' => $campoLiquidacion->id],
            ['valor' => $path]
        );
    }

    // 2. Guardar soporte de pago
    if ($request->hasFile('soporte_pago')) {
        $campoSoporte = $campos_fase2->where('name', 'soporte_pago')->first();
        
        $valorExistente = PracticaValorCampo::where('practica_id', $practica->id)
            ->where('campo_id', $campoSoporte->id)
            ->first();
        
        if ($valorExistente && $valorExistente->valor) {
            Storage::disk('public')->delete($valorExistente->valor);
        }
        
        $path = $request->file('soporte_pago')->store('practicas/fase2', 'public');
        PracticaValorCampo::updateOrCreate(
            ['practica_id' => $practica->id, 'campo_id' => $campoSoporte->id],
            ['valor' => $path]
        );
    }

    // 3. Marcar como enviada
    $campoSubmited = $campos_fase2->where('name', 'submited_fase2')->first();
    PracticaValorCampo::updateOrCreate(
        ['practica_id' => $practica->id, 'campo_id' => $campoSubmited->id],
        ['valor' => 'true']
    );

    // 4. Actualizar timestamp (no cambiar estado, solo tocar updated_at)
    $practica->touch();

    Log::info('Fase 2 - Documentos enviados', [
        'practica_id' => $practica->id,
        'user_id' => auth()->id()
    ]);
    //Envio de correo
    //$this->practicaMailService->sendFase2($practica);

    return response()->json(['success' => 'Documentos de pago enviados correctamente']);
}

        /**
     * FASE 2 - Ver detalles de lo enviado (para estudiantes y comité)
     */
    public function getFase2Details(Request $request)
    {
        try {
            $practica = Practica::with('valoresCampos.campo')->findOrFail($request->practica_id);

            $valores = [];
            foreach ($practica->valoresCampos as $vc) {
                $valores[$vc->campo->name] = $vc->valor;
            }

            $liquidacionPago = $valores['liquidacion_pago'] ?? null;
            $soportePago = $valores['soporte_pago'] ?? null;
            $respuestaComite = $valores['respuesta_comite_fase2'] ?? null;
            
            // Obtener URLs públicas de los archivos
            $liquidacionUrl = $liquidacionPago ? Storage::disk('public')->url($liquidacionPago) : null;
            $soporteUrl = $soportePago ? Storage::disk('public')->url($soportePago) : null;

            return response()->json([
                'success' => true,
                'liquidacion_pago' => $liquidacionPago,
                'liquidacion_url' => $liquidacionUrl,
                'soporte_pago' => $soportePago,
                'soporte_url' => $soporteUrl,
                'respuesta_comite' => $respuestaComite,
                'fecha_envio' => $practica->updated_at->format('d/m/Y H:i')
            ]);
        } catch (Exception $e) {
            Log::error('Error en getFase2Details: ' . $e->getMessage());
            return response()->json(['error' => 'Error al cargar los detalles'], 500);
        }
    }

        /**
     * FASE 2 - Comité/Admin: Responder solicitud (con asignación de director/evaluador/codirector)
     */

public function replyFase2(Request $request)
{
    Log::info('DIRECTOR DATA', [
    'director_id' => $request->director_id,
    'evaluador_id' => $request->evaluador_id,
]);

    Log::info('=== replyFase2 INICIO ===', $request->all());
    
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

    // Solo permitir responder si está en Fase 2
    if ($practica->estado !== 'Fase 2') {
        return response()->json(['error' => 'La práctica no está en la fase correspondiente'], 422);
    }

    // Guardar la respuesta del comité
    $tipo_fase2 = $this->getType('practicas_fase_2');
    $campoRespuesta = Campo::where('tipo_solicitud_id', $tipo_fase2->id)
        ->where('name', 'respuesta_comite_fase2')
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
        // APROBADA: Asignar docentes y cambiar a Fase 3
        
        // Asignar director
       
        $campoDirector = Campo::where('tipo_solicitud_id', $tipo_fase2->id)
            ->where('name', 'director_id')
            ->first();

        if ($campoDirector) {
            PracticaValorCampo::updateOrCreate(
                ['practica_id' => $practica->id, 'campo_id' => $campoDirector->id],
                ['valor' => $request->director_id]
            );

            // ASIGNAR ROL DIRECTOR
            if ($request->director_id) {
                $director = User::find($request->director_id);

                if ($director && !$director->hasRole('director_practica')) {
                    $director->assignRole('director_practica');
                }
            }
        }

        // Asignar evaluador
        $campoEvaluador = Campo::where('tipo_solicitud_id', $tipo_fase2->id)
            ->where('name', 'evaluador_id')
            ->first();

        if ($campoEvaluador) {

            PracticaValorCampo::updateOrCreate(
                [
                    'practica_id' => $practica->id,
                    'campo_id' => $campoEvaluador->id
                ],
                [
                    'valor' => $request->evaluador_id
                ]
            );

            // ASIGNAR ROL EVALUADOR
            if ($request->evaluador_id) {

                $evaluador = User::find($request->evaluador_id);

                if ($evaluador && !$evaluador->hasRole('evaluador_practica')) {
                    $evaluador->assignRole('evaluador_practica');
                }
            }
        }

        // Asignar codirector (opcional)
        if ($request->filled('codirector_id')) {
            $campoCodirector = Campo::where('tipo_solicitud_id', $tipo_fase2->id)
                ->where('name', 'codirector_id')
                ->first();
            if ($campoCodirector) {
                PracticaValorCampo::updateOrCreate(
                    ['practica_id' => $practica->id, 'campo_id' => $campoCodirector->id],
                    ['valor' => $request->codirector_id]
                );
            }
        }

        // Cambiar a Fase 3
        $practica->estado = 'Fase 3';
        
        $tipoFase3 = TipoSolicitud::where('nombre', 'practicas_fase_3')->first();
        if ($tipoFase3) {
            $practica->tipo_solicitud_id = $tipoFase3->id;
        }
        
        $practica->save();
        
        Log::info('Fase 2 - Aprobada, asignados', [
            'director_id' => $request->director_id,
            'evaluador_id' => $request->evaluador_id,
            'codirector_id' => $request->codirector_id ?? null
        ]);
        
    } else {
        // RECHAZADA: Resetear submited_fase2 a 'false' para que el estudiante pueda reenviar
        
        $campoSubmited = Campo::where('tipo_solicitud_id', $tipo_fase2->id)
            ->where('name', 'submited_fase2')
            ->first();
        
        if ($campoSubmited) {
            PracticaValorCampo::updateOrCreate(
                ['practica_id' => $practica->id, 'campo_id' => $campoSubmited->id],
                ['valor' => 'false']
            );
        }
        
        // NO cambiar estado, sigue en Fase 2
        $practica->touch();
        
        Log::info('Fase 2 - Rechazada');
    }

    /*$this->practicaMailService->sendRespuestaFase2($practica,
        [
            'estado' => $request->estado,
            'respuesta' => $request->respuesta,

            'director' => $request->director_id
                ? User::find($request->director_id)?->name
                : null,

            'evaluador' => $request->evaluador_id
                ? User::find($request->evaluador_id)?->name
                : null,

            'codirector' => $request->codirector_id
                ? User::find($request->codirector_id)?->name
                : null,
        ]
    );*/

    return response()->json([
        'success' => 'Respuesta enviada correctamente', 
        'nuevo_estado' => $practica->estado,
        'tipo_solicitud_id' => $practica->tipo_solicitud_id
    ]);
}


    /*FASE 3 - Estudiante: Envío de documentos*/
    public function storeFase3(Request $request)
    {
    

        $validator = Validator::make($request->all(), [

            'practica_id' => 'required|exists:practicas,id',
            'arl' => 'required|file|mimes:pdf|max:5120',
            'doc_fdc127' => 'required|file|mimes:doc,docx|max:5120',
            'doc_fdc195' => 'required|file|mimes:doc,docx|max:5120',
        ], [
            'arl.required' => 'El ARL es obligatorio.',
            'arl.mimes' => 'El ARL debe ser un archivo PDF.',
            'arl.max' => 'El ARL no puede superar los 5MB.',

            'doc_fdc127.required' => 'El formato F-DC-127 es obligatorio.',
            'doc_fdc127.mimes' => 'El formato F-DC-127 debe ser un archivo WORD.',
            'doc_fdc127.max' => 'El formato F-DC-127 no puede superar los 5MB.',

            'doc_fdc195.required' => 'El formato F-DC-195 es obligatorio.',
            'doc_fdc195.mimes' => 'El formato F-DC-195 debe ser un archivo WORD.',
            'doc_fdc195.max' => 'El formato F-DC-195 no puede superar los 5MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $practica = Practica::findOrFail($request->practica_id);

        // Verificar que esté en Fase 3
        if (!in_array($practica->estado, ['Fase 3'])) {
            return response()->json([
                'error' => 'La práctica no está en la fase correspondiente'
            ], 422);

        }

        $tipo_fase3 = $this->getType('practicas_fase_3');

        $campos_fase3 = Campo::where(
            'tipo_solicitud_id',
            $tipo_fase3->id
        )->get();

        // ==================== 1. ARL ====================
        if ($request->hasFile('arl')) {
            $campoArl = $campos_fase3
                ->where('name', 'arl')
                ->first();

            $valorExistente = PracticaValorCampo::where(
                    'practica_id',
                    $practica->id
                )
                ->where('campo_id', $campoArl->id)
                ->first();

            if ($valorExistente && $valorExistente->valor) {
                Storage::disk('public')
                    ->delete($valorExistente->valor);
            }

            $path = $request
                ->file('arl')
                ->store('practicas/fase3', 'public');

            PracticaValorCampo::updateOrCreate(
                [
                    'practica_id' => $practica->id,
                    'campo_id' => $campoArl->id
                ],
                [
                    'valor' => $path
                ]
            );
        }

        // ==================== 2. FDC-127 ====================

        if ($request->hasFile('doc_fdc127')) {
            $campo127 = $campos_fase3
                ->where('name', 'doc_fdc127')
                ->first();
            $valorExistente = PracticaValorCampo::where(
                    'practica_id',
                    $practica->id
                )
                ->where('campo_id', $campo127->id)
                ->first();

            if ($valorExistente && $valorExistente->valor) {
                Storage::disk('public')
                    ->delete($valorExistente->valor);

            }
            $path = $request
                ->file('doc_fdc127')
                ->store('practicas/fase3', 'public');

            PracticaValorCampo::updateOrCreate(

                [
                    'practica_id' => $practica->id,
                    'campo_id' => $campo127->id
                ],

                [
                    'valor' => $path
                ]
            );
        }

        // ==================== 3. FDC-195 ====================

        if ($request->hasFile('doc_fdc195')) {
            $campo195 = $campos_fase3
                ->where('name', 'doc_fdc195')
                ->first();

            $valorExistente = PracticaValorCampo::where(
                    'practica_id',
                    $practica->id
                )
                ->where('campo_id', $campo195->id)
                ->first();

            if ($valorExistente && $valorExistente->valor) {
                Storage::disk('public')
                    ->delete($valorExistente->valor);

            }

            $path = $request
                ->file('doc_fdc195')
                ->store('practicas/fase3', 'public');

            PracticaValorCampo::updateOrCreate(
                [
                    'practica_id' => $practica->id,
                    'campo_id' => $campo195->id
                ],
                [
                    'valor' => $path
                ]
            );
        }

        // ==================== 4. MARCAR ENVÍO ====================

        $campoSubmited = $campos_fase3
            ->where('name', 'submited_fase3')
            ->first();

        PracticaValorCampo::updateOrCreate(
            [
                'practica_id' => $practica->id,
                'campo_id' => $campoSubmited->id
            ],

            [
                'valor' => 'true'
            ]
        );

        // Actualizar timestamp
        $practica->touch();
        Log::info('Fase 3 - Documentos enviados', [
            'practica_id' => $practica->id,
            'user_id' => auth()->id()

        ]);

        // Envío correo - DESCOMENTAR CUANDO SE UTILICE
       //  $this->practicaMailService->sendFase3($practica);
        

        return response()->json([
            'success' => 'Documentos enviados correctamente'
        ]);
    }

    /* FASE 3 - Ver detalles de lo enviado
 (para estudiante y director) */
    public function getFase3Details(Request $request)
    {
        try {

            $practica = Practica::with('valoresCampos.campo')
                ->findOrFail($request->practica_id);

            $valores = [];

            foreach ($practica->valoresCampos as $vc) {
                $valores[$vc->campo->name] = $vc->valor;
            }

            // ===============================
            // ARCHIVOS DEL ESTUDIANTE
            // ===============================

            $arl = $valores['arl'] ?? null;
            $docFdc127 = $valores['doc_fdc127'] ?? null;
            $docFdc195 = $valores['doc_fdc195'] ?? null;

            // ===============================
            // RESPUESTA DEL DIRECTOR
            // ===============================

            $respuestaDirector = $valores['respuesta_director_fase3'] ?? null;

            // ===============================
            // URLS PÚBLICAS
            // ===============================

            $arlUrl = $arl ? asset('storage/' . $arl) : null;

            $docFdc127Url = $docFdc127
                ? asset('storage/' . $docFdc127)
                : null;

            $docFdc195Url = $docFdc195
                ? asset('storage/' . $docFdc195)
                : null;

            return response()->json([
                'success' => true,

                // Archivos
                'arl' => $arl,
                'arl_url' => $arlUrl,

                'doc_fdc127' => $docFdc127,
                'doc_fdc127_url' => $docFdc127Url,

                'doc_fdc195' => $docFdc195,
                'doc_fdc195_url' => $docFdc195Url,

                // Respuesta director
                'respuesta_director' => $respuestaDirector,

                // Fecha
                'fecha_envio' => $practica->updated_at->format('d/m/Y H:i')
            ]);

        } catch (Exception $e) {

            Log::error('Error en getFase3Details: ' . $e->getMessage());
            dd($practica->valoresCampos);
            return response()->json([
                'error' => 'Error al cargar los detalles'
            ], 500);
        }
    }








    public function replyFase3(Request $request)
{
    try {
        Log::info('=== replyFase3 INICIO ===', $request->all());
        
        $tipo_fase3 = TipoSolicitud::where('nombre', 'practicas_fase_3')->first();
        
        if (!$tipo_fase3) {
            return response()->json(['error' => 'Configuración de fase no encontrada'], 500);
        }
        
        $validator = Validator::make($request->all(), [
            'practica_id' => 'required|exists:practicas,id',
            'estado' => 'required|in:Aprobada,Rechazada',
            'fdc127' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'fdc195' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'turnitin' => 'nullable|file|mimes:pdf|max:5120',
            'respuesta' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $practica = Practica::findOrFail($request->practica_id);

        // Solo permitir responder si está en Fase 3
        if ($practica->estado !== 'Fase 3') {
            return response()->json(['error' => 'La práctica no está en la fase correspondiente'], 422);
        }

        // ================= GUARDAR RESPUESTA DEL DIRECTOR =================
        $campoRespuesta = Campo::where('tipo_solicitud_id', $tipo_fase3->id)
            ->where('name', 'respuesta_director_fase3')
            ->first();
        if ($campoRespuesta) {
            PracticaValorCampo::updateOrCreate(
                ['practica_id' => $practica->id, 'campo_id' => $campoRespuesta->id],
                ['valor' => $request->respuesta ?? '']
            );
        }

        // ================= GUARDAR ESTADO DEL DIRECTOR =================
        $campoEstado = Campo::where('tipo_solicitud_id', $tipo_fase3->id)
            ->where('name', 'estado_director_fase3')
            ->first();
        if ($campoEstado) {
            PracticaValorCampo::updateOrCreate(
                ['practica_id' => $practica->id, 'campo_id' => $campoEstado->id],
                ['valor' => $request->estado]
            );
        }

        // ================= GUARDAR DOCUMENTOS =================
        if ($request->hasFile('fdc127')) {
            $fdc127Path = $request->file('fdc127')->store('practicas/fase3/director/fdc127', 'public');
            $campoFdc127 = Campo::where('tipo_solicitud_id', $tipo_fase3->id)
                ->where('name', 'fdc127_director_fase3')->first();
            if ($campoFdc127) {
                // Eliminar archivo anterior si existe
                $valorExistente = PracticaValorCampo::where('practica_id', $practica->id)
                    ->where('campo_id', $campoFdc127->id)->first();
                if ($valorExistente && $valorExistente->valor) {
                    Storage::disk('public')->delete($valorExistente->valor);
                }
                PracticaValorCampo::updateOrCreate(
                    ['practica_id' => $practica->id, 'campo_id' => $campoFdc127->id],
                    ['valor' => $fdc127Path]
                );
            }
        }

        if ($request->hasFile('fdc195')) {
            $fdc195Path = $request->file('fdc195')->store('practicas/fase3/director/fdc195', 'public');
            $campoFdc195 = Campo::where('tipo_solicitud_id', $tipo_fase3->id)
                ->where('name', 'fdc195_director_fase3')->first();
            if ($campoFdc195) {
                $valorExistente = PracticaValorCampo::where('practica_id', $practica->id)
                    ->where('campo_id', $campoFdc195->id)->first();
                if ($valorExistente && $valorExistente->valor) {
                    Storage::disk('public')->delete($valorExistente->valor);
                }
                PracticaValorCampo::updateOrCreate(
                    ['practica_id' => $practica->id, 'campo_id' => $campoFdc195->id],
                    ['valor' => $fdc195Path]
                );
            }
        }

        if ($request->hasFile('turnitin')) {
            $turnitinPath = $request->file('turnitin')->store('practicas/fase3/director/turnitin', 'public');
            $campoTurnitin = Campo::where('tipo_solicitud_id', $tipo_fase3->id)
                ->where('name', 'turnitin_director_fase3')->first();
            if ($campoTurnitin) {
                $valorExistente = PracticaValorCampo::where('practica_id', $practica->id)
                    ->where('campo_id', $campoTurnitin->id)->first();
                if ($valorExistente && $valorExistente->valor) {
                    Storage::disk('public')->delete($valorExistente->valor);
                }
                PracticaValorCampo::updateOrCreate(
                    ['practica_id' => $practica->id, 'campo_id' => $campoTurnitin->id],
                    ['valor' => $turnitinPath]
                );
            }
        }

        // ================= ACTUALIZAR ESTADO DE LA PRÁCTICA =================
if ($request->estado === 'Aprobada') {
    // APROBADA: Cambiar a Fase 4
    $practica->estado = 'Fase 4';
    
    // Cambiar el tipo de solicitud a Fase 4
    $tipoFase4 = TipoSolicitud::where('nombre', 'practicas_fase_4')->first();
    if ($tipoFase4) {
        $practica->tipo_solicitud_id = $tipoFase4->id;
    }
    
    $practica->save();

    //CORREO DESCOMENTAR CUANDO SE UTILICE - APRUEBA EL DIRECTOR

  //  $this->practicaMailService->sendRespuestaFase3($practica, $request);
    
    // ========== GUARDAR submited_fase4 = 'true' PARA QUE EL EVALUADOR SEPA QUE EL DIRECTOR YA ENVIÓ ==========
    $campoSubmitedFase4 = Campo::where('name', 'submited_fase4')->first();
    if ($campoSubmitedFase4) {
        PracticaValorCampo::updateOrCreate(
            ['practica_id' => $practica->id, 'campo_id' => $campoSubmitedFase4->id],
            ['valor' => 'true']
        );
        Log::info('submited_fase4 guardado como true', [
            'practica_id' => $practica->id
        ]);
    } else {
        Log::error('No se encontró el campo submited_fase4');
    }
    
    Log::info('Fase 3 - Aprobada por director, pasa a Fase 4', [
        'practica_id' => $practica->id,
        'nuevo_estado' => $practica->estado,
        'nuevo_tipo_solicitud_id' => $practica->tipo_solicitud_id
    ]);
            
        } else {
    // ================= RECHAZADA =================
    
    // 1. Resetear submited_fase3 a 'false' para que el estudiante pueda reenviar
    $campoSubmited = Campo::where('tipo_solicitud_id', $tipo_fase3->id)
        ->where('name', 'submited_fase3')
        ->first();
    if ($campoSubmited) {
        PracticaValorCampo::updateOrCreate(
            ['practica_id' => $practica->id, 'campo_id' => $campoSubmited->id],
            ['valor' => 'false']
        );
    }
    
    // 2. RESETEAR estado_director_fase3 para que el director pueda volver a responder
    $campoEstado = Campo::where('tipo_solicitud_id', $tipo_fase3->id)
        ->where('name', 'estado_director_fase3')
        ->first();
    if ($campoEstado) {
        PracticaValorCampo::updateOrCreate(
            ['practica_id' => $practica->id, 'campo_id' => $campoEstado->id],
            ['valor' => '']  // Vacío para que no cuente como "ya respondió"
        );
    }
    
    // 3. RESETEAR también la respuesta del director (opcional, pero recomendado)
    $campoRespuesta = Campo::where('tipo_solicitud_id', $tipo_fase3->id)
        ->where('name', 'respuesta_director_fase3')
        ->first();
    if ($campoRespuesta) {
        PracticaValorCampo::updateOrCreate(
            ['practica_id' => $practica->id, 'campo_id' => $campoRespuesta->id],
            ['valor' => '']
        );
    }
    
    // NO cambiamos el estado de la práctica, se mantiene en "Fase 3"
    $practica->touch();
    
    Log::info('Fase 3 - Rechazada por director, todo reseteado para nuevo ciclo', [
        'practica_id' => $practica->id
    ]);
}

        return response()->json([
            'success' => 'Respuesta enviada correctamente', 
            'nuevo_estado' => $practica->estado,
            'tipo_solicitud_id' => $practica->tipo_solicitud_id
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error en replyFase3: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        return response()->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
    }
}
















    public function replyFase4(Request $request)
{
    try {
        Log::info('=== replyFase4 INICIO ===', $request->all());
        
        $validator = Validator::make($request->all(), [
            'practica_id' => 'required|exists:practicas,id',
            'estado' => 'required|in:Aprobada,Rechazada',
            'fdc127' => 'nullable|file|mimes:doc,docx|max:5120',
            'fdc195' => 'nullable|file|mimes:doc,docx|max:5120',
            'respuesta' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $practica = Practica::findOrFail($request->practica_id);

        if ($practica->estado !== 'Fase 4') {
            return response()->json(['error' => 'La práctica no está en la fase correspondiente'], 422);
        }

        $tipo_fase4 = TipoSolicitud::where('nombre', 'practicas_fase_4')->first();
        
        if (!$tipo_fase4) {
            return response()->json(['error' => 'Configuración de fase no encontrada'], 500);
        }

        // Guardar respuesta del evaluador
        $campoRespuesta = Campo::where('tipo_solicitud_id', $tipo_fase4->id)
            ->where('name', 'respuesta_evaluador_fase4')
            ->first();
        if ($campoRespuesta) {
            PracticaValorCampo::updateOrCreate(
                ['practica_id' => $practica->id, 'campo_id' => $campoRespuesta->id],
                ['valor' => $request->respuesta ?? '']
            );
        }

        // Guardar estado del evaluador
        $campoEstado = Campo::where('tipo_solicitud_id', $tipo_fase4->id)
            ->where('name', 'estado_evaluador_fase4')
            ->first();
        if ($campoEstado) {
            PracticaValorCampo::updateOrCreate(
                ['practica_id' => $practica->id, 'campo_id' => $campoEstado->id],
                ['valor' => $request->estado]
            );
        }

        // Guardar documentos
        if ($request->hasFile('fdc127')) {
            $fdc127Path = $request->file('fdc127')->store('practicas/fase4/evaluador/fdc127', 'public');
            $campoFdc127 = Campo::where('tipo_solicitud_id', $tipo_fase4->id)
                ->where('name', 'fdc127_evaluador_fase4')->first();
            if ($campoFdc127) {
                PracticaValorCampo::updateOrCreate(
                    ['practica_id' => $practica->id, 'campo_id' => $campoFdc127->id],
                    ['valor' => $fdc127Path]
                );
            }
        }

        if ($request->hasFile('fdc195')) {
            $fdc195Path = $request->file('fdc195')->store('practicas/fase4/evaluador/fdc195', 'public');
            $campoFdc195 = Campo::where('tipo_solicitud_id', $tipo_fase4->id)
                ->where('name', 'fdc195_evaluador_fase4')->first();
            if ($campoFdc195) {
                PracticaValorCampo::updateOrCreate(
                    ['practica_id' => $practica->id, 'campo_id' => $campoFdc195->id],
                    ['valor' => $fdc195Path]
                );
            }
        }

        // Actualizar estado
        if ($request->estado === 'Aprobada') {
            $practica->estado = 'Fase 5';
            $tipoFase5 = TipoSolicitud::where('nombre', 'practicas_fase_5')->first();
            if ($tipoFase5) {
                $practica->tipo_solicitud_id = $tipoFase5->id;
            }
            $practica->save();
        } else {
            $practica->estado = 'Fase 3';
            $tipoFase3 = TipoSolicitud::where('nombre', 'practicas_fase_3')->first();
            if ($tipoFase3) {
                $practica->tipo_solicitud_id = $tipoFase3->id;
            }
            $practica->save();
        }

        return response()->json([
            'success' => 'Respuesta enviada correctamente', 
            'nuevo_estado' => $practica->estado
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error en replyFase4: ' . $e->getMessage());
        return response()->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
    }
}
    



}
