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
    /* Obtener el tipo de solicitud por nombre */

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

            // Generar el código de modalidad (sin guardar aún) - SOLO UNA VEZ
            $codigo_modalidad_generado = $this->generarCodigoModalidadPractica($practica->id);
            
            $codigo_practica = 'PRA-' . str_pad($practica->id, 5, '0', STR_PAD_LEFT);

            if ($practica->deshabilitado) {
                return redirect()->route('practicas.index')
                    ->with('error', 'Esta práctica se encuentra deshabilitada. No se puede acceder al seguimiento.');
            }


            $estado = $practica->estado;

            if ($estado === 'Finalizado') {

                $fase_actual = 7;

            } elseif (str_contains($estado, 'Fase')) {

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

                    // ========== INTEGRANTES PARA EL SELECT ICFES (MÁXIMO 2) ==========
            $integrante_1 = User::find($practica->user_id);  // El creador de la práctica
            $integrante_2 = null;

            // Buscar segundo integrante (campo id_integrante_2)
            if (isset($valores['id_integrante_2']) && $valores['id_integrante_2']) {
                $integrante_2 = User::find($valores['id_integrante_2']);
            }

            $lista_integrantes = [];
            if ($integrante_1) $lista_integrantes[] = $integrante_1;
            if ($integrante_2) $lista_integrantes[] = $integrante_2;

            // ========== VARIABLES ICFES ==========
            $submited_icfes_practicas = $valores['submited_icfes_practicas'] ?? 'false';
            $doc_icfes_practicas = $valores['doc_icfes_practicas'] ?? null;
            $beneficiarios_icfes_practicas = $valores['beneficiarios_icfes_practicas'] ?? null;

            // Procesar submited_icfes_practicas (si es JSON con IDs)
            if ($submited_icfes_practicas !== 'false' && $submited_icfes_practicas) {
                $solicitantes = json_decode($submited_icfes_practicas, true) ?? [];
                $submited_icfes_practicas = in_array(auth()->user()->id, $solicitantes) ? 'true' : 'false';
            } else {
                $submited_icfes_practicas = 'false';
            }

            // ========== VERIFICAR SI EL USUARIO ES BENEFICIARIO ==========
            $es_beneficiario_icfes = false;
            if ($beneficiarios_icfes_practicas && $beneficiarios_icfes_practicas !== 'false') {
                $beneficiarios = json_decode($beneficiarios_icfes_practicas, true) ?? [];
                $es_beneficiario_icfes = in_array(auth()->user()->id, $beneficiarios);
            }

            // Variables para Fase 1, 2, 3, 4, 5 y 6
            $submited_fase1 = $valores['submited_fase1'] ?? 'false';
            $submited_fase2 = $valores['submited_fase2'] ?? 'false';
            $submited_fase3 = $valores['submited_fase3'] ?? 'false';
            $submited_fase4 = $valores['submited_fase4'] ?? 'false';
            $submited_fase5 = $valores['submited_fase5'] ?? 'false';
            $submited_fase6 = $valores['submited_fase6'] ?? 'false';
            
            // Variables para Fase 3 - Director
            $estado_director_fase3 = $valores['estado_director_fase3'] ?? '';

            $estado_director_fase5 = $valores['estado_director_fase5'] ?? '';
            
            // Variables para Fase 4 y Fase 6 - Evaluador
            $estado_evaluador_fase4 = $valores['estado_evaluador_fase4'] ?? '';

            $estado_evaluador_fase6 = $valores['estado_evaluador_fase6'] ?? '';

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
                'estado_evaluador_fase4' => $estado_evaluador_fase4,
                'estado_director_fase5' => $estado_director_fase5,
                'submited_fase5' => $submited_fase5,
                'estado_evaluador_fase6' => $estado_evaluador_fase6,
                'submited_fase6' => $submited_fase6,
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
                'submited_fase6',
                'estado_director_fase5',
                'estado_director_fase3',
                'estado_evaluador_fase4',
                'estado_evaluador_fase6',
                'director_actual', 
                'evaluador_actual', 
                'docentes',
                'codigo_practica', 
                'fechas',

                'codigo_modalidad_generado',  // ← Pasar a la vista

                'lista_integrantes',
                'submited_icfes_practicas',
                'doc_icfes_practicas',
                'es_beneficiario_icfes'


            ));
            
        } catch (Exception $e) {
            \Log::error('Error en roadmap: ' . $e->getMessage());
            return redirect()->route('practicas.index')->with('error', 'No se pudo cargar el seguimiento.');
        }
    }

    // En app/Http/Controllers/RoadMapPracticaController.php
    // Función auxiliar para generar el código
    public static function generarCodigoModalidadPractica($practicaId)
    {
        $practica = Practica::with('user.nivel')->findOrFail($practicaId);
        $nivelId = $practica->user->nivel_id;

        $prefijo = match ($nivelId) {
            1 => '65',
            2 => '125',
            default => throw new \Exception("Nivel académico desconocido."),
        };

        $anioActual = Carbon::now()->year;

        $ultimoCodigo = PracticaValorCampo::whereHas('campo', function ($query) {
            $query->where('name', 'codigo_modalidad');
        })
            ->whereYear('created_at', $anioActual)
            ->orderBy('created_at', 'desc')
            ->pluck('valor')
            ->map(function ($valor) {
                if (preg_match('/\d{2,3}-\d{4}-(\d+)/', $valor, $matches)) {
                    return intval($matches[1]);
                }
                return 0;
            })
            ->filter()
            ->max();

        $nuevoConsecutivo = str_pad(($ultimoCodigo ?? 0) + 1, 3, '0', STR_PAD_LEFT);

        return "{$prefijo}-{$anioActual}-{$nuevoConsecutivo}";
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
    /*FASE 1 - Estudiante: Envío del formato F-DC-126*/
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

    /*FASE 1 - Ver detalles (para estudiantes y admin)*/
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

    /*FASE 1 - Comité/Admin: Responder solicitud*/
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


    /* FASE 2 - Estudiante: Envío de documentos de pago*/
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

    /*FASE 2 - Ver detalles de lo enviado (para estudiantes y comité)*/
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

    /* FASE 2 - Comité/Admin: Responder solicitud (con asignación de director/evaluador/codirector)*/

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

            // Dentro del bloque APROBADA, después de asignar docentes, agregar:

            // Guardar código de modalidad
            if ($request->filled('codigo_modalidad')) {
                $campoCodigo = Campo::where('tipo_solicitud_id', $tipo_fase2->id)
                    ->where('name', 'codigo_modalidad')
                    ->first();
                
                if ($campoCodigo) {
                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoCodigo->id],
                        ['valor' => $request->codigo_modalidad]
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

    /* FASE 3 - Ver detalles de lo enviado (para estudiante y director) */
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
    /* Fase 3 - Respuesta director */ 
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

            // ================= GUARDAR DOCUMENTOS - ACTUALIZAR LOS CAMPOS EXISTENTES =================
        if ($request->hasFile('fdc127')) {
            $fdc127Path = $request->file('fdc127')->store('practicas/fase3/documentos', 'public');
            
            // Buscar el campo existente doc_fdc127 (no el del director)
            $campoDoc = Campo::where('name', 'doc_fdc127')->first();
            if ($campoDoc) {
                // Eliminar archivo anterior si existe
                $valorExistente = PracticaValorCampo::where('practica_id', $practica->id)
                    ->where('campo_id', $campoDoc->id)->first();
                if ($valorExistente && $valorExistente->valor) {
                    Storage::disk('public')->delete($valorExistente->valor);
                }
                PracticaValorCampo::updateOrCreate(
                    ['practica_id' => $practica->id, 'campo_id' => $campoDoc->id],
                    ['valor' => $fdc127Path]
                );
            }
        }

        if ($request->hasFile('fdc195')) {
            $fdc195Path = $request->file('fdc195')->store('practicas/fase3/documentos', 'public');
            
            $campoDoc = Campo::where('name', 'doc_fdc195')->first();
            if ($campoDoc) {
                $valorExistente = PracticaValorCampo::where('practica_id', $practica->id)
                    ->where('campo_id', $campoDoc->id)->first();
                if ($valorExistente && $valorExistente->valor) {
                    Storage::disk('public')->delete($valorExistente->valor);
                }
                PracticaValorCampo::updateOrCreate(
                    ['practica_id' => $practica->id, 'campo_id' => $campoDoc->id],
                    ['valor' => $fdc195Path]
                );
            }
        }

       if ($request->hasFile('turnitin')) {
            $turnitinPath = $request->file('turnitin')
                ->store('practicas/fase3/documentos', 'public');
            $campoTurnitin = Campo::where(
                'name',
                'turnitin_director_fase3'
            )->first();

            if ($campoTurnitin) {
                $valorExistente = PracticaValorCampo::where(
                        'practica_id',
                        $practica->id
                    )
                    ->where(
                        'campo_id',
                        $campoTurnitin->id
                    )
                    ->first();

                if ($valorExistente && $valorExistente->valor) {
                    Storage::disk('public')
                        ->delete($valorExistente->valor);
                }

                PracticaValorCampo::updateOrCreate(
                    [
                        'practica_id' => $practica->id,
                        'campo_id' => $campoTurnitin->id
                    ],
                    [
                        'valor' => $turnitinPath
                    ]

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

     /* Fase 4 - Respuesta Evaluador */ 
    public function replyFase4(Request $request)
    {
        Log::info('REQUEST FASE 4', $request->all());
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

            // ================= GUARDAR DOCUMENTOS - ACTUALIZAR LOS CAMPOS EXISTENTES =================
        if ($request->hasFile('fdc127')) {
            $fdc127Path = $request->file('fdc127')->store('practicas/fase4', 'public');
            
            $campoDoc = Campo::where('name', 'doc_fdc127')->first();
            if ($campoDoc) {
                $valorExistente = PracticaValorCampo::where('practica_id', $practica->id)
                    ->where('campo_id', $campoDoc->id)->first();
                if ($valorExistente && $valorExistente->valor) {
                    Storage::disk('public')->delete($valorExistente->valor);
                }
                PracticaValorCampo::updateOrCreate(
                    ['practica_id' => $practica->id, 'campo_id' => $campoDoc->id],
                    ['valor' => $fdc127Path]
                );
            }
        }

        if ($request->hasFile('fdc195')) {
            $fdc195Path = $request->file('fdc195')->store('practicas/fase4', 'public');
            
            $campoDoc = Campo::where('name', 'doc_fdc195')->first();
            if ($campoDoc) {
                $valorExistente = PracticaValorCampo::where('practica_id', $practica->id)
                    ->where('campo_id', $campoDoc->id)->first();
                if ($valorExistente && $valorExistente->valor) {
                    Storage::disk('public')->delete($valorExistente->valor);
                }
                PracticaValorCampo::updateOrCreate(
                    ['practica_id' => $practica->id, 'campo_id' => $campoDoc->id],
                    ['valor' => $fdc195Path]
                );
            }
        }

            // Actualizar estado
            if ($request->estado === 'Aprobada') {
                $practica->estado = 'Fase 4';
                $tipoFase4 = TipoSolicitud::where('nombre', 'practicas_fase_4')->first();
                if ($tipoFase4) {
                    $practica->tipo_solicitud_id = $tipoFase4->id;
                }
                $practica->save();
            } else {
        // ================= RECHAZADA: Volver a Fase 3 y resetear TODO =================
        
        // 1. Cambiar estado a Fase 3
        $practica->estado = 'Fase 3';
        
        $tipoFase3 = TipoSolicitud::where('nombre', 'practicas_fase_3')->first();
        if ($tipoFase3) {
            $practica->tipo_solicitud_id = $tipoFase3->id;
        }
        $practica->save();
        
        // 2. Resetear submited_fase3 a 'false' para que el estudiante pueda reenviar
        $campoSubmitedFase3 = Campo::where('name', 'submited_fase3')->first();
        if ($campoSubmitedFase3) {
            PracticaValorCampo::updateOrCreate(
                ['practica_id' => $practica->id, 'campo_id' => $campoSubmitedFase3->id],
                ['valor' => 'false']
            );
        }
        
        // 3. Resetear estado_director_fase3 a '' para que el director pueda volver a responder
        $campoEstadoDirector = Campo::where('name', 'estado_director_fase3')->first();
        if ($campoEstadoDirector) {
            PracticaValorCampo::updateOrCreate(
                ['practica_id' => $practica->id, 'campo_id' => $campoEstadoDirector->id],
                ['valor' => '']
            );
        }
        
        // 4. Resetear respuesta_director_fase3 a ''
        $campoRespuestaDirector = Campo::where('name', 'respuesta_director_fase3')->first();
        if ($campoRespuestaDirector) {
            PracticaValorCampo::updateOrCreate(
                ['practica_id' => $practica->id, 'campo_id' => $campoRespuestaDirector->id],
                ['valor' => '']
            );
        }
        
        // 5. Resetear submited_fase4 a 'false'
        $campoSubmitedFase4 = Campo::where('name', 'submited_fase4')->first();
        if ($campoSubmitedFase4) {
            PracticaValorCampo::updateOrCreate(
                ['practica_id' => $practica->id, 'campo_id' => $campoSubmitedFase4->id],
                ['valor' => 'false']
            );
        }
        
        // 6. Resetear estado_evaluador_fase4 a ''
        $campoEstadoEvaluador = Campo::where('name', 'estado_evaluador_fase4')->first();
        if ($campoEstadoEvaluador) {
            PracticaValorCampo::updateOrCreate(
                ['practica_id' => $practica->id, 'campo_id' => $campoEstadoEvaluador->id],
                ['valor' => '']
            );
        }
        
        // 7. Resetear respuesta_evaluador_fase4 a ''
        $campoRespuestaEvaluador = Campo::where('name', 'respuesta_evaluador_fase4')->first();
        if ($campoRespuestaEvaluador) {
            PracticaValorCampo::updateOrCreate(
                ['practica_id' => $practica->id, 'campo_id' => $campoRespuestaEvaluador->id],
                ['valor' => '']
            );
        }
        
        Log::info('Fase 4 - Rechazada por evaluador, vuelve a Fase 3 con todo reseteado', [
            'practica_id' => $practica->id,
            'nuevo_estado' => $practica->estado
        ]);
        }

                $practica->refresh();

                return response()->json([
                    'success' => 'Respuesta enviada correctamente', 
                    'nuevo_estado' => $practica->estado
                ]);
                
            } catch (\Exception $e) {
                Log::error('Error en replyFase4: ' . $e->getMessage());
                return response()->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
            }
    }

     /* Fase 4 - Respuesta comite */ 
    public function replyFase4Comite(Request $request)
    {

        try {
            Log::info('=== replyFase4Comite INICIO ===', $request->all());
            
            $validator = Validator::make($request->all(), [
                'practica_id' => 'required|exists:practicas,id',
                'estado' => 'required|in:Aprobada,Rechazada',
                'titulo_propuesta' => 'required_if:estado,Aprobada|string|max:255',
                'nro_acta' => 'required_if:estado,Aprobada|string',
                'fecha_acta' => 'required_if:estado,Aprobada|date',
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

            // Guardar respuesta del comité
            $campoRespuesta = Campo::where('tipo_solicitud_id', $tipo_fase4->id)
                ->where('name', 'respuesta_comite_fase4')
                ->first();
            if ($campoRespuesta) {
                PracticaValorCampo::updateOrCreate(
                    ['practica_id' => $practica->id, 'campo_id' => $campoRespuesta->id],
                    ['valor' => $request->respuesta ?? '']
                );
            }

            // ================= GUARDAR DOCUMENTOS - ACTUALIZAR LOS CAMPOS EXISTENTES =================
        if ($request->hasFile('fdc127')) {
            $fdc127Path = $request->file('fdc127')->store('practicas/fase4/comite/documentos', 'public');
            
            $campoDoc = Campo::where('name', 'doc_fdc127')->first();
            if ($campoDoc) {
                $valorExistente = PracticaValorCampo::where('practica_id', $practica->id)
                    ->where('campo_id', $campoDoc->id)->first();
                if ($valorExistente && $valorExistente->valor) {
                    Storage::disk('public')->delete($valorExistente->valor);
                }
                PracticaValorCampo::updateOrCreate(
                    ['practica_id' => $practica->id, 'campo_id' => $campoDoc->id],
                    ['valor' => $fdc127Path]
                );
            }
        }

        if ($request->hasFile('fdc195')) {
            $fdc195Path = $request->file('fdc195')->store('practicas/fase4/comite/documentos', 'public');
            
            $campoDoc = Campo::where('name', 'doc_fdc195')->first();
            if ($campoDoc) {
                $valorExistente = PracticaValorCampo::where('practica_id', $practica->id)
                    ->where('campo_id', $campoDoc->id)->first();
                if ($valorExistente && $valorExistente->valor) {
                    Storage::disk('public')->delete($valorExistente->valor);
                }
                PracticaValorCampo::updateOrCreate(
                    ['practica_id' => $practica->id, 'campo_id' => $campoDoc->id],
                    ['valor' => $fdc195Path]
                );
            }
        }

        if ($request->estado === 'Aprobada') {
            // Guardar título de la propuesta
            $campoTitulo = Campo::where('tipo_solicitud_id', $tipo_fase4->id)
                ->where('name', 'titulo_propuesta_fase4')
                ->first();
            if ($campoTitulo) {
                PracticaValorCampo::updateOrCreate(
                    ['practica_id' => $practica->id, 'campo_id' => $campoTitulo->id],
                    ['valor' => $request->titulo_propuesta]
                );
            }
            
            // Crear acta
            ActaPractica::create([
                'practica_id' => $practica->id,
                'numero' => $request->nro_acta,
                'fecha' => $request->fecha_acta,
                'descripcion' => $request->respuesta ?? '',
            ]);
            
            // Pasar a Fase 5
            $practica->estado = 'Fase 5';
            $tipoFase5 = TipoSolicitud::where('nombre', 'practicas_fase_5')->first();
            if ($tipoFase5) {
                $practica->tipo_solicitud_id = $tipoFase5->id;
            }
            $practica->save();
            
            Log::info('Fase 4 - Comité APROBÓ, pasa a Fase 5');
            
        } else {
            // RECHAZADA: Volver a Fase 3
            $practica->estado = 'Fase 3';
            $tipoFase3 = TipoSolicitud::where('nombre', 'practicas_fase_3')->first();
            if ($tipoFase3) {
                $practica->tipo_solicitud_id = $tipoFase3->id;
            }
            $practica->save();
            
            // Resetear submited_fase3
            $campoSubmited = Campo::where('name', 'submited_fase3')->first();
            if ($campoSubmited) {
                PracticaValorCampo::updateOrCreate(
                    ['practica_id' => $practica->id, 'campo_id' => $campoSubmited->id],
                    ['valor' => 'false']
                );
            }
            
            Log::info('Fase 4 - Comité RECHAZÓ, vuelve a Fase 3');
        }

        return response()->json(['success' => 'Respuesta enviada correctamente']);
        
        } catch (\Exception $e) {
            Log::error('Error en replyFase4Comite: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /* FASE 5 - Estudiante: Envío de documentos finales*/
    public function storeFase5(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'practica_id' => 'required|exists:practicas,id',
            'doc_fdc196' => 'required|file|mimes:doc,docx,pdf|max:5120',
            'doc_fdc129' => 'required|file|mimes:doc,docx,pdf|max:5120',
            'doc_fdc128' => 'required|file|mimes:doc,docx,pdf|max:10240',

        ], [

            'doc_fdc196.required' => 'El acta de terminación es obligatoria.',
            'doc_fdc196.mimes' => 'El acta de terminación debe ser PDF o WORD.',
            'doc_fdc196.max' => 'El acta de terminación no puede superar los 5MB.',

            'doc_fdc129.required' => 'La rejilla de evaluación es obligatoria.',
            'doc_fdc129.mimes' => 'La rejilla de evaluación debe ser PDF o WORD.',
            'doc_fdc129.max' => 'La rejilla de evaluación no puede superar los 5MB.',

            'doc_fdc128.required' => 'El informe final es obligatorio.',
            'doc_fdc128.mimes' => 'El informe final debe ser PDF o WORD.',
            'doc_fdc128.max' => 'El informe final no puede superar los 10MB.',
        ]);

            if ($validator->fails()) {

                return response()->json([
                    'errors' => $validator->errors()
                ], 422);

            }

            $practica = Practica::findOrFail($request->practica_id);

            // Verificar fase
            if (!in_array($practica->estado, ['Fase 5'])) {

                return response()->json([
                    'error' => 'La práctica no está en la fase correspondiente'
                ], 422);

            }

            $tipo_fase5 = $this->getType('practicas_fase_5');

            $campos_fase5 = Campo::where(
                'tipo_solicitud_id',
                $tipo_fase5->id
            )->get();

            // ==================== 1. INFORME FINAL ====================

            if ($request->hasFile('doc_fdc128')) {

                $campoInforme = $campos_fase5
                    ->where('name', 'doc_fdc128')
                    ->first();

                $valorExistente = PracticaValorCampo::where(
                        'practica_id',
                        $practica->id
                    )
                    ->where('campo_id', $campoInforme->id)
                    ->first();

                if ($valorExistente && $valorExistente->valor) {

                    Storage::disk('public')
                        ->delete($valorExistente->valor);

                }

                $path = $request
                    ->file('doc_fdc128')
                    ->store('practicas/fase5', 'public');

                PracticaValorCampo::updateOrCreate(

                    [
                        'practica_id' => $practica->id,
                        'campo_id' => $campoInforme->id
                    ],

                    [
                        'valor' => $path
                    ]

                );
            }

            // ==================== 2. REJILLA EVALUACIÓN ====================

            if ($request->hasFile('doc_fdc129')) {

                $campoRejilla = $campos_fase5
                    ->where('name', 'doc_fdc129')
                    ->first();

                $valorExistente = PracticaValorCampo::where(
                        'practica_id',
                        $practica->id
                    )
                    ->where('campo_id', $campoRejilla->id)
                    ->first();

                if ($valorExistente && $valorExistente->valor) {

                    Storage::disk('public')
                        ->delete($valorExistente->valor);

                }

                $path = $request
                    ->file('doc_fdc129')
                    ->store('practicas/fase5', 'public');

                PracticaValorCampo::updateOrCreate(

                    [
                        'practica_id' => $practica->id,
                        'campo_id' => $campoRejilla->id
                    ],

                    [
                        'valor' => $path
                    ]

                );
            }



            // ==================== 3. ACTA TERMINACIÓN ====================

            if ($request->hasFile('doc_fdc196')) {

                $campoActa = $campos_fase5
                    ->where('name', 'doc_fdc196')
                    ->first();

                $valorExistente = PracticaValorCampo::where(
                        'practica_id',
                        $practica->id
                    )
                    ->where('campo_id', $campoActa->id)
                    ->first();

                if ($valorExistente && $valorExistente->valor) {

                    Storage::disk('public')
                        ->delete($valorExistente->valor);

                }

                $path = $request
                    ->file('doc_fdc196')
                    ->store('practicas/fase5', 'public');

                PracticaValorCampo::updateOrCreate(

                    [
                        'practica_id' => $practica->id,
                        'campo_id' => $campoActa->id
                    ],

                    [
                        'valor' => $path
                    ]

                );
            }

        
        
            // ==================== 4. MARCAR ENVÍO ====================

            $campoSubmited = $campos_fase5
                ->where('name', 'submited_fase5')
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

            Log::info('Fase 5 - Documentos finales enviados', [

                'practica_id' => $practica->id,
                'user_id' => auth()->id()

            ]);

            // Envío correo
            // $this->practicaMailService->sendFase5($practica);

            return response()->json([
                'success' => 'Documentos finales enviados correctamente'
            ]);
    }

    /* FASE 5 - Ver detalles de lo enviado (para estudiante, director y comité) */
    public function getFase5Details(Request $request)
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
            $informeFinal = $valores['doc_fdc128'] ?? null;
            $actaTerminacion = $valores['doc_fdc196'] ?? null;
            $rejillaEvaluacion = $valores['doc_fdc129'] ?? null;

            

            // ===============================
            // RESPUESTA DEL DIRECTOR
            // ===============================

            $estadoDirector = $valores['estado_director_fase5'] ?? null;

            $respuestaDirector = $valores['respuesta_director_fase5'] ?? null;

            $informeFinalDirector = $valores['informe_final_director_fase5'] ?? null;

            $turnitinDirector = $valores['turnitin_director_fase5'] ?? null;

            // ===============================
            // RESPUESTA DEL EVALUADOR
            // ===============================

            $estadoEvaluador = $valores['estado_evaluador_fase5'] ?? null;

            $respuestaEvaluador = $valores['respuesta_evaluador_fase5'] ?? null;

            $informeFinalEvaluador = $valores['informe_final_evaluador_fase5'] ?? null;

            // ===============================
            // RESPUESTA COMITÉ
            // ===============================

            $estadoComite = $valores['estado_comite_fase5'] ?? null;

            $respuestaComite = $valores['respuesta_comite_fase5'] ?? null;

            // ===============================
            // URLS PÚBLICAS
            // ===============================

            $actaTerminacionUrl = $actaTerminacion
                ? asset('storage/' . $actaTerminacion)
                : null;

            $rejillaEvaluacionUrl = $rejillaEvaluacion
                ? asset('storage/' . $rejillaEvaluacion)
                : null;

            $informeFinalUrl = $informeFinal
                ? asset('storage/' . $informeFinal)
                : null;

            $informeFinalDirectorUrl = $informeFinalDirector
                ? asset('storage/' . $informeFinalDirector)
                : null;

            $turnitinDirectorUrl = $turnitinDirector
                ? asset('storage/' . $turnitinDirector)
                : null;

            $informeFinalEvaluadorUrl = $informeFinalEvaluador
                ? asset('storage/' . $informeFinalEvaluador)
                : null;

            return response()->json([

                'success' => true,

                // ===============================
                // ESTUDIANTE
                // ===============================

                'acta_terminacion' => $actaTerminacion,
                'acta_terminacion_url' => $actaTerminacionUrl,

                'rejilla_evaluacion' => $rejillaEvaluacion,
                'rejilla_evaluacion_url' => $rejillaEvaluacionUrl,

                'informe_final' => $informeFinal,
                'informe_final_url' => $informeFinalUrl,

                // ===============================
                // DIRECTOR
                // ===============================

                'estado_director' => $estadoDirector,

                'respuesta_director' => $respuestaDirector,

                'informe_final_director' => $informeFinalDirector,

                'informe_final_director_url' => $informeFinalDirectorUrl,

                'turnitin_director' => $turnitinDirector,

                'turnitin_director_url' => $turnitinDirectorUrl,

                // ===============================
                // EVALUADOR
                // ===============================

                'estado_evaluador' => $estadoEvaluador,

                'respuesta_evaluador' => $respuestaEvaluador,

                'informe_final_evaluador' => $informeFinalEvaluador,

                'informe_final_evaluador_url' => $informeFinalEvaluadorUrl,

                // ===============================
                // COMITÉ
                // ===============================

                'estado_comite' => $estadoComite,

                'respuesta_comite' => $respuestaComite,

                // ===============================
                // FECHA
                // ===============================

                'fecha_envio' => $practica->updated_at->format('d/m/Y H:i')

            ]);

        } catch (Exception $e) {

            Log::error(
                'Error en getFase5Details: ' . $e->getMessage()
            );

            return response()->json([
                'error' => 'Error al cargar los detalles'
            ], 500);
        }
    }

     /* Fase 5 - Respuesta director */ 
    public function replyFase5(Request $request)
    {
        try {

            Log::info('=== replyFase5 INICIO ===', $request->all());

            $tipo_fase5 = TipoSolicitud::where(
                'nombre',
                'practicas_fase_5'
            )->first();

            if (!$tipo_fase5) {

                return response()->json([
                    'error' => 'Configuración de fase no encontrada'
                ], 500);

            }

            // ================= VALIDACIÓN =================

            $validator = Validator::make($request->all(), [

                'practica_id' => 'required|exists:practicas,id',

                'estado' => 'required|in:Aprobada,Rechazada',

                'fdc128' => 'nullable|file|mimes:doc,docx,pdf|max:10240',

                'fdc129' => 'nullable|file|mimes:doc,docx,pdf|max:5120',

                'fdc196' => 'nullable|file|mimes:doc,docx,pdf|max:5120',

                'turnitin' => 'nullable|file|mimes:pdf|max:5120',

                'respuesta' => 'nullable|string'

            ]);

            if ($validator->fails()) {

                return response()->json([
                    'errors' => $validator->errors()
                ], 422);

            }

            $practica = Practica::findOrFail($request->practica_id);

            // ================= VALIDAR FASE =================

            if ($practica->estado !== 'Fase 5') {

                return response()->json([
                    'error' => 'La práctica no está en la fase correspondiente'
                ], 422);

            }

            // ================= RESPUESTA DIRECTOR =================

            $campoRespuesta = Campo::where(
                'tipo_solicitud_id',
                $tipo_fase5->id
            )
            ->where('name', 'respuesta_director_fase5')
            ->first();

            if ($campoRespuesta) {

                PracticaValorCampo::updateOrCreate(

                    [
                        'practica_id' => $practica->id,
                        'campo_id' => $campoRespuesta->id
                    ],

                    [
                        'valor' => $request->respuesta ?? ''
                    ]

                );

            }

            // ================= ESTADO DIRECTOR =================

            $campoEstado = Campo::where(
                'tipo_solicitud_id',
                $tipo_fase5->id
            )
            ->where('name', 'estado_director_fase5')
            ->first();

            if ($campoEstado) {

                PracticaValorCampo::updateOrCreate(

                    [
                        'practica_id' => $practica->id,
                        'campo_id' => $campoEstado->id
                    ],

                    [
                        'valor' => $request->estado
                    ]

                );

            }

            // ======================================================
            // ACTUALIZAR DOCUMENTOS DEL ESTUDIANTE
            // ======================================================

            // ================= FDC128 =================

            if ($request->hasFile('fdc128')) {

                $path = $request
                    ->file('fdc128')
                    ->store('practicas/fase5', 'public');

                // IMPORTANTE:
                // actualiza el MISMO campo del estudiante

                $campoDoc = Campo::where('name', 'doc_fdc128')->first();

                if ($campoDoc) {

                    $valorExistente = PracticaValorCampo::where(
                            'practica_id',
                            $practica->id
                        )
                        ->where('campo_id', $campoDoc->id)
                        ->first();

                    if ($valorExistente && $valorExistente->valor) {

                        Storage::disk('public')
                            ->delete($valorExistente->valor);

                    }

                    PracticaValorCampo::updateOrCreate(

                        [
                            'practica_id' => $practica->id,
                            'campo_id' => $campoDoc->id
                        ],

                        [
                            'valor' => $path
                        ]

                    );

                }

            }

            // ================= FDC129 =================

            if ($request->hasFile('fdc129')) {

                $path = $request
                    ->file('fdc129')
                    ->store('practicas/fase5', 'public');

                $campoDoc = Campo::where('name', 'doc_fdc129')->first();

                if ($campoDoc) {

                    $valorExistente = PracticaValorCampo::where(
                            'practica_id',
                            $practica->id
                        )
                        ->where('campo_id', $campoDoc->id)
                        ->first();

                    if ($valorExistente && $valorExistente->valor) {

                        Storage::disk('public')
                            ->delete($valorExistente->valor);

                    }

                    PracticaValorCampo::updateOrCreate(

                        [
                            'practica_id' => $practica->id,
                            'campo_id' => $campoDoc->id
                        ],

                        [
                            'valor' => $path
                        ]

                    );

                }

            }

            // ================= FDC196 =================

            if ($request->hasFile('fdc196')) {

                $path = $request
                    ->file('fdc196')
                    ->store('practicas/fase5', 'public');

                $campoDoc = Campo::where('name', 'doc_fdc196')->first();

                if ($campoDoc) {

                    $valorExistente = PracticaValorCampo::where(
                            'practica_id',
                            $practica->id
                        )
                        ->where('campo_id', $campoDoc->id)
                        ->first();

                    if ($valorExistente && $valorExistente->valor) {

                        Storage::disk('public')
                            ->delete($valorExistente->valor);

                    }

                    PracticaValorCampo::updateOrCreate(

                        [
                            'practica_id' => $practica->id,
                            'campo_id' => $campoDoc->id
                        ],

                        [
                            'valor' => $path
                        ]

                    );

                }

            }

            // ================= TURNITIN DIRECTOR FASE 5 =================

            if ($request->hasFile('turnitin')) {

                $turnitinPath = $request
                    ->file('turnitin')
                    ->store('practicas/fase5', 'public');

                $campoTurnitin = Campo::where(
                    'name',
                    'turnitin_director_fase5'
                )->first();

                if ($campoTurnitin) {

                    $valorExistente = PracticaValorCampo::where(
                            'practica_id',
                            $practica->id
                        )
                        ->where(
                            'campo_id',
                            $campoTurnitin->id
                        )
                        ->first();

                    // Eliminar archivo anterior
                    if ($valorExistente && $valorExistente->valor) {

                        Storage::disk('public')
                            ->delete($valorExistente->valor);

                    }

                    PracticaValorCampo::updateOrCreate(

                        [
                            'practica_id' => $practica->id,
                            'campo_id' => $campoTurnitin->id
                        ],

                        [
                            'valor' => $turnitinPath
                        ]

                    );

                }

            }

            // ======================================================
            // APROBADA
            // ======================================================

            if ($request->estado === 'Aprobada') {

                // Cambiar estado
                $practica->estado = 'Fase 6';

                // Buscar tipo solicitud fase 6
                $tipoFase6 = TipoSolicitud::where(
                    'nombre',
                    'practicas_fase_6'
                )->first();

                // Asignar tipo solicitud
                if ($tipoFase6) {

                    $practica->tipo_solicitud_id = $tipoFase6->id;

                }

                // Guardar
                $practica->save();

                Log::info('Fase 5 aprobada - pasa a Fase 6', [

                    'practica_id' => $practica->id,
                    'nuevo_estado' => $practica->estado,
                    'nuevo_tipo_solicitud_id' => $practica->tipo_solicitud_id

                ]);

            }

            // ======================================================
            // RECHAZADA
            // ======================================================

            else {

                // Permitir reenviar al estudiante

                $campoSubmited = Campo::where('name', 'submited_fase5')
                    ->first();

                if ($campoSubmited) {

                    PracticaValorCampo::updateOrCreate(

                        [
                            'practica_id' => $practica->id,
                            'campo_id' => $campoSubmited->id
                        ],

                        [
                            'valor' => 'false'
                        ]

                    );

                }

                // Reset estado director

                $campoEstadoDirector = Campo::where(
                    'name',
                    'estado_director_fase5'
                )->first();

                if ($campoEstadoDirector) {

                    PracticaValorCampo::updateOrCreate(

                        [
                            'practica_id' => $practica->id,
                            'campo_id' => $campoEstadoDirector->id
                        ],

                        [
                            'valor' => ''
                        ]

                    );

                }

                // Reset respuesta director

                $campoRespuestaDirector = Campo::where(
                    'name',
                    'respuesta_director_fase5'
                )->first();

                if ($campoRespuestaDirector) {

                    PracticaValorCampo::updateOrCreate(

                        [
                            'practica_id' => $practica->id,
                            'campo_id' => $campoRespuestaDirector->id
                        ],

                        [
                            'valor' => ''
                        ]

                    );

                }

                $practica->touch();

                Log::info('Fase 5 rechazada - reinicio para nuevo envío', [

                    'practica_id' => $practica->id

                ]);

            }

            return response()->json([

                'success' => 'Respuesta enviada correctamente',

                'nuevo_estado' => $practica->estado

            ]);

        } catch (\Exception $e) {

            Log::error(
                'Error en replyFase5: ' . $e->getMessage()
            );

            Log::error(
                'Linea: ' . $e->getLine()
            );

            Log::error(
                'Archivo: ' . $e->getFile()
            );

            return response()->json([

                'error' => $e->getMessage(),
                'line' => $e->getLine()

            ], 500);

        }
    }

    /* Fase 6 - Responder evaluador  */
    public function replyFase6(Request $request)
    {
        Log::info('REQUEST FASE 6', $request->all());
        try {
            Log::info('=== replyFase6 INICIO ===', $request->all());
            
            $validator = Validator::make($request->all(), [
                'practica_id' => 'required|exists:practicas,id',
                'estado' => 'required|in:Aprobada,Rechazada',
                'fdc128' => 'nullable|file|mimes:doc,docx|max:5120',
                'fdc129' => 'nullable|file|mimes:doc,docx|max:5120',
                'respuesta' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

           $practica = Practica::findOrFail($request->practica_id);

            Log::info('PASO A');

            if ($practica->estado !== 'Fase 6') {
                return response()->json(['error' => 'La práctica no está en la fase correspondiente'], 422);
            }

            Log::info('PASO B');

            $tipo_fase6 = TipoSolicitud::where('nombre', 'practicas_fase_6')->first();

            Log::info('PASO C', [
                'tipo_fase6' => $tipo_fase6?->id
            ]);

            // Guardar respuesta del evaluador
            $campoRespuesta = Campo::where('tipo_solicitud_id', $tipo_fase6->id)
                ->where('name', 'respuesta_evaluador_fase6')
                ->first();
            if ($campoRespuesta) {
                PracticaValorCampo::updateOrCreate(
                    ['practica_id' => $practica->id, 'campo_id' => $campoRespuesta->id],
                    ['valor' => $request->respuesta ?? '']
                );
            }

            // Guardar estado del evaluador
            $campoEstado = Campo::where('tipo_solicitud_id', $tipo_fase6->id)
                ->where('name', 'estado_evaluador_fase6')
                ->first();
            if ($campoEstado) {
                PracticaValorCampo::updateOrCreate(
                    ['practica_id' => $practica->id, 'campo_id' => $campoEstado->id],
                    ['valor' => $request->estado]
                );
            }

            // ================= GUARDAR DOCUMENTOS - ACTUALIZAR LOS CAMPOS EXISTENTES =================
        if ($request->hasFile('fdc128')) {
            $fdc128Path = $request->file('fdc128')->store('practicas/fase6', 'public');
            
            $campoDoc = Campo::where('name', 'doc_fdc128')->first();
            if ($campoDoc) {
                $valorExistente = PracticaValorCampo::where('practica_id', $practica->id)
                    ->where('campo_id', $campoDoc->id)->first();
                if ($valorExistente && $valorExistente->valor) {
                    Storage::disk('public')->delete($valorExistente->valor);
                }
                PracticaValorCampo::updateOrCreate(
                    ['practica_id' => $practica->id, 'campo_id' => $campoDoc->id],
                    ['valor' => $fdc128Path]
                );
            }
        }

        if ($request->hasFile('fdc129')) {
            $fdc129Path = $request->file('fdc129')->store('practicas/fase6', 'public');
            
            $campoDoc = Campo::where('name', 'doc_fdc129')->first();
            if ($campoDoc) {
                $valorExistente = PracticaValorCampo::where('practica_id', $practica->id)
                    ->where('campo_id', $campoDoc->id)->first();
                if ($valorExistente && $valorExistente->valor) {
                    Storage::disk('public')->delete($valorExistente->valor);
                }
                PracticaValorCampo::updateOrCreate(
                    ['practica_id' => $practica->id, 'campo_id' => $campoDoc->id],
                    ['valor' => $fdc129Path]
                );
            }
        }

            // Actualizar estado
            if ($request->estado === 'Aprobada') {
                $practica->estado = 'Fase 6';
                $tipoFase6 = TipoSolicitud::where('nombre', 'practicas_fase_6')->first();
                if ($tipoFase6) {
                    $practica->tipo_solicitud_id = $tipoFase6->id;
                }
                $practica->save();
            } else {
        // ================= RECHAZADA: Volver a Fase 5 y resetear TODO =================
        
        // 1. Cambiar estado a Fase 5
        $practica->estado = 'Fase 5';
        
        $tipoFase3 = TipoSolicitud::where('nombre', 'practicas_fase_5')->first();
        if ($tipoFase3) {
            $practica->tipo_solicitud_id = $tipoFase3->id;
        }
        $practica->save();
        
        // 2. Resetear submited_fase5 a 'false' para que el estudiante pueda reenviar
        $campoSubmitedFase3 = Campo::where('name', 'submited_fase5')->first();
        if ($campoSubmitedFase3) {
            PracticaValorCampo::updateOrCreate(
                ['practica_id' => $practica->id, 'campo_id' => $campoSubmitedFase3->id],
                ['valor' => 'false']
            );
        }
        
        // 3. Resetear estado_director_fase5 a '' para que el director pueda volver a responder
        $campoEstadoDirector = Campo::where('name', 'estado_director_fase5')->first();
        if ($campoEstadoDirector) {
            PracticaValorCampo::updateOrCreate(
                ['practica_id' => $practica->id, 'campo_id' => $campoEstadoDirector->id],
                ['valor' => '']
            );
        }
        
        // 4. Resetear respuesta_director_fase5 a ''
        $campoRespuestaDirector = Campo::where('name', 'respuesta_director_fase5')->first();
        if ($campoRespuestaDirector) {
            PracticaValorCampo::updateOrCreate(
                ['practica_id' => $practica->id, 'campo_id' => $campoRespuestaDirector->id],
                ['valor' => '']
            );
        }
        
        // 5. Resetear submited_fase5 a 'false'
        $campoSubmitedFase5 = Campo::where('name', 'submited_fase5')->first();
        if ($campoSubmitedFase5) {
            PracticaValorCampo::updateOrCreate(
                ['practica_id' => $practica->id, 'campo_id' => $campoSubmitedFase5->id],
                ['valor' => 'false']
            );
        }
        
        // 6. Resetear estado_evaluador_fase5 a ''
        $campoEstadoEvaluador = Campo::where('name', 'estado_evaluador_fase5')->first();
        if ($campoEstadoEvaluador) {
            PracticaValorCampo::updateOrCreate(
                ['practica_id' => $practica->id, 'campo_id' => $campoEstadoEvaluador->id],
                ['valor' => '']
            );
        }
        
        // 7. Resetear respuesta_evaluador_fase5 a ''
        $campoRespuestaEvaluador = Campo::where('name', 'respuesta_evaluador_fase5')->first();
        if ($campoRespuestaEvaluador) {
            PracticaValorCampo::updateOrCreate(
                ['practica_id' => $practica->id, 'campo_id' => $campoRespuestaEvaluador->id],
                ['valor' => '']
            );
        }
        
        Log::info('Fase 6 - Rechazada por evaluador, vuelve a Fase 5 con todo reseteado', [
            'practica_id' => $practica->id,
            'nuevo_estado' => $practica->estado
        ]);
        }

                $practica->refresh();

                return response()->json([
                    'success' => 'Respuesta enviada correctamente', 
                    'nuevo_estado' => $practica->estado
                ]);
                
            } catch (\Exception $e) {
                Log::error('Error en replyFase4: ' . $e->getMessage());
                return response()->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
            }
    }
    /*Fase 6 - Responder Comite */ 
    public function replyFase6Comite(Request $request)
    {

            try {
                Log::info('=== replyFase6Comite INICIO ===', $request->all());
                
                $validator = Validator::make($request->all(), [
                    'practica_id' => 'required|exists:practicas,id',
                    'estado' => 'required|in:Aprobada,Rechazada',
                    'nro_acta' => 'required_if:estado,Aprobada|string',
                    'fecha_acta' => 'required_if:estado,Aprobada|date',
                    'fdc128' => 'nullable|file|mimes:doc,docx|max:5120',
                    'fdc129' => 'nullable|file|mimes:doc,docx|max:5120',
                    'respuesta' => 'nullable|string'
                ]);

                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                $practica = Practica::findOrFail($request->practica_id);

                if ($practica->estado !== 'Fase 6') {
                    return response()->json(['error' => 'La práctica no está en la fase correspondiente'], 422);
                }

            $tipo_fase6 = TipoSolicitud::where('nombre', 'practicas_fase_6')->first();

                // Guardar respuesta del comité
                $campoRespuesta = Campo::where('tipo_solicitud_id', $tipo_fase6->id)
                    ->where('name', 'respuesta_comite_fase6')
                    ->first();

                if ($campoRespuesta) {
                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoRespuesta->id],
                        ['valor' => $request->respuesta ?? '']
                    );
                }

                // Guardar estado del comité
                $campoEstado = Campo::where('tipo_solicitud_id', $tipo_fase6->id)
                    ->where('name', 'estado_comite_fase6')
                    ->first();

                if ($campoEstado) {
                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoEstado->id],
                        ['valor' => $request->estado]
                    );
                }




                // ================= GUARDAR DOCUMENTOS - ACTUALIZAR LOS CAMPOS EXISTENTES =================
            if ($request->hasFile('fdc128')) {
                $fdc128Path = $request->file('fdc128')->store('practicas/fase6/comite/documentos', 'public');
                
                $campoDoc = Campo::where('name', 'doc_fdc128')->first();
                if ($campoDoc) {
                    $valorExistente = PracticaValorCampo::where('practica_id', $practica->id)
                        ->where('campo_id', $campoDoc->id)->first();
                    if ($valorExistente && $valorExistente->valor) {
                        Storage::disk('public')->delete($valorExistente->valor);
                    }
                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoDoc->id],
                        ['valor' => $fdc128Path]
                    );
                }
            }

            if ($request->hasFile('fdc129')) {
                $fdc129Path = $request->file('fdc129')->store('practicas/fase6/comite/documentos', 'public');
                
                $campoDoc = Campo::where('name', 'doc_fdc129')->first();
                if ($campoDoc) {
                    $valorExistente = PracticaValorCampo::where('practica_id', $practica->id)
                        ->where('campo_id', $campoDoc->id)->first();
                    if ($valorExistente && $valorExistente->valor) {
                        Storage::disk('public')->delete($valorExistente->valor);
                    }
                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoDoc->id],
                        ['valor' => $fdc129Path]
                    );
                }
            }

            if ($request->estado === 'Aprobada') {
                
                
                // Crear acta
                ActaPractica::create([
                    'practica_id' => $practica->id,
                    'numero' => $request->nro_acta,
                    'fecha' => $request->fecha_acta,
                    'descripcion' => $request->respuesta ?? '',
                ]);
                
                // Finalizar práctica
                $practica->estado = 'Finalizado';

                $tipoFinalizada = TipoSolicitud::where(
                    'nombre',
                    'practicas_finalizada'
                )->first();

                if ($tipoFinalizada) {
                    $practica->tipo_solicitud_id = $tipoFinalizada->id;
                }

                $practica->save();
                
                Log::info('Fase 6 - Comité APROBÓ, práctica finalizada', [
                    'practica_id' => $practica->id
                ]);
                
            } else {
                // RECHAZADA: Volver a Fase 5
                $practica->estado = 'Fase 5';
                $tipoFase5 = TipoSolicitud::where('nombre', 'practicas_fase_5')->first();
                if ($tipoFase5) {
                    $practica->tipo_solicitud_id = $tipoFase5->id;
                }
                $practica->save();
                
                // Resetear submited_fase5
                $campoSubmited = Campo::where('name', 'submited_fase5')->first();
                if ($campoSubmited) {
                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoSubmited->id],
                        ['valor' => 'false']
                    );
                }
                
                Log::info('Fase 6 - Comité RECHAZÓ, vuelve a Fase 5');
            }

            return response()->json(['success' => 'Respuesta enviada correctamente']);
            
        } catch (\Exception $e) {
            Log::error('Error en replyFase4Comite: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /* Detalles Fase 6 */
    public function getFase6Details(Request $request)
    {
        try {

            $practica = Practica::with('valoresCampos.campo')
                ->findOrFail($request->practica_id);

            $valores = [];

            foreach ($practica->valoresCampos as $vc) {
                $valores[$vc->campo->name] = $vc->valor;
            }

            // ===============================
            // DOCUMENTOS
            // ===============================

            $informeFinal = $valores['doc_fdc128'] ?? null;

            $rejillaEvaluacion = $valores['doc_fdc129'] ?? null;

            $actaTerminacion = $valores['doc_fdc196'] ?? null;

            // OJO: cambia este nombre por el que tengas realmente en el seeder
            $turnitin = $valores['turnitin_director_fase5'] ?? null;

            // ===============================
            // URLS
            // ===============================

            $informeFinalUrl = $informeFinal
                ? asset('storage/' . $informeFinal)
                : null;

            $rejillaEvaluacionUrl = $rejillaEvaluacion
                ? asset('storage/' . $rejillaEvaluacion)
                : null;

            $actaTerminacionUrl = $actaTerminacion
                ? asset('storage/' . $actaTerminacion)
                : null;

            $turnitinUrl = $turnitin
                ? asset('storage/' . $turnitin)
                : null;

            return response()->json([

                'success' => true,

                'informe_final' => $informeFinal,
                'informe_final_url' => $informeFinalUrl,

                'rejilla_evaluacion' => $rejillaEvaluacion,
                'rejilla_evaluacion_url' => $rejillaEvaluacionUrl,

                'acta_terminacion' => $actaTerminacion,
                'acta_terminacion_url' => $actaTerminacionUrl,

                'turnitin' => $turnitin,
                'turnitin_url' => $turnitinUrl,

                'fecha_envio' => $practica->updated_at->format('d/m/Y H:i')

            ]);

        } catch (Exception $e) {

            Log::error(
                'Error en getFase6Details: ' . $e->getMessage()
            );

            return response()->json([
                'error' => 'Error al cargar los detalles'
            ], 500);
        }
    }

    /* Detalles Fase 7 */
    public function getFase7Details(Request $request)
    {
        try {

            $practica = Practica::with('valoresCampos.campo')
                ->findOrFail($request->practica_id);

            $valores = [];

            foreach ($practica->valoresCampos as $vc) {
                $valores[$vc->campo->name] = $vc->valor;
            }

           // ===============================
            // DOCUMENTOS
            // ===============================

            $rejillaFdc129 = $valores['doc_fdc129'] ?? null;

            $informeFinalFdc128 = $valores['doc_fdc128'] ?? null;

            $turnitinFdc128 = $valores['turnitin_director_fase5'] ?? null;

            $propuestaFdc127 = $valores['doc_fdc127'] ?? null;

            $turnitinFdc127 = $valores['turnitin_director_fase3'] ?? null;

            // ===============================
            // URLS
            // ===============================

           $rejillaFdc129Url = $rejillaFdc129
                ? asset('storage/' . $rejillaFdc129)
                : null;

            $informeFinalFdc128Url = $informeFinalFdc128
                ? asset('storage/' . $informeFinalFdc128)
                : null;

            $turnitinFdc128Url = $turnitinFdc128
                ? asset('storage/' . $turnitinFdc128)
                : null;

            $propuestaFdc127Url = $propuestaFdc127
                ? asset('storage/' . $propuestaFdc127)
                : null;

            $turnitinFdc127Url = $turnitinFdc127
                ? asset('storage/' . $turnitinFdc127)
                : null;

            return response()->json([

                'success' => true,

                'rejilla_fdc129_url' => $rejillaFdc129Url,

                'informe_final_fdc128_url' => $informeFinalFdc128Url,

                'turnitin_fdc128_url' => $turnitinFdc128Url,

                'propuesta_fdc127_url' => $propuestaFdc127Url,

                'turnitin_fdc127_url' => $turnitinFdc127Url,

            ]);

        } catch (Exception $e) {

            Log::error(
                'Error en getFase7Details: ' . $e->getMessage()
            );

            return response()->json([
                'error' => 'Error al cargar los detalles'
            ], 500);
        }
    }




        // ==================== BENEFICIO ICFES PARA PRÁCTICAS ====================

    /* FASE 5/6 - Estudiante: Envía solicitud de beneficio ICFES*/
    public function storeIcfesSolicitud(Request $request)
    {
        \Log::info('=== ICFES ESTUDIANTE - INICIO ===');
        \Log::info('Datos recibidos:', $request->all());
        \Log::info('Archivos:', $request->hasFile('doc_icfes_practicas') ? 'Sí hay archivo' : 'No hay archivo');
        
        $validator = Validator::make($request->all(), [
            'practica_id' => 'required|exists:practicas,id',
            'doc_icfes_practicas' => 'required|array|min:1',
            'doc_icfes_practicas.*' => 'file|mimes:pdf|max:4096',
            'submited_icfes_practicas' => 'required|string'
        ]);

        if ($validator->fails()) {
            \Log::error('Validación fallida:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

            $practica = Practica::findOrFail($request->practica_id);

            // Verificar que esté en Fase 5 o 6
            if (!in_array($practica->estado, ['Fase 5', 'Fase 6'])) {
                return response()->json(['error' => 'La práctica no está en la fase correspondiente'], 422);
            }

            // Obtener el campo submited_icfes_practicas
            $campoIcfes = Campo::where('name', 'submited_icfes_practicas')
                ->where('tipo_solicitud_id', $practica->tipo_solicitud_id)
                ->first();

            if (!$campoIcfes) {
                return response()->json(['error' => 'Configuración incorrecta del campo ICFES'], 500);
            }

            // Guardar el archivo PDF
            $path = null;
            if ($request->hasFile('doc_icfes_practicas')) {
                $file = $request->file('doc_icfes_practicas')[0];
                $fileName = 'icfes_practicas_' . $practica->id . '_' . auth()->user()->id . '_' . time() . '.pdf';
                $path = $file->storeAs('icfes_practicas', $fileName, 'public');
                
                // Guardar también en el campo doc_icfes_practicas
                $campoDoc = Campo::where('name', 'doc_icfes_practicas')
                    ->where('tipo_solicitud_id', $practica->tipo_solicitud_id)
                    ->first();
                
                if ($campoDoc) {
                    // Eliminar archivo anterior si existe
                    $existingDoc = PracticaValorCampo::where('practica_id', $practica->id)
                        ->where('campo_id', $campoDoc->id)
                        ->first();
                    if ($existingDoc && $existingDoc->valor) {
                        Storage::disk('public')->delete($existingDoc->valor);
                    }
                    
                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoDoc->id],
                        ['valor' => $path]
                    );
                }
            }

            // Actualizar el campo submited_icfes_practicas (almacena JSON con IDs)
            $existingValue = PracticaValorCampo::where('practica_id', $practica->id)
                ->where('campo_id', $campoIcfes->id)
                ->first();

            $integrantesSolicitantes = [];
            if ($existingValue && $existingValue->valor) {
                $integrantesSolicitantes = json_decode($existingValue->valor, true) ?? [];
            }
            
            if (!in_array(auth()->user()->id, $integrantesSolicitantes)) {
                $integrantesSolicitantes[] = auth()->user()->id;
            }

            PracticaValorCampo::updateOrCreate(
                [
                    'practica_id' => $practica->id,
                    'campo_id' => $campoIcfes->id
                ],
                ['valor' => json_encode($integrantesSolicitantes)]
            );

            // Enviar correo a los administradores (comentado por ahora)
            // $admins = User::role(['super_admin', 'admin'])->get();
            // foreach ($admins as $admin) {
            //     Mail::to($admin->email)->send(new IcfesSolicitudPracticaMail($practica, auth()->user()));
            // }

            Log::info('Beneficio ICFES - Solicitud enviada', [
                'practica_id' => $practica->id,
                'estudiante_id' => auth()->user()->id,
                'fase' => $practica->estado
            ]);

            \Log::info('=== ICFES ESTUDIANTE - FIN EXITOSO ===');
        return response()->json(['success' => 'Solicitud enviada correctamente']);
    }

    /*FASE 5/6 - Admin/Comité: Responder solicitud de beneficio ICFES*/
    public function responderIcfesSolicitud(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'practica_id' => 'required|exists:practicas,id',
            'estado_icfes_practicas' => 'required|in:Aprobado,Rechazado',
            'estudiante_id' => 'required|exists:users,id',
            'nro_acta_icfes_practicas' => 'required|integer',    // ← integer porque en BD es int
            'fecha_acta_icfes_practicas' => 'required|date',     // ← date porque en BD es date
            'respuesta_icfes_practicas' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $practica = Practica::findOrFail($request->practica_id);
        $estudiante = User::findOrFail($request->estudiante_id);

        // Verificar que esté en Fase 5 o 6
        if (!in_array($practica->estado, ['Fase 5', 'Fase 6'])) {
            return response()->json(['error' => 'La práctica no está en la fase correspondiente'], 422);
        }

        // ========== GUARDAR EN ACTAS_PRACTICAS ==========
        ActaPractica::create([
            'practica_id' => $practica->id,
            'numero' => $request->nro_acta_icfes_practicas,      // ← int
            'fecha' => $request->fecha_acta_icfes_practicas,     // ← date
            'descripcion' => $request->respuesta_icfes_practicas  // ← text
        ]);

        // Si es aprobado, marcar al estudiante como beneficiario
        if ($request->estado_icfes_practicas === 'Aprobado') {
            $campoBeneficiario = Campo::where('name', 'beneficiarios_icfes_practicas')
                ->where('tipo_solicitud_id', $practica->tipo_solicitud_id)
                ->first();

            if ($campoBeneficiario) {
                $existingBeneficiarios = PracticaValorCampo::where('practica_id', $practica->id)
                    ->where('campo_id', $campoBeneficiario->id)
                    ->first();
                
                $beneficiarios = [];
                if ($existingBeneficiarios && $existingBeneficiarios->valor) {
                    $beneficiarios = json_decode($existingBeneficiarios->valor, true) ?? [];
                }
                
                if (!in_array($estudiante->id, $beneficiarios)) {
                    $beneficiarios[] = $estudiante->id;
                }
                
                PracticaValorCampo::updateOrCreate(
                    [
                        'practica_id' => $practica->id,
                        'campo_id' => $campoBeneficiario->id
                    ],
                    ['valor' => json_encode($beneficiarios)]
                );
            }
        }

        Log::info('Beneficio ICFES - Solicitud respondida', [
            'practica_id' => $practica->id,
            'estudiante_id' => $estudiante->id,
            'estado' => $request->estado_icfes_practicas,
            'acta_numero' => $request->nro_acta_icfes_practicas,
            'acta_fecha' => $request->fecha_acta_icfes_practicas
        ]);

        return response()->json(['success' => 'Respuesta enviada correctamente']);
    }

}
