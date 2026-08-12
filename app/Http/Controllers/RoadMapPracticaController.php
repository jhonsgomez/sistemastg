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
use Illuminate\Support\Facades\Log;
use App\Models\Fecha;
use App\Services\PracticaMailService;
use App\Services\PracticaService;
use App\Mail\PracticasMail;
use App\Models\ValorCampo;
use Illuminate\Support\Facades\Mail;


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

    public function getPeriodoActual()
    {
        $anio_actual = date('Y');
        $mes_actual = date('n');

        $periodo_actual = ($mes_actual <= 6) ? "$anio_actual-1" : "$anio_actual-2";

        return $periodo_actual;
    }

    public function index(Request $request)
    {
        $rol_especifico = $request->input('rol_especifico');

        if ($request->routeIs('director.practicas.roadmap')) {
            $rol_especifico = 'director_practica';
        } elseif ($request->routeIs('evaluador.practicas.roadmap')) {
            $rol_especifico = 'evaluador_practica';
        }

        $rutaRetorno = 'practicas.index';

        if ($rol_especifico === 'director_practica') {
            $rutaRetorno = 'director.practicas.index';
        }

        if ($rol_especifico === 'evaluador_practica') {
            $rutaRetorno = 'evaluador.practicas.index';
        }
        // Obtener el periodo actual
        $periodoActual = $this->getPeriodoActual();

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
                return redirect()->route($rutaRetorno)
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

            if (in_array($estado, ['Pendiente', 'Rechazada', 'Aplazada'])) {
                return redirect()->route($rutaRetorno)
                    ->with('info', 'La práctica aún no ha sido aprobada para iniciar el seguimiento.');
            }

            // Cargar TODOS los valores de campos
            $valores = [];
            foreach ($practica->valoresCampos as $vc) {
                $valores[$vc->campo->name] = $vc->valor;
            }

            $rol_especifico = null;

            if ($request->routeIs('director.practicas.roadmap')) {
                $rol_especifico = 'director_practica';
            } elseif ($request->routeIs('evaluador.practicas.roadmap')) {
                $rol_especifico = 'evaluador_practica';
            } else {
                $rol_especifico = $request->input('rol_especifico');
            }

            // ========== INTEGRANTES PARA EL SELECT ICFES (MÁXIMO 2) ==========
            $integrante_1 = User::find($practica->user_id);
            $integrante_2 = null;

            if (isset($valores['id_integrante_2']) && $valores['id_integrante_2']) {
                $integrante_2 = User::find($valores['id_integrante_2']);
            }

            $lista_integrantes = [];
            if ($integrante_1) $lista_integrantes[] = $integrante_1;
            if ($integrante_2) $lista_integrantes[] = $integrante_2;

            // ========== NUEVAS VARIABLES ICFES POR ESTUDIANTE ==========

            // 1. Obtener datos de submited (quién ha enviado solicitud)
            $submited_icfes_practicas = $valores['submited_icfes_practicas'] ?? '{}';
            $submitedData = json_decode($submited_icfes_practicas, true) ?: [];

            // Asegurar que sea un array (por si acaso hay valores booleanos antiguos)
            if (!is_array($submitedData)) {
                $submitedData = [];
            }

            // 2. Obtener beneficiarios (quienes fueron aprobados)
            $beneficiarios_icfes_practicas = $valores['beneficiarios_icfes_practicas'] ?? '[]';
            $beneficiariosData = json_decode($beneficiarios_icfes_practicas, true) ?: [];

            // Asegurar que sea un array
            if (!is_array($beneficiariosData)) {
                $beneficiariosData = [];
            }

            // 3. Verificar si el usuario actual ya envió solicitud
            $userId = (string) auth()->id();
            $yaEnvio = isset($submitedData[$userId]) && $submitedData[$userId] === true;

            // 4. Verificar si el usuario actual es beneficiario
            $beneficiariosDataInt = array_map('intval', $beneficiariosData);
            $esBeneficiario = in_array((int) auth()->id(), $beneficiariosDataInt);

            // 5. Obtener estudiantes que han enviado solicitud (para el select del admin)
            $solicitudesEnviadas = [];
            if (is_array($submitedData) && !empty($submitedData)) {
                $idsEnviaron = array_keys(array_filter($submitedData));
                if (!empty($idsEnviaron)) {
                    $solicitudesEnviadas = User::whereIn('id', $idsEnviaron)->get();
                }
            }

            // 6. Obtener documentos PDF por estudiante
            $doc_icfes_practicas = $valores['doc_icfes_practicas'] ?? '{}';
            $docData = json_decode($doc_icfes_practicas, true) ?: [];

            // Después de obtener $valores, agregar:
            $carta_prorroga = $valores['carta_prorroga'] ?? null;
            $liquidacion_prorroga = $valores['liquidacion_prorroga'] ?? null;
            $soporte_prorroga = $valores['soporte_prorroga'] ?? null;
            $carta_retiro = $valores['carta_retiro'] ?? null;

            // En el método index, después de obtener $valores:

            // ========== VERIFICAR SI EL ESTUDIANTE ESTÁ RETIRADO ==========
            $campoRetirados = Campo::where('name', 'retirados_practica')
                ->where('tipo_solicitud_id', $practica->tipo_solicitud_id)
                ->first();

            $estudiantesRetirados = [];
            if ($campoRetirados) {
                $valor = $practica->valoresCampos
                    ->where('campo_id', $campoRetirados->id)
                    ->first();
                if ($valor && $valor->valor) {
                    $estudiantesRetirados = json_decode($valor->valor, true) ?: [];
                }
            }

            $estaRetirado = in_array(auth()->id(), $estudiantesRetirados);

            // Redirigir si está retirado
            if (auth()->user()->hasRole('estudiante') && $estaRetirado) {
                return redirect()->route('practicas.index')
                    ->with('error', 'Has sido retirado de esta práctica. No puedes acceder al seguimiento.');
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
                'codigo_modalidad_generado',
                'rol_especifico',
                'lista_integrantes',
                // NUEVAS VARIABLES
                'yaEnvio',
                'esBeneficiario',
                'solicitudesEnviadas',
                'docData',
                'submitedData',

                'carta_prorroga',
                'liquidacion_prorroga',
                'soporte_prorroga',
                'carta_retiro',

                // En el compact, agregar:
                'estudiantesRetirados',
                'estaRetirado',


            ));
        } catch (Exception $e) {
            return redirect()->route($rutaRetorno)
                ->with('error', 'No se pudo cargar el seguimiento.');
        }
    }


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

        // Buscar valores anteriores con campo 'codigo_modalidad' en el año actual
        $ultimoCodigo = ValorCampo::whereHas('campo', function ($query) {
            $query->where('name', 'codigo_modalidad');
        })
            ->whereYear('created_at', $anioActual)
            ->orderBy('created_at', 'desc')
            ->pluck('valor')
            ->map(function ($valor) {
                // Extraer el número del formato XXX-YYYY-ZZZ
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
        $periodoActual = $this->getPeriodoActual();

        $fechasData = Fecha::where('periodo', $periodoActual)->first();

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

        $fechaActual = now()->format('Y-m-d');

        return view('practicas.index', [
            'rol_especifico' => 'director_practica',
            'dataRoute' => route('director.practicas.data'),
            'fechas' => $fechas,
            'fechaActual' => $fechaActual,
        ]);
    }

    public function indexEvaluador()
    {
        $periodoActual = $this->getPeriodoActual();

        $fechasData = Fecha::where('periodo', $periodoActual)->first();

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

        $fechaActual = now()->format('Y-m-d');

        return view('practicas.index', [
            'rol_especifico' => 'evaluador_practica',
            'dataRoute' => route('evaluador.practicas.data'),
            'fechas' => $fechas,
            'fechaActual' => $fechaActual,
        ]);
    }

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
            'doc_fdc126' => 'required|file|mimes:doc,docx,pdf|max:8192',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $practica = Practica::findOrFail($request->practica_id);

        //  Permitir si está en Fase 1 O si está en Pendiente (recién aprobada)
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

            $path = $request->file('doc_fdc126')->store(
                "practicas/{$practica->id}/fase1",
                'public'
            );

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

        // ENVIAR CORREO  FASE 1 
        $this->practicaMailService->sendFase1($practica);

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
            'estado' => 'required|in:Aprobada,Rechazada,Aplazada',
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
            //  APROBADA: Cambiar a Fase 2
            $practica->estado = 'Fase 2';

            // Cambiar el tipo_solicitud_id a Fase 2
            $tipoFase2 = TipoSolicitud::where('nombre', 'practicas_fase_2')->first();
            if ($tipoFase2) {
                $practica->tipo_solicitud_id = $tipoFase2->id;
            }

            $practica->save();

            // Enviar correo de aprobación al estudiante
            $this->practicaMailService->sendRespuestaFase1($practica, $request);
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
            $this->practicaMailService->sendRespuestaFase1($practica, $request);
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

            $path = $request->file('liquidacion_pago')->store(
                "practicas/{$practica->id}/fase2",
                'public'
            );
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

            $path = $request->file('soporte_pago')->store(
                "practicas/{$practica->id}/fase2",
                'public'
            );
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

        //Envio de correo
        $this->practicaMailService->sendFase2($practica);

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
            return response()->json(['error' => 'Error al cargar los detalles'], 500);
        }
    }

    /* FASE 2 - Comité/Admin: Responder solicitud (con asignación de director/evaluador/codirector)*/
    public function replyFase2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'practica_id' => 'required|exists:practicas,id',
            'estado' => 'required|in:Aprobada,Rechazada,Aplazada',
            'nro_acta' => 'required|string',
            'fecha_acta' => 'required|date',
            'respuesta' => 'required|string',

            'director_id' => 'required_if:estado,Aprobada|nullable|exists:users,id',
            'evaluador_id' => 'required_if:estado,Aprobada|nullable|exists:users,id',
            'codirector_id' => 'nullable|exists:users,id',
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
        }

        $this->practicaMailService->sendRespuestaFase2($practica, [
            'estado' => $request->estado,
            'respuesta' => $request->respuesta,
            'director_id' => $request->director_id,
            'evaluador_id' => $request->evaluador_id,
            'codirector_id' => $request->codirector_id,
        ]);

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
            'doc_fdc195' => 'required|file|mimes:doc,docx,pdf|max:5120',
        ], [
            'arl.required' => 'El ARL es obligatorio.',
            'arl.mimes' => 'El ARL debe ser un archivo PDF.',
            'arl.max' => 'El ARL no puede superar los 5MB.',

            'doc_fdc127.required' => 'El formato F-DC-127 es obligatorio.',
            'doc_fdc127.mimes' => 'El formato F-DC-127 debe ser un archivo WORD.',
            'doc_fdc127.max' => 'El formato F-DC-127 no puede superar los 5MB.',

            'doc_fdc195.required' => 'El formato F-DC-195 es obligatorio.',
            'doc_fdc195.mimes' => 'El formato F-DC-195 debe ser un archivo Word o PDF.',
            'doc_fdc195.max' => 'El formato F-DC-195 no puede superar los 5 MB.',
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
                ->store(
                    "practicas/{$practica->id}/fase3",
                    'public'
                );

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
            ->store(
                "practicas/{$practica->id}/fase3",
                'public'
            );

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
                ->store(
                    "practicas/{$practica->id}/fase3",
                    'public'
                );

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

        // Envío correo
        $this->practicaMailService->sendFase3($practica);

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
            return response()->json([
                'error' => 'Error al cargar los detalles'
            ], 500);
        }
    }

    /* Fase 3 - Respuesta director */
    public function replyFase3(Request $request)
    {
        try {
            $tipo_fase3 = TipoSolicitud::where('nombre', 'practicas_fase_3')->first();

            if (!$tipo_fase3) {
                return response()->json(['error' => 'Configuración de fase no encontrada'], 500);
            }

            $validator = Validator::make($request->all(), [
                'practica_id' => 'required|exists:practicas,id',
                'estado' => 'required|in:Aprobada,Rechazada,Aplazada',
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
                $fdc127Path = $request->file('fdc127')
                ->store(
                    "practicas/{$practica->id}/fase3/documentos",
                    'public'
                );

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
                $fdc195Path = $request->file('fdc195')
                ->store(
                    "practicas/{$practica->id}/fase3/documentos",
                    'public'
                );

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
                ->store(
                    "practicas/{$practica->id}/fase3/documentos",
                    'public'
                );s
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



                // ========== GUARDAR submited_fase4 = 'true' PARA QUE EL EVALUADOR SEPA QUE EL DIRECTOR YA ENVIÓ ==========
                $campoSubmitedFase4 = Campo::where('name', 'submited_fase4')->first();
                if ($campoSubmitedFase4) {
                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoSubmitedFase4->id],
                        ['valor' => 'true']
                    );
                }
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
            }

            // Envio de correo
            $this->practicaMailService->sendRespuestaFase3($practica, $request);

            return response()->json([
                'success' => 'Respuesta enviada correctamente',
                'nuevo_estado' => $practica->estado,
                'tipo_solicitud_id' => $practica->tipo_solicitud_id
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }

    /* Fase 4 - Respuesta Evaluador */
    public function replyFase4(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'practica_id' => 'required|exists:practicas,id',
                'estado' => 'required|in:Aprobada,Rechazada,Aplazada',
                'fdc127' => 'nullable|file|mimes:doc,docx|max:5120',
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

                $fdc127Path = $request->file('fdc127')->store(
                    "practicas/{$practica->id}/fase4",
                    'public'
                );

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
            }
            $this->practicaMailService->sendRespuestaFase4Evaluador($practica, $request);
            $practica->refresh();

            return response()->json([
                'success' => 'Respuesta enviada correctamente',
                'nuevo_estado' => $practica->estado
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }

    /* Fase 4 - Respuesta comite */
    public function replyFase4Comite(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'practica_id' => 'required|exists:practicas,id',
                'estado' => 'required|in:Aprobada,Rechazada,Aplazada',
                'titulo_propuesta' => 'nullable|string|max:255',
                'nro_acta' => 'required_if:estado,Aprobada|string',
                'fecha_acta' => 'required_if:estado,Aprobada|date',
                'fdc127' => 'nullable|file|mimes:doc,docx|max:5120',
                'respuesta' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Validación manual SOLO cuando APRUEBA
            if ($request->estado === 'Aprobada') {
                $errors = [];

                if (empty(trim($request->titulo_propuesta))) {
                    $errors['titulo_propuesta'] = ['El título de la propuesta es obligatorio cuando se aprueba'];
                }

                if (empty(trim($request->nro_acta))) {
                    $errors['nro_acta'] = ['El número de acta es obligatorio cuando se aprueba'];
                }

                if (empty($request->fecha_acta)) {
                    $errors['fecha_acta'] = ['La fecha del acta es obligatoria cuando se aprueba'];
                }

                if (!empty($errors)) {
                    return response()->json(['errors' => $errors], 422);
                }
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
                
                $fdc127Path = $request->file('fdc127')->store(
                    "practicas/{$practica->id}/fase4/comite/documentos",
                    'public'
                );

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

                // Guardar fechas DESPUÉS de pasar a Fase 5
                $this->guardarValorCampo($practica->id, 'fecha_inicio_practica', Carbon::now()->toDateTimeString());
                $this->guardarValorCampo($practica->id, 'fecha_limite_practica', Carbon::now()->addDays(180)->toDateTimeString());
                $this->guardarValorCampo($practica->id, 'solicitudes_prorroga', '0');
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

                // ========== NUEVO: BORRAR ESTADOS Y FECHAS ==========

                // 1. Borrar estado del director (fase 3)
                $campoDirector = Campo::where('name', 'estado_director_fase3')->first();
                if ($campoDirector) {
                    PracticaValorCampo::where('practica_id', $practica->id)
                        ->where('campo_id', $campoDirector->id)
                        ->delete();
                }

                // 2. Borrar estado del evaluador (fase 4)
                $campoEvaluador = Campo::where('name', 'estado_evaluador_fase4')->first();
                if ($campoEvaluador) {
                    PracticaValorCampo::where('practica_id', $practica->id)
                        ->where('campo_id', $campoEvaluador->id)
                        ->delete();
                }

                // 3. Borrar respuesta del comité (fase 4)
                $campoRespuestaComite = Campo::where('name', 'respuesta_comite_fase4')->first();
                if ($campoRespuestaComite) {
                    PracticaValorCampo::where('practica_id', $practica->id)
                        ->where('campo_id', $campoRespuestaComite->id)
                        ->delete();
                }

                // 4. Borrar título de la propuesta (fase 4)
                $campoTitulo = Campo::where('name', 'titulo_propuesta_fase4')->first();
                if ($campoTitulo) {
                    PracticaValorCampo::where('practica_id', $practica->id)
                        ->where('campo_id', $campoTitulo->id)
                        ->delete();
                }
            }

            $this->practicaMailService
                ->sendRespuestaFase4Comite($practica, $request);

            return response()->json(['success' => 'Respuesta enviada correctamente']);
        } catch (\Exception $e) {
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
                ->store(
                    "practicas/{$practica->id}/fase5",
                    'public'
                );

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
                ->store(
                    "practicas/{$practica->id}/fase5",
                    'public'
                );

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
                ->store(
                    "practicas/{$practica->id}/fase5",
                    'public'
                );

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

        // Envío correo
        $this->practicaMailService->sendFase5($practica);

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
            return response()->json([
                'error' => 'Error al cargar los detalles'
            ], 500);
        }
    }

    /* Fase 5 - Respuesta director */
    public function replyFase5(Request $request)
    {
        try {
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
                'estado' => 'required|in:Aprobada,Rechazada,Aplazada',
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
            )->where('name', 'respuesta_director_fase5')->first();

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
            )->where('name', 'estado_director_fase5')->first();

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
                ->store(
                    "practicas/{$practica->id}/fase5",
                    'public'
                );

                $campoDoc = Campo::where('name', 'doc_fdc128')->first();

                if ($campoDoc) {
                    $valorExistente = PracticaValorCampo::where(
                        'practica_id',
                        $practica->id
                    )->where('campo_id', $campoDoc->id)->first();

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
                ->store(
                    "practicas/{$practica->id}/fase5",
                    'public'
                );

                $campoDoc = Campo::where('name', 'doc_fdc129')->first();

                if ($campoDoc) {
                    $valorExistente = PracticaValorCampo::where(
                        'practica_id',
                        $practica->id
                    )->where('campo_id', $campoDoc->id)->first();

                    if ($valorExistente && $valorExistente->valor) {
                        Storage::disk('public')->delete($valorExistente->valor);
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
                    ->store(
                        "practicas/{$practica->id}/fase5",
                        'public'
                    );

                $campoDoc = Campo::where('name', 'doc_fdc196')->first();

                if ($campoDoc) {
                    $valorExistente = PracticaValorCampo::where(
                        'practica_id',
                        $practica->id
                    )->where('campo_id', $campoDoc->id)->first();

                    if ($valorExistente && $valorExistente->valor) {
                        Storage::disk('public')->delete($valorExistente->valor);
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
                ->store(
                    "practicas/{$practica->id}/fase5",
                    'public'
                );

                $campoTurnitin = Campo::where(
                    'name',
                    'turnitin_director_fase5'
                )->first();

                if ($campoTurnitin) {
                    $valorExistente = PracticaValorCampo::where(
                        'practica_id',
                        $practica->id
                    )->where(
                        'campo_id',
                        $campoTurnitin->id
                    )->first();

                    // Eliminar archivo anterior
                    if ($valorExistente && $valorExistente->valor) {
                        Storage::disk('public')->delete($valorExistente->valor);
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
            }

            // ======================================================
            // RECHAZADA
            // ======================================================
            else {
                // Permitir reenviar al estudiante
                $campoSubmited = Campo::where('name', 'submited_fase5')->first();

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
            }

            $this->practicaMailService->sendRespuestaFase5($practica, $request);

            return response()->json([
                'success' => 'Respuesta enviada correctamente',
                'nuevo_estado' => $practica->estado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /* Fase 6 - Responder evaluador  */
    public function replyFase6(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'practica_id' => 'required|exists:practicas,id',
                'estado' => 'required|in:Aprobada,Rechazada,Aplazada',
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
                $fdc128Path = $request->file('fdc128')
                ->store(
                    "practicas/{$practica->id}/fase6",
                    'public'
                );

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
                $fdc129Path = $request->file('fdc129')
                    ->store(
                        "practicas/{$practica->id}/fase6",
                        'public'
                    );

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
                $tipoFase5 = TipoSolicitud::where('nombre', 'practicas_fase_5')->first();
                if ($tipoFase5) {
                    $practica->tipo_solicitud_id = $tipoFase5->id;
                }
                $practica->save();

                // 2. Resetear submited_fase5 a 'false'
                $campoSubmitedFase5 = Campo::where('name', 'submited_fase5')->first();
                if ($campoSubmitedFase5) {
                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoSubmitedFase5->id],
                        ['valor' => 'false']
                    );
                }

                // 3. Resetear estado_director_fase5 a '' 
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

                // 5. Resetear estado_evaluador_fase6 a '' (el que puso el evaluador)
                $campoEstadoEvaluador = Campo::where('name', 'estado_evaluador_fase6')->first();
                if ($campoEstadoEvaluador) {
                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoEstadoEvaluador->id],
                        ['valor' => '']
                    );
                }

                // 6. Resetear respuesta_evaluador_fase6 a ''
                $campoRespuestaEvaluador = Campo::where('name', 'respuesta_evaluador_fase6')->first();
                if ($campoRespuestaEvaluador) {
                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoRespuestaEvaluador->id],
                        ['valor' => '']
                    );
                }

                // 7. Resetear estado_comite_fase6 a '' (para que el comité vea pendiente)
                $campoEstadoComite = Campo::where('name', 'estado_comite_fase6')->first();
                if ($campoEstadoComite) {
                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoEstadoComite->id],
                        ['valor' => '']
                    );
                }

                // 8. Resetear respuesta_comite_fase6 a ''
                $campoRespuestaComite = Campo::where('name', 'respuesta_comite_fase6')->first();
                if ($campoRespuestaComite) {
                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoRespuestaComite->id],
                        ['valor' => '']
                    );
                }

                // 9. Resetear estado_evaluador_fase5 a '' (para que el evaluador vea pendiente)
                $campoEstadoEvaluadorFase5 = Campo::where('name', 'estado_evaluador_fase5')->first();
                if ($campoEstadoEvaluadorFase5) {
                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoEstadoEvaluadorFase5->id],
                        ['valor' => '']
                    );
                }

                // 10. Resetear respuesta_evaluador_fase5 a ''
                $campoRespuestaEvaluadorFase5 = Campo::where('name', 'respuesta_evaluador_fase5')->first();
                if ($campoRespuestaEvaluadorFase5) {
                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoRespuestaEvaluadorFase5->id],
                        ['valor' => '']
                    );
                }

                // 11. Borrar documentos si existen
                $campoDoc128 = Campo::where('name', 'doc_fdc128')->first();
                if ($campoDoc128) {
                    $doc = PracticaValorCampo::where('practica_id', $practica->id)
                        ->where('campo_id', $campoDoc128->id)
                        ->first();
                    if ($doc && $doc->valor) {
                        Storage::disk('public')->delete($doc->valor);
                        $doc->delete();
                    }
                }

                $campoDoc129 = Campo::where('name', 'doc_fdc129')->first();
                if ($campoDoc129) {
                    $doc = PracticaValorCampo::where('practica_id', $practica->id)
                        ->where('campo_id', $campoDoc129->id)
                        ->first();
                    if ($doc && $doc->valor) {
                        Storage::disk('public')->delete($doc->valor);
                        $doc->delete();
                    }
                }
            }

            $this->practicaMailService->sendRespuestaFase6Evaluador($practica, $request);
            $practica->refresh();

            return response()->json([
                'success' => 'Respuesta enviada correctamente',
                'nuevo_estado' => $practica->estado
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }

    /*Fase 6 - Responder Comite */
    public function replyFase6Comite(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'practica_id' => 'required|exists:practicas,id',
                'estado' => 'required|in:Aprobada,Rechazada,Aplazada',
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
                $fdc128Path = $request->file('fdc128')
                    ->store(
                        "practicas/{$practica->id}/fase6/comite/documentos",
                        'public'
                    );
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
                $fdc129Path = $request->file('fdc129')
                    ->store(
                        "practicas/{$practica->id}/fase6/comite/documentos",
                        'public'
                    );

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
            } else {
                // ================= RECHAZADA: Volver a Fase 5 y resetear TODO =================

                // 1. Cambiar estado a Fase 5
                $practica->estado = 'Fase 5';
                $tipoFase5 = TipoSolicitud::where('nombre', 'practicas_fase_5')->first();
                if ($tipoFase5) {
                    $practica->tipo_solicitud_id = $tipoFase5->id;
                }
                $practica->save();

                // 2. Resetear submited_fase5 a 'false' para que el estudiante pueda reenviar
                $campoSubmited = Campo::where('name', 'submited_fase5')->first();
                if ($campoSubmited) {
                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoSubmited->id],
                        ['valor' => 'false']
                    );
                }

                // 3. Resetear estado_director_fase5 a '' 
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

                // 5. Resetear estado_evaluador_fase6 a '' (el que puso el evaluador)
                $campoEstadoEvaluador = Campo::where('name', 'estado_evaluador_fase6')->first();
                if ($campoEstadoEvaluador) {
                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoEstadoEvaluador->id],
                        ['valor' => '']
                    );
                }

                // 6. Resetear respuesta_evaluador_fase6 a ''
                $campoRespuestaEvaluador = Campo::where('name', 'respuesta_evaluador_fase6')->first();
                if ($campoRespuestaEvaluador) {
                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoRespuestaEvaluador->id],
                        ['valor' => '']
                    );
                }

                // 7. Resetear respuesta_comite_fase6 a '' (la respuesta del comité)
                $campoRespuestaComite = Campo::where('name', 'respuesta_comite_fase6')->first();
                if ($campoRespuestaComite) {
                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoRespuestaComite->id],
                        ['valor' => '']
                    );
                }

                // 8. Resetear estado_comite_fase6 a '' (el estado que puso el comité)
                $campoEstadoComite = Campo::where('name', 'estado_comite_fase6')->first();
                if ($campoEstadoComite) {
                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoEstadoComite->id],
                        ['valor' => '']
                    );
                }

                // 9. Borrar documentos si existen
                $campoDoc128 = Campo::where('name', 'doc_fdc128')->first();
                if ($campoDoc128) {
                    $doc = PracticaValorCampo::where('practica_id', $practica->id)
                        ->where('campo_id', $campoDoc128->id)
                        ->first();
                    if ($doc && $doc->valor) {
                        Storage::disk('public')->delete($doc->valor);
                        $doc->delete();
                    }
                }

                $campoDoc129 = Campo::where('name', 'doc_fdc129')->first();
                if ($campoDoc129) {
                    $doc = PracticaValorCampo::where('practica_id', $practica->id)
                        ->where('campo_id', $campoDoc129->id)
                        ->first();
                    if ($doc && $doc->valor) {
                        Storage::disk('public')->delete($doc->valor);
                        $doc->delete();
                    }
                }
            }

            $this->practicaMailService->sendRespuestaFase6Comite($practica, $request);

            return response()->json(['success' => 'Respuesta enviada correctamente']);
        } catch (\Exception $e) {
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
            return response()->json([
                'error' => 'Error al cargar los detalles'
            ], 500);
        }
    }

    /* FASE 5/6 - Estudiante: Envía solicitud de beneficio ICFES*/
    public function storeIcfesSolicitud(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'practica_id' => 'required|exists:practicas,id',
            'doc_icfes_practicas' => 'required|array|min:1',
            'doc_icfes_practicas.*' => 'file|mimes:pdf|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $practica = Practica::findOrFail($request->practica_id);
        $userId = (string) auth()->id(); // string para JSON

        if (!in_array($practica->estado, ['Fase 5', 'Fase 6'])) {
            return response()->json(['error' => 'La práctica no está en la fase correspondiente'], 422);
        }

        // ========== 1. OBTENER/ACTUALIZAR submited_icfes_practicas ==========
        $campoSubmited = Campo::where('name', 'submited_icfes_practicas')
            ->where('tipo_solicitud_id', $practica->tipo_solicitud_id)
            ->first();

        $submitedData = [];
        if ($campoSubmited) {
            $existing = PracticaValorCampo::where('practica_id', $practica->id)
                ->where('campo_id', $campoSubmited->id)
                ->first();

            if ($existing && $existing->valor) {
                $submitedData = json_decode($existing->valor, true) ?: [];
            }
        }

        // Verificar si ya envió
        if (isset($submitedData[$userId]) && $submitedData[$userId] === true) {
            return response()->json(['error' => 'Ya has enviado tu solicitud'], 422);
        }

        // ========== 2. GUARDAR PDF (JSON con userId como clave) ==========
        $campoDoc = Campo::where('name', 'doc_icfes_practicas')
            ->where('tipo_solicitud_id', $practica->tipo_solicitud_id)
            ->first();

        $docData = [];

        if ($campoDoc) {
            $existingDoc = PracticaValorCampo::where('practica_id', $practica->id)
                ->where('campo_id', $campoDoc->id)
                ->first();

            if ($existingDoc && $existingDoc->valor) {
                $docData = json_decode($existingDoc->valor, true) ?: [];
            }
        }

        if ($request->hasFile('doc_icfes_practicas')) {
            $file = $request->file('doc_icfes_practicas')[0];
            $fileName = 'icfes_practicas_' . $practica->id . '_' . $userId . '_' . time() . '.pdf';
            $path = $file->storeAs(
                "practicas/{$practica->id}/icfes",
                $fileName,
                'public'
            );
            $docData[$userId] = $path;

            PracticaValorCampo::updateOrCreate(
                ['practica_id' => $practica->id, 'campo_id' => $campoDoc->id],
                ['valor' => json_encode($docData)]
            );
        }

        // ========== 3. MARCAR ENVIADO ==========
        $submitedData[$userId] = true;

        PracticaValorCampo::updateOrCreate(
            ['practica_id' => $practica->id, 'campo_id' => $campoSubmited->id],
            ['valor' => json_encode($submitedData)]
        );

        $integrante2 = null;

        $campos = $practica->camposConValores();

        foreach ($campos as $campo) {
            if (($campo['campo'] ?? null) === 'id_integrante_2' && !empty($campo['valor'])) {
                $integrante2 = User::with('tipo_documento')->find($campo['valor']);
                break;
            }
        }

        $data = [
            'tipo_correo' => 'solicitud_icfes_practicas',

            'cuerpo_correo' => [
                'estado' => $practica->estado,
                'estudiante' => auth()->user(),
                'integrante_2' => $integrante2,
            ],

            'adjuntos' => [
                $docData[$userId] ?? null,
            ],

            'adjuntar_archivos' => true,
        ];

        $destinatarios = [
            auth()->user()->email,
        ];

        if (!empty($integrante2?->email)) {
            $destinatarios[] = $integrante2->email;
        }

        $destinatarios = array_unique(array_filter($destinatarios));

        Mail::to($destinatarios)
            ->queue(new PracticasMail($data));

        return response()->json(['success' => 'Solicitud enviada correctamente']);
    }

    /* FASE 5/6 - Admin/Comité: Responder solicitud de beneficio ICFES */
    public function responderIcfesSolicitud(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'practica_id' => 'required|exists:practicas,id',
            'estado_icfes_practicas' => 'required|in:Aprobado,Rechazado',
            'estudiante_id' => 'required|exists:users,id',
            'nro_acta_icfes_practicas' => 'required|integer',
            'fecha_acta_icfes_practicas' => 'required|date',
            'respuesta_icfes_practicas' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $practica = Practica::findOrFail($request->practica_id);
        $estudianteId = (string) $request->estudiante_id;

        if (!in_array($practica->estado, ['Fase 5', 'Fase 6'])) {
            return response()->json(['error' => 'La práctica no está en la fase correspondiente'], 422);
        }

        // Guardar acta
        ActaPractica::create([
            'practica_id' => $practica->id,
            'numero' => $request->nro_acta_icfes_practicas,
            'fecha' => $request->fecha_acta_icfes_practicas,
            'descripcion' => $request->respuesta_icfes_practicas
        ]);

        // ========== ACTUALIZAR BENEFICIARIOS (APROBADO) ==========
        $campoBeneficiario = Campo::where('name', 'beneficiarios_icfes_practicas')
            ->where('tipo_solicitud_id', $practica->tipo_solicitud_id)
            ->first();

        $beneficiarios = [];
        if ($campoBeneficiario) {
            $existing = PracticaValorCampo::where('practica_id', $practica->id)
                ->where('campo_id', $campoBeneficiario->id)
                ->first();

            if ($existing && $existing->valor) {
                $beneficiarios = json_decode($existing->valor, true) ?: [];
            }
        }

        if ($request->estado_icfes_practicas === 'Aprobado') {
            if (!in_array($estudianteId, $beneficiarios)) {
                $beneficiarios[] = (int) $estudianteId;
            }

            PracticaValorCampo::updateOrCreate(
                ['practica_id' => $practica->id, 'campo_id' => $campoBeneficiario->id],
                ['valor' => json_encode($beneficiarios)]
            );
        } else {
            // RECHAZADO: Borrar del submited para que pueda reintentar
            $campoSubmited = Campo::where('name', 'submited_icfes_practicas')
                ->where('tipo_solicitud_id', $practica->tipo_solicitud_id)
                ->first();

            if ($campoSubmited) {
                $existing = PracticaValorCampo::where('practica_id', $practica->id)
                    ->where('campo_id', $campoSubmited->id)
                    ->first();

                if ($existing && $existing->valor) {
                    $submitedData = json_decode($existing->valor, true) ?: [];
                    unset($submitedData[$estudianteId]); // Eliminar para que pueda reintentar

                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoSubmited->id],
                        ['valor' => json_encode($submitedData)]
                    );
                }
            }
        }
        $estudiante = User::find($request->estudiante_id);

        if ($estudiante && $estudiante->email) {
            $data = [
                'tipo_correo' => 'respuesta_icfes_practicas',

                'cuerpo_correo' => [
                    'estudiante' => $estudiante,
                    'estado' => $request->estado_icfes_practicas,
                    'respuesta' => $request->respuesta_icfes_practicas,
                    'nro_acta' => $request->nro_acta_icfes_practicas,
                    'fecha_acta' => $request->fecha_acta_icfes_practicas,
                ],
                'adjuntar_archivos' => false,
            ];

            Mail::to($estudiante->email)
                ->queue(new PracticasMail($data));
        }

        return response()->json(['success' => 'Respuesta enviada correctamente']);
    }

    /**
     * Estudiante envía solicitud de configuración
     */
    public function configEstudiante(Request $request)
    {
        $rules = [
            'practica_id' => 'required|exists:practicas,id',
            'tipo_solicitud' => 'required|in:retiro,cambio_director,cambio_evaluador,prorroga',
            'comentarios_config' => 'nullable|string',
        ];

        if ($request->tipo_solicitud === 'prorroga') {
            $rules['carta_prorroga'] = 'required|array|max:1';
            $rules['carta_prorroga.*'] = 'file|mimes:pdf|max:4096';
            $rules['liquidacion_prorroga'] = 'required|array|max:1';
            $rules['liquidacion_prorroga.*'] = 'file|mimes:pdf|max:4096';
            $rules['soporte_prorroga'] = 'required|array|max:1';
            $rules['soporte_prorroga.*'] = 'file|mimes:pdf|max:4096';
        }

        if ($request->tipo_solicitud === 'retiro') {
            $rules['carta_retiro'] = 'required|array|max:1';
            $rules['carta_retiro.*'] = 'file|mimes:pdf|max:4096';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $practica = Practica::findOrFail($request->practica_id);
        $userId = auth()->id();
        $fase_actual = $this->getFaseActual($practica->estado);

        if ($request->tipo_solicitud === 'prorroga' && !in_array($fase_actual, [5, 6])) {
            return response()->json(['error' => 'La prórroga solo se puede solicitar en Fase 5 o 6'], 422);
        }

        if (in_array($request->tipo_solicitud, ['cambio_director', 'cambio_evaluador']) && $fase_actual < 3) {
            return response()->json(['error' => 'Los cambios solo se pueden solicitar desde Fase 3'], 422);
        }

        if ($request->tipo_solicitud === 'retiro' && $fase_actual < 1) {
            return response()->json(['error' => 'El retiro solo se puede solicitar desde Fase 1'], 422);
        }

        $adjuntosCorreo = [];

        if ($request->tipo_solicitud === 'prorroga') {
            if ($request->hasFile('carta_prorroga')) {
                $file = $request->file('carta_prorroga')[0];

            $path = $file->storeAs(
                "practicas/{$practica->id}/solicitudes/prorroga",
                'carta_prorroga_' . $practica->id . '_' . $userId . '_' . time() . '.pdf',
                'public'
            );

                $this->guardarValorCampo($practica->id, 'carta_prorroga', $path);
                $adjuntosCorreo[] = $path;
            }

            if ($request->hasFile('liquidacion_prorroga')) {
                $file = $request->file('liquidacion_prorroga')[0];

            $path = $file->storeAs(
                "practicas/{$practica->id}/solicitudes/prorroga",
                'liquidacion_prorroga_' . $practica->id . '_' . $userId . '_' . time() . '.pdf',
                'public'
            );

                $this->guardarValorCampo($practica->id, 'liquidacion_prorroga', $path);
                $adjuntosCorreo[] = $path;
            }

            if ($request->hasFile('soporte_prorroga')) {
                $file = $request->file('soporte_prorroga')[0];

            $path = $file->storeAs(
                "practicas/{$practica->id}/solicitudes/prorroga",
                'soporte_prorroga_' . $practica->id . '_' . $userId . '_' . time() . '.pdf',
                'public'
            );

                $this->guardarValorCampo($practica->id, 'soporte_prorroga', $path);
                $adjuntosCorreo[] = $path;
            }
        }

        if ($request->tipo_solicitud === 'retiro') {
            if ($request->hasFile('carta_retiro')) {
                $file = $request->file('carta_retiro')[0];

                $path = $file->storeAs(
                    "practicas/{$practica->id}/solicitudes/retiro",
                    'carta_retiro_' . $practica->id . '_' . $userId . '_' . time() . '.pdf',
                    'public'
                );

                $this->guardarValorCampo($practica->id, 'carta_retiro', $path);
                $adjuntosCorreo[] = $path;
            }
        }

        if ($request->filled('comentarios_config')) {
            $this->guardarValorCampo($practica->id, 'comentarios_solicitud', $request->comentarios_config);
        }

        $this->guardarValorCampo($practica->id, 'tipo_solicitud_pendiente', $request->tipo_solicitud);
        $this->guardarValorCampo($practica->id, 'estado_solicitud', 'pendiente');

        $practica->load(['user.tipo_documento', 'valoresCampos.campo']);

        $campos = $practica->camposConValores();

        $integrante2Id = collect($campos)
            ->firstWhere('campo', 'id_integrante_2')['valor'] ?? null;

        $integrante2 = null;

        if (!empty($integrante2Id)) {
            $integrante2 = User::with('tipo_documento')->find($integrante2Id);
        }

        $data = [
            'tipo_correo' => 'solicitud_ajuste_practica',

            'adjuntos' => $adjuntosCorreo,

            'adjuntar_archivos' => !empty($adjuntosCorreo),

            'cuerpo_correo' => [
                'estado' => $practica->estado,
                'tipo_solicitud' => $request->tipo_solicitud,
                'comentarios' => $request->comentarios_config,
                'estudiante' => auth()->user(),
                'integrante_2' => $integrante2,
                'campos' => $campos,
            ],
        ];

        $destinatarios = [
            auth()->user()->email,
        ];

        if (!empty($integrante2?->email)) {
            $destinatarios[] = $integrante2->email;
        }

        $destinatarios = array_unique(array_filter($destinatarios));

        Mail::to($destinatarios)
            ->queue(new PracticasMail($data));

        return response()->json(['success' => 'Solicitud enviada correctamente']);
    }

    /* Admin responde solicitud*/
    public function configAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'practica_id' => 'required|exists:practicas,id',
            'director_id' => 'nullable|exists:users,id',
            'evaluador_id' => 'nullable|exists:users,id',
            'retirar_estudiante' => 'nullable|exists:users,id',
            'nro_acta_ajustes' => 'required|integer',
            'fecha_acta_ajustes' => 'required|date',
            'comentarios_config_admin' => 'nullable|string',
            'aprobar_prorroga' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $practica = Practica::findOrFail($request->practica_id);
        $cambiosRealizados = false;
        $descripcionActa = '';

        $tipoSolicitudCorreo = null;
        $nuevaFechaLimiteCorreo = null;
        $nuevoDirectorCorreo = null;
        $nuevoEvaluadorCorreo = null;
        $estudianteRetiradoCorreo = null;

        // Verificar prórroga
        $aprobarProrroga = $request->boolean('aprobar_prorroga');

        if ($aprobarProrroga) {
            $registroFechaLimite = PracticaValorCampo::with('campo')
                ->where('practica_id', $practica->id)
                ->whereHas('campo', function ($q) {
                    $q->where('name', 'fecha_limite_practica');
                })
                ->first();

            if (!$registroFechaLimite || empty($registroFechaLimite->valor)) {
                return response()->json([
                    'error' => 'No se encontró la fecha límite de la práctica para aprobar la prórroga'
                ], 422);
            }

            $nuevaFechaLimite = Carbon::parse($registroFechaLimite->valor)->addDays(90);
            $registroFechaLimite->valor = $nuevaFechaLimite->toDateTimeString();
            $registroFechaLimite->save();

            $registroProrrogas = PracticaValorCampo::where('practica_id', $practica->id)
                ->whereHas('campo', function ($q) {
                    $q->where('name', 'solicitudes_prorroga');
                })
                ->first();

            if ($registroProrrogas) {
                $prorrogas = (int) $registroProrrogas->valor;
                $registroProrrogas->valor = (string) ($prorrogas + 1);
                $registroProrrogas->save();
            } else {
                $campoProrrogas = Campo::where('name', 'solicitudes_prorroga')
                    ->first();

                if ($campoProrrogas) {
                    PracticaValorCampo::create([
                        'practica_id' => $practica->id,
                        'campo_id' => $campoProrrogas->id,
                        'valor' => '1',
                    ]);
                }
            }

            $cambiosRealizados = true;
            $tipoSolicitudCorreo = 'prorroga';
            $nuevaFechaLimiteCorreo = $nuevaFechaLimite->format('d/m/Y');
        }

        // Guardar acta
        ActaPractica::create([
            'practica_id' => $practica->id,
            'numero' => $request->nro_acta_ajustes,
            'fecha' => $request->fecha_acta_ajustes,
            'descripcion' => $request->comentarios_config_admin ?? ''
        ]);

        // Verificar cambio de director
if ($request->has('director_id') && !empty($request->director_id)) {
    // Buscar el registro existente SIN importar el tipo_solicitud_id
    $registroDirector = PracticaValorCampo::where('practica_id', $practica->id)
        ->whereHas('campo', function ($q) {
            $q->where('name', 'director_id');
        })
        ->first();

    if ($registroDirector) {
        // Actualizar el registro existente
        if ($registroDirector->valor != $request->director_id) {
            $registroDirector->valor = $request->director_id;
            $registroDirector->save();
            $cambiosRealizados = true;
            $tipoSolicitudCorreo = 'cambio_director';
            $nuevoDirectorCorreo = User::with('tipo_documento')->find($request->director_id);
            
            // ===== ASIGNAR EL ROL DE DIRECTOR_PRACTICA AL NUEVO DIRECTOR =====
            if ($nuevoDirectorCorreo && !$nuevoDirectorCorreo->hasRole('director_practica')) {
                $nuevoDirectorCorreo->assignRole('director_practica');
            }
        }
    }
}

        // Verificar cambio de evaluador
if ($request->has('evaluador_id') && !empty($request->evaluador_id)) {
    // Buscar el registro existente SIN importar el tipo_solicitud_id
    $registroEvaluador = PracticaValorCampo::where('practica_id', $practica->id)
        ->whereHas('campo', function ($q) {
            $q->where('name', 'evaluador_id');
        })
        ->first();

    if ($registroEvaluador) {
        // Actualizar el registro existente
        if ($registroEvaluador->valor != $request->evaluador_id) {
            $registroEvaluador->valor = $request->evaluador_id;
            $registroEvaluador->save();
            $cambiosRealizados = true;
            $tipoSolicitudCorreo = 'cambio_evaluador';
            $nuevoEvaluadorCorreo = User::with('tipo_documento')->find($request->evaluador_id);
            
            // ===== ASIGNAR EL ROL DE EVALUADOR_PRACTICA AL NUEVO EVALUADOR =====
            if ($nuevoEvaluadorCorreo && !$nuevoEvaluadorCorreo->hasRole('evaluador_practica')) {
                $nuevoEvaluadorCorreo->assignRole('evaluador_practica');
            }
        }
    }
}

        // Verificar retiro de estudiante
        if ($request->has('retirar_estudiante') && !empty($request->retirar_estudiante)) {
            $estudianteId = $request->retirar_estudiante;

            // Obtener o crear el campo retirados_practica
            $campoRetirados = Campo::where('name', 'retirados_practica')
                ->where('tipo_solicitud_id', $practica->tipo_solicitud_id)
                ->first();

            if ($campoRetirados) {
                $existentes = PracticaValorCampo::where('practica_id', $practica->id)
                    ->where('campo_id', $campoRetirados->id)
                    ->first();

                $retirados = [];
                if ($existentes && $existentes->valor) {
                    $retirados = json_decode($existentes->valor, true) ?: [];
                }

                // Agregar el estudiante si no está ya retirado
                if (!in_array($estudianteId, $retirados)) {
                    $retirados[] = (int) $estudianteId;

                    PracticaValorCampo::updateOrCreate(
                        ['practica_id' => $practica->id, 'campo_id' => $campoRetirados->id],
                        ['valor' => json_encode($retirados)]
                    );

                    $cambiosRealizados = true;
                    $tipoSolicitudCorreo = 'retiro';
                    $estudianteRetiradoCorreo = User::with('tipo_documento')->find($estudianteId);
                }
            }
        }

        if (!$cambiosRealizados) {
            return response()->json(['error' => 'No se realizó ningún cambio'], 422);
        }

        $practica->load(['user.tipo_documento', 'valoresCampos.campo']);
        $campos = $practica->camposConValores();
        $integrante2Id = collect($campos)
            ->firstWhere('campo', 'id_integrante_2')['valor'] ?? null;
        $integrante2 = null;

        if (!empty($integrante2Id)) {
            $integrante2 = User::with('tipo_documento')->find($integrante2Id);
        }

        $data = [
            'tipo_correo' => 'respuesta_ajuste_practica',
            'cuerpo_correo' => [
                'estado' => $practica->estado,
                'tipo_solicitud' => $tipoSolicitudCorreo,
                'comentarios' => $request->comentarios_config_admin,
                'nro_acta' => $request->nro_acta_ajustes,
                'fecha_acta' => $request->fecha_acta_ajustes,
                'estudiante' => $practica->user,
                'integrante_2' => $integrante2,
                'nueva_fecha_limite' => $nuevaFechaLimiteCorreo,
                'nuevo_director' => $nuevoDirectorCorreo,
                'nuevo_evaluador' => $nuevoEvaluadorCorreo,
                'estudiante_retirado' => $estudianteRetiradoCorreo,
                'campos' => $campos,
            ],
            'adjuntar_archivos' => false,
        ];

        $destinatarios = [
            $practica->user->email,
        ];

        if (!empty($integrante2?->email)) {
            $destinatarios[] = $integrante2->email;
        }

        $destinatarios = array_unique(array_filter($destinatarios));
        Mail::to($destinatarios)
            ->queue(new PracticasMail($data));

        return response()->json(['success' => 'Respuesta enviada correctamente']);
    }

    // Funciones auxiliares privadas
    private function guardarValorCampo($practicaId, $campoName, $valor)
    {
        $tipoFase5 = TipoSolicitud::where('nombre', 'practicas_fase_5')->first();

        if (!$tipoFase5) {
            return;
        }

        $campo = Campo::where('tipo_solicitud_id', $tipoFase5->id)
            ->where('name', $campoName)
            ->first();

        if (!$campo) {
            return;
        }

        PracticaValorCampo::updateOrCreate(
            [
                'practica_id' => $practicaId,
                'campo_id' => $campo->id,
            ],
            [
                'valor' => $valor,
            ]
        );
    }

    private function getFaseActual($estado)
    {
        if ($estado === 'Finalizado') return 7;
        if (str_contains($estado, 'Fase')) {
            $partes = explode(' ', $estado);
            return (int) $partes[1];
        }
        return 0;
    }
}
