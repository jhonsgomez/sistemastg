<?php

namespace App\Http\Controllers;

use App\Mail\ProyectosGradoMail;
use App\Models\Acta;
use App\Models\Campo;
use App\Models\Fecha;
use App\Models\LineaInvestigacion;
use App\Models\Modalidad;
use App\Models\Nivel;
use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use App\Models\User;
use App\Models\ValorCampo;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RoadMapController extends Controller
{
    public function getType($name)
    {
        $type = TipoSolicitud::query()->where('nombre', '=', $name)->where('deleted_at', '=', NULL)->first();
        return $type;
    }

    function findCampo($campos, $nombreCampo)
    {
        foreach ($campos as $item) {
            if (isset($item['campo']['name']) && $item['campo']['name'] === $nombreCampo) {
                return $item['campo'];
            }
        }
        return null;
    }

    public function findCampoByName($campos, $name)
    {
        foreach ($campos as $item) {
            if (isset($item['campo']['name']) && $item['campo']['name'] === $name) {
                return $item['valor'] ? $item['valor'] : null;
            }
        }
    }

    public function findCamposByTipoSolicitud($campos, $tipo_id)
    {
        return collect($campos)
            ->filter(fn($item) => isset($item['campo']['tipo_solicitud_id']) && $item['campo']['tipo_solicitud_id'] == $tipo_id)
            ->map(fn($item) => [
                'campo' => $item['campo'],
                'valor' => $item['valor']
            ])
            ->values();
    }

    public function getFechasByPeriodo($periodo)
    {
        $fechas = Fecha::where('periodo', '=', $periodo)->first();
        return $fechas ? $fechas->fechas : null;
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
        $solicitud = null;
        $campos = null;
        $estado = null;
        $docentes = null;
        $director_actual = null;
        $evaluador_actual = null;
        $lineas_investigacion = null;
        $codigo_modalidad = null;

        try {
            $solicitud = Solicitud::query()->where('id', '=', $request->solicitud_id)->first();
            $lineas_investigacion = LineaInvestigacion::query()->where('deleted_at', '=', NULL)->get();
            $codigo_modalidad = null;

            $estado = $solicitud->estado;
            if (str_contains($solicitud->estado, 'Fase')) {
                $estado_array = explode(' ', $solicitud->estado);
                $estado = $estado_array[1];

                $tipo_estado = strtolower(str_replace(' ', '_', $solicitud->estado));
                $type = self::getType($tipo_estado);

                $campos = Campo::query()->where('tipo_solicitud_id', '=', $type->id)->where('deleted_at', '=', NULL)->get();

                if ($estado === '1') {
                    $codigo_modalidad = self::generarCodigoModalidad($solicitud->id);
                }
            }

            $campos_valores = $solicitud->camposConValores();

            $submited_fase1 = self::findCampoByName($campos_valores, 'submited') === null ? "false" : self::findCampoByName($campos_valores, 'submited');
            $submited_fase2 = self::findCampoByName($campos_valores, 'submited_fase2') === null ? "false" : self::findCampoByName($campos_valores, 'submited_fase2');
            $submited_fase3_director = self::findCampoByName($campos_valores, 'submited_fase3_director') === null ? "false" : self::findCampoByName($campos_valores, 'submited_fase3_director');
            $submited_fase3_evaluador = self::findCampoByName($campos_valores, 'submited_fase3_evaluador') === null ? "false" : self::findCampoByName($campos_valores, 'submited_fase3_evaluador');
            $submited_fase4 = self::findCampoByName($campos_valores, 'submited_fase4') === null ? "false" : self::findCampoByName($campos_valores, 'submited_fase4');
            $submited_fase5_director = self::findCampoByName($campos_valores, 'submited_fase5_director') === null ? "false" : self::findCampoByName($campos_valores, 'submited_fase5_director');
            $submited_fase5_evaluador = self::findCampoByName($campos_valores, 'submited_fase5_evaluador') === null ? "false" : self::findCampoByName($campos_valores, 'submited_fase5_evaluador');
            $submited_icfes = self::findCampoByName($campos_valores, 'submited_icfes');

            $integrante_1 = User::query()->where('id', $this->findCampoByName($campos_valores, 'id_integrante_1'))->first();
            $integrante_2 = User::query()->where('id', $this->findCampoByName($campos_valores, 'id_integrante_2'))->first();
            $integrante_3 = User::query()->where('id', $this->findCampoByName($campos_valores, 'id_integrante_3'))->first();

            $lista_integrantes = [];

            if (isset($integrante_1)) $lista_integrantes[] = $integrante_1;
            if (isset($integrante_2)) $lista_integrantes[] = $integrante_2;
            if (isset($integrante_3)) $lista_integrantes[] = $integrante_3;

            if (isset($submited_icfes)) {
                $submited_json = json_decode($submited_icfes);
                $submited_icfes = collect($submited_json)->contains(auth()->user()->id) ? "true" : "false";
            } else {
                $submited_icfes = "false";
            }

            $periodo_proyecto = self::findCampoByName($campos_valores, 'periodo');
            $fechas = self::getFechasByPeriodo($periodo_proyecto);
            $director_actual = self::findCampoByName($campos_valores, 'director_id');
            $evaluador_actual = self::findCampoByName($campos_valores, 'evaluador_id');

            $fecha_inicio_informe = self::findCampoByName($campos_valores, 'fecha_inicio_informe');
            $fecha_minima_informe = null;
            if (isset($fecha_inicio_informe)) {
                $fecha_minima_informe = Carbon::parse($fecha_inicio_informe)->addDays(env('DIAS_MINIMOS_INFORME'))->format('Y-m-d');
            }
            $fecha_maxima_informe = self::findCampoByName($campos_valores, 'fecha_maxima_informe');

            $type_propuestas_banco = self::getType('solicitud_banco');
            $propuestas_banco = Solicitud::query()->where('estado', '=', 'Aprobada')
                ->where('tipo_solicitud_id', '=', $type_propuestas_banco->id)
                ->whereHas('valoresCampos', function ($q) {
                    $q->whereHas('campo', function ($q) {
                        $q->where(function ($q) {
                            $q->where('name', 'nivel')
                                ->where('valores_campos.valor', auth()->user()->nivel_id);
                        });
                        $q->orWhere(function ($q) {
                            $q->where('name', '=', 'disponible')
                                ->where('valores_campos.valor', 'true');
                        });
                    });
                })
                ->orderBy('created_at', 'desc')->get();

            $docentes = User::role('docente')->get();

            $ideas_banco = [];

            $anio_actual = date('Y');
            $mes_actual = date('n');

            $periodo_actual = ($mes_actual <= 6) ? "$anio_actual-1" : "$anio_actual-2";
            $modalidad_proyecto = Modalidad::findOrFail(self::findCampoByName($campos_valores, 'modalidad'));

            foreach ($propuestas_banco as $propuesta) {
                $campos_propuesta = $propuesta->camposConValores();
                $propuesta_id = $propuesta->id;
                $titulo = self::findCampoByName($campos_propuesta, 'titulo');
                $modalidad = Modalidad::findOrFail(self::findCampoByName($campos_propuesta, 'modalidad'));
                $nivel = Nivel::findOrFail(self::findCampoByName($campos_propuesta, 'nivel'));
                $periodo = self::findCampoByName($campos_propuesta, 'periodo');
                $disponible = self::findCampoByName($campos_propuesta, 'disponible');
                $docente = User::findOrFail($propuesta->user_id);

                if ($disponible === "true" && $periodo === $periodo_actual && $modalidad_proyecto->id === $modalidad->id && $nivel->id === auth()->user()->nivel_id) {
                    $ideas_banco[] = (object) [
                        'propuesta_id' => $propuesta_id,
                        'titulo' => $titulo,
                        'modalidad' => $modalidad->nombre,
                        'nivel' => $nivel->nombre,
                        'periodo' => $periodo,
                        'docente' => $docente->name
                    ];
                }
            }

            return view('roadmap.index', compact(['fechas', 'solicitud', 'lineas_investigacion', 'estado', 'campos', 'ideas_banco', 'docentes', 'codigo_modalidad', 'submited_fase1', 'submited_fase2', 'submited_fase3_director', 'submited_fase3_evaluador', 'submited_fase4', 'submited_fase5_director', 'submited_fase5_evaluador', 'submited_icfes', 'fecha_inicio_informe', 'fecha_minima_informe', 'fecha_maxima_informe', 'director_actual', 'evaluador_actual', 'lista_integrantes']));
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public static function generarCodigoModalidad($solicitudId)
    {
        $solicitud = Solicitud::with('user.nivel')->findOrFail($solicitudId);
        $nivelId = $solicitud->user->nivel_id;

        // Determinar prefijo
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

    // FASE 1:

    public function fase1(Request $request)
    {
        $type = null;
        $validator = null;

        try {
            if ($request->has('check_idea_banco')) {
                $request->merge(['check_idea_banco' => "true"]);

                $validator = Validator::make($request->all(), [
                    'idea_banco' => 'required',
                    'soporte_pago' => 'required',
                    'soporte_pago.*' => 'required|mimes:pdf|max:15360',
                    'solicitud_id' => 'required',
                    'submited' => 'required',
                ]);
            } else {
                $request->merge(['check_idea_banco' => "false"]);

                $validator = Validator::make($request->all(), [
                    'titulo' => 'required|string',
                    'objetivo' => 'required|string',
                    'linea_investigacion' => 'required',
                    'descripcion' => 'required|string',
                    'soporte_pago' => 'required',
                    'soporte_pago.*' => 'required|mimes:pdf|max:15360',
                    'solicitud_id' => 'required',
                    'submited' => 'required',
                ]);
            }

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $type = self::getType('fase_1');
            $campos = Campo::query()->where('tipo_solicitud_id', '=', $type->id)->where('deleted_at', '=', NULL)->get();
            $campo_soporte_pago = Campo::where('name', '=', 'soporte_pago')->firstOrFail();

            foreach ($campos as $campo) {
                if ($request->has($campo->name) && $campo->name != "soporte_pago") {
                    ValorCampo::create([
                        'solicitud_id' => $request->input('solicitud_id'),
                        'campo_id' => $campo->id,
                        'valor' => $request->input($campo->name)
                    ]);
                }
            }

            if ($request->hasFile('soporte_pago')) {
                $nombresArchivos = [];
                foreach ($request->file('soporte_pago') as $archivo) {
                    $extension = $archivo->getClientOriginalExtension();
                    $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
                    $archivo->storeAs('documentos_proyectos/proyecto-00' . $request->input('solicitud_id'), $nombreUnico, 'public');
                    $nombresArchivos[] = $nombreUnico;
                }

                ValorCampo::create([
                    'solicitud_id' => $request->input('solicitud_id'),
                    'campo_id' => $campo_soporte_pago->id,
                    'valor' => json_encode($nombresArchivos)
                ]);
            }

            if ($request->has('check_idea_banco')) {
                $proyecto = Solicitud::query()->where('id', '=', $request->input('solicitud_id'))->first();
                $campos = $proyecto->camposConValores();

                $check_idea_banco = self::findCampoByName($campos, 'check_idea_banco');

                if ($check_idea_banco === "true") {
                    $idea_banco_id = self::findCampoByName($campos, 'idea_banco');
                    $idea_banco = Solicitud::query()
                        ->where('id', '=', $idea_banco_id)
                        ->firstOrFail();

                    $campos_idea = $idea_banco->camposConValores();
                    $campo_disponible = self::findCampo($campos_idea, 'disponible');

                    ValorCampo::where('solicitud_id', '=', $idea_banco->id)
                        ->where('campo_id', '=', $campo_disponible['id'])
                        ->firstOrFail()
                        ->update(['valor' => 'false']);
                }
            }

            self::sendEmailFase1Estudiante($request->input('solicitud_id'));
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function reply_fase1(Request $request)
    {
        $validator = null;

        try {
            if ($request->has('estado')) {
                if ($request->input('estado') === "Aprobado") {
                    $validator = Validator::make($request->all(), [
                        'estado' => 'required',
                        'nro_acta_fase_1' => 'required|numeric',
                        'fecha_acta_fase_1' => 'required|date',
                        'codigo_modalidad' => 'required',
                        'director' => 'required',
                        'evaluador' => 'required',
                        'respuesta_fase1' => 'required',
                        'solicitud_id' => 'required',
                    ], [
                        'nro_acta_fase_1.required' => 'El número de acta es obligatorio.',
                        'nro_acta_fase_1.numeric' => 'El número de acta debe ser un número.',
                        'fecha_acta_fase_1.required' => 'La fecha del acta es obligatoria.',
                        'fecha_acta_fase_1.date' => 'La fecha del acta debe ser una fecha válida.',
                        'codigo_modalidad.required' => 'El código de modalidad es obligatorio.'
                    ]);

                    if ($request->input('director') === $request->input('evaluador')) {
                        return response()->json(['errors' => ['director' => ['El director y el evaluador no pueden ser la misma persona.']]], 422);
                    }
                } elseif ($request->input('estado') === "Rechazado") {
                    $validator = Validator::make($request->all(), [
                        'estado' => 'required',
                        'nro_acta_fase_1' => 'required|numeric',
                        'fecha_acta_fase_1' => 'required|date',
                        'respuesta_fase1' => 'required',
                        'solicitud_id' => 'required',
                    ], [
                        'nro_acta_fase_1.required' => 'El número de acta es obligatorio.',
                        'nro_acta_fase_1.numeric' => 'El número de acta debe ser un número.',
                        'fecha_acta_fase_1.required' => 'La fecha del acta es obligatoria.',
                        'fecha_acta_fase_1.date' => 'La fecha del acta debe ser una fecha válida.',
                    ]);
                }
            } else {
                $validator = Validator::make($request->all(), [
                    'estado' => 'required',
                    'nro_acta_fase_1' => 'required|numeric',
                    'fecha_acta_fase_1' => 'required|date',
                    'respuesta_fase1' => 'required',
                    'solicitud_id' => 'required',
                ], [
                    'nro_acta_fase_1.required' => 'El número de acta es obligatorio.',
                    'nro_acta_fase_1.numeric' => 'El número de acta debe ser un número.',
                    'fecha_acta_fase_1.required' => 'La fecha del acta es obligatoria.',
                    'fecha_acta_fase_1.date' => 'La fecha del acta debe ser una fecha válida.',
                ]);
            }

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            DB::transaction(function () use ($request) {
                $data_old = [];

                $solicitud = Solicitud::query()
                    ->where('id', '=', $request->input('solicitud_id'))
                    ->firstOrFail();

                $campos = $solicitud->camposConValores();
                $idea_disponible = 'false';

                if ($request->input('estado') === 'Aprobado') {
                    // idea no disponible
                    $idea_disponible = 'false';

                    // Asignar codigo de modalidad
                    $codigo_modalidad = $request->input('codigo_modalidad');
                    $campo_codigo_modalidad = self::findCampo($campos, 'codigo_modalidad');
                    ValorCampo::where('solicitud_id', '=', $solicitud->id)
                        ->where('campo_id', '=', $campo_codigo_modalidad['id'])
                        ->firstOrFail()
                        ->update(['valor' => $codigo_modalidad]);

                    // Asignar roles y actualizar director
                    $director = User::findOrFail($request->input('director'));
                    $director->assignRole('director');

                    $campo_director = self::findCampo($campos, 'director_id');
                    ValorCampo::where('solicitud_id', '=', $solicitud->id)
                        ->where('campo_id', '=', $campo_director['id'])
                        ->firstOrFail()
                        ->update(['valor' => $director->id]);

                    // Asignar roles y actualizar evaluador
                    $evaluador = User::findOrFail($request->input('evaluador'));
                    $evaluador->assignRole('evaluador');

                    $campo_evaluador = self::findCampo($campos, 'evaluador_id');
                    ValorCampo::where('solicitud_id', '=', $solicitud->id)
                        ->where('campo_id', '=', $campo_evaluador['id'])
                        ->firstOrFail()
                        ->update(['valor' => $evaluador->id]);

                    // En caso de que exista un codirector se crear el ValorCampo
                    $campo_codirector = self::findCampo($campos, 'codirector_id');
                    $codirector_id = $request->input('codirector');

                    if ($codirector_id !== null) {
                        $codirector = User::findOrFail($codirector_id);

                        ValorCampo::where('solicitud_id', '=', $solicitud->id)
                            ->where('campo_id', '=', $campo_codirector['id'])
                            ->firstOrFail()
                            ->update(['valor' => $codirector->id]);
                    }

                    // Actualizar estado de la solicitud
                    $solicitud->update(['estado' => 'Fase 2']);

                    // Guardar Acta
                    $acta = Acta::create([
                        'numero' => $request->input('nro_acta_fase_1'),
                        'fecha' => $request->input('fecha_acta_fase_1'),
                        'descripcion' => "Aprobación del pago de la modalidad",
                        'proyecto_id' => $request->input('solicitud_id')
                    ]);

                    $acta->save();
                } else {
                    // idea disponible
                    $idea_disponible = 'true';

                    $type = self::getType('fase_1');
                    $campos_valores_fase1 = self::findCamposByTipoSolicitud($campos, $type->id);
                    $check_idea_banco = self::findCampoByName($campos_valores_fase1, 'check_idea_banco');

                    if ($check_idea_banco === 'false') {
                        $data_old['titulo'] = self::findCampoByName($campos_valores_fase1, 'titulo');
                        $data_old['objetivo'] = self::findCampoByName($campos_valores_fase1, 'objetivo');
                        $data_old['linea_investigacion'] = self::findCampoByName($campos_valores_fase1, 'linea_investigacion');
                        $data_old['descripcion'] = self::findCampoByName($campos_valores_fase1, 'descripcion');
                    } else {
                        $idea_banco_id = self::findCampoByName($campos_valores_fase1, 'idea_banco');
                        $idea_banco = Solicitud::query()->where('id', '=', $idea_banco_id)->first();
                        $tipo_solicitud_banco = self::getType('solicitud_banco');
                        $campos_idea_banco = self::findCamposByTipoSolicitud($idea_banco->camposConValores(), $tipo_solicitud_banco->id);

                        $data_old['titulo'] = self::findCampoByName($campos_idea_banco, 'titulo');
                        $data_old['objetivo'] = self::findCampoByName($campos_idea_banco, 'objetivo');
                        $data_old['linea_investigacion'] = self::findCampoByName($campos_idea_banco, 'linea_investigacion');
                        $data_old['descripcion'] = null;
                    }

                    // vaciar todos los campos de fase 1 para la solicitud
                    foreach ($campos_valores_fase1 as $item) {
                        ValorCampo::where('solicitud_id', '=', $solicitud->id)
                            ->where('campo_id', '=', $item['campo']['id'])
                            ->firstOrFail()
                            ->delete();
                    }

                    // Guardar acta
                    $acta = Acta::create([
                        'numero' => $request->input('nro_acta_fase_1'),
                        'fecha' => $request->input('fecha_acta_fase_1'),
                        'descripcion' => "Rechazo del pago de la modalidad",
                        'proyecto_id' => $request->input('solicitud_id')
                    ]);

                    $acta->save();
                }

                // Actualizar disponible en banco
                $check_idea_banco = self::findCampoByName($campos, 'check_idea_banco');

                if ($check_idea_banco === "true") {
                    $idea_banco_id = self::findCampoByName($campos, 'idea_banco');
                    $idea_banco = Solicitud::query()
                        ->where('id', '=', $idea_banco_id)
                        ->firstOrFail();

                    $campos_idea = $idea_banco->camposConValores();
                    $campo_disponible = self::findCampo($campos_idea, 'disponible');

                    ValorCampo::where('solicitud_id', '=', $idea_banco->id)
                        ->where('campo_id', '=', $campo_disponible['id'])
                        ->firstOrFail()
                        ->update(['valor' => $idea_disponible]);
                }

                self::sendEmailFase1Docentes($request, $data_old);
            });

            return response()->json(['message' => 'Proceso completado']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function sendEmailFase1Estudiante($solicitud_id)
    {
        try {
            $cuerpo_correo = [];
            $correos_destinatarios = [];
            $asunto_correo = 'PROYECTO DE GRADO - FASE 1';
            $tipo_correo = 'fase_1';
            $comentarios = null;
            $esRespuesta = false;

            $solicitud = Solicitud::query()->where('id', '=', $solicitud_id)->first();
            $fase_0 = self::getType('fase_0');
            $fase_1 = self::getType('fase_1');

            $campos_fase_0 = self::findCamposByTipoSolicitud($solicitud->camposConValores(), $fase_0->id);

            $periodo_proyecto = self::findCampoByName($campos_fase_0, 'periodo');
            $fechas_proyecto = self::getFechasByPeriodo($periodo_proyecto);

            $cuerpo_correo['fecha_aprobacion'] = $fechas_proyecto['fecha_aprobacion_propuesta'];

            foreach ($campos_fase_0 as $item) {
                if (str_contains($item['campo']['name'], 'id_integrante') && isset($item['valor'])) {
                    $integrante = User::findOrFail($item['valor']);
                    $correos_destinatarios[] = $integrante->email;
                }
            }

            $campos = self::findCamposByTipoSolicitud($solicitud->camposConValores(), $fase_1->id);
            $check_idea_banco = self::findCampoByName($campos, 'check_idea_banco');

            if ($check_idea_banco === 'false') {
                $cuerpo_correo['titulo'] = self::findCampoByName($campos, 'titulo');
                $cuerpo_correo['objetivo'] = self::findCampoByName($campos, 'objetivo');
                $cuerpo_correo['linea_investigacion'] = LineaInvestigacion::findOrFail(self::findCampoByName($campos, 'linea_investigacion'))->nombre;
                $cuerpo_correo['descripcion'] = self::findCampoByName($campos, 'descripcion');
            } else {
                $idea_banco_id = self::findCampoByName($campos, 'idea_banco');
                $idea_banco = Solicitud::query()->where('id', '=', $idea_banco_id)->first();
                $tipo_solicitud_banco = self::getType('solicitud_banco');
                $campos_idea_banco = self::findCamposByTipoSolicitud($idea_banco->camposConValores(), $tipo_solicitud_banco->id);

                $cuerpo_correo['titulo'] = self::findCampoByName($campos_idea_banco, 'titulo');
                $cuerpo_correo['objetivo'] = self::findCampoByName($campos_idea_banco, 'objetivo');
                $cuerpo_correo['linea_investigacion'] = LineaInvestigacion::findOrFail(self::findCampoByName($campos_idea_banco, 'linea_investigacion'))->nombre;
                $cuerpo_correo['descripcion'] = null;
            }

            Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function sendEmailFase1Docentes($request, $data_old)
    {
        try {
            $solicitud_id = $request->solicitud_id;
            $cuerpo_correo = [];
            $correos_destinatarios = [];
            $correos_directores = [];
            $correos_evaluadores = [];
            $asunto_correo = '';
            $tipo_correo = 'respuesta_fase_1';
            $comentarios = $request->respuesta_fase1;
            $esRespuesta = true;

            $solicitud = Solicitud::where('id', '=', $solicitud_id)->first();
            $campos_solicitud = $solicitud->camposConValores();

            $fase_0 = self::getType('fase_0');
            $fase_1 = self::getType('fase_1');

            $campos_fase_0 = self::findCamposByTipoSolicitud($campos_solicitud, $fase_0->id);

            $periodo_proyecto = self::findCampoByName($campos_fase_0, 'periodo');
            $fechas_proyecto = self::getFechasByPeriodo($periodo_proyecto);

            $cuerpo_correo['nro_acta'] = $request->nro_acta_fase_1;
            $cuerpo_correo['fecha_acta'] = $request->fecha_acta_fase_1;

            $cuerpo_correo['estado'] = $request->estado;
            $cuerpo_correo['fecha_aprobacion'] = $fechas_proyecto['fecha_aprobacion_propuesta'];

            $cuerpo_correo['director_nombre'] = null;
            $cuerpo_correo['director_correo'] = null;

            $cuerpo_correo['evaluador_nombre'] = null;
            $cuerpo_correo['evaluador_correo'] = null;

            $cuerpo_correo['codirector_nombre'] = null;
            $cuerpo_correo['codirector_correo'] = null;

            foreach ($campos_fase_0 as $campo) {
                switch ($campo['campo']['name']) {
                    case 'modalidad':
                        $cuerpo_correo[$campo['campo']['name']] = Modalidad::findOrFail($campo['valor'])->nombre;
                        break;
                    case 'nivel':
                        $cuerpo_correo[$campo['campo']['name']] = Nivel::findOrFail($campo['valor'])->nombre;
                        break;
                    case 'id_integrante_1':
                    case 'id_integrante_2':
                    case 'id_integrante_3':
                        $tag = str_replace('id_', '', $campo['campo']['name']);
                        $integrante = User::findOrFail($campo['valor']);
                        $cuerpo_correo[$tag] = $integrante;
                        $correos_destinatarios[] = $integrante->email;
                        break;
                    default:
                        $cuerpo_correo[$campo['campo']['name']] = $campo['valor'];
                        break;
                }
            }

            if ($request->estado === 'Rechazado') {
                $cuerpo_correo['titulo'] = $data_old['titulo'];
                $cuerpo_correo['objetivo'] = $data_old['objetivo'];
                $cuerpo_correo['linea_investigacion'] = LineaInvestigacion::findOrFail($data_old['linea_investigacion'])->nombre;
                $cuerpo_correo['descripcion'] = $data_old['descripcion'];
            } else if ($request->estado === 'Aprobado') {
                $campos = self::findCamposByTipoSolicitud($campos_solicitud, $fase_1->id);
                $check_idea_banco = self::findCampoByName($campos, 'check_idea_banco');

                $cuerpo_correo['codigo_modalidad'] = $request->codigo_modalidad;

                if ($check_idea_banco === 'false') {
                    $cuerpo_correo['titulo'] = self::findCampoByName($campos, 'titulo');
                    $cuerpo_correo['objetivo'] = self::findCampoByName($campos, 'objetivo');
                    $cuerpo_correo['linea_investigacion'] = LineaInvestigacion::findOrFail(self::findCampoByName($campos, 'linea_investigacion'))->nombre;
                    $cuerpo_correo['descripcion'] = self::findCampoByName($campos, 'descripcion');
                } else {
                    $idea_banco_id = self::findCampoByName($campos, 'idea_banco');
                    $idea_banco = Solicitud::query()->where('id', '=', $idea_banco_id)->first();
                    $tipo_solicitud_banco = self::getType('solicitud_banco');
                    $campos_idea_banco = self::findCamposByTipoSolicitud($idea_banco->camposConValores(), $tipo_solicitud_banco->id);

                    $cuerpo_correo['titulo'] = self::findCampoByName($campos_idea_banco, 'titulo');
                    $cuerpo_correo['objetivo'] = self::findCampoByName($campos_idea_banco, 'objetivo');
                    $cuerpo_correo['linea_investigacion'] = LineaInvestigacion::findOrFail(self::findCampoByName($campos_idea_banco, 'linea_investigacion'))->nombre;
                    $cuerpo_correo['descripcion'] = null;
                }

                $director_id = $request->director;
                $evaluador_id = $request->evaluador;
                $codirector_id = $request->codirector ?? null;

                $director = User::findOrFail($director_id);
                $evaluador = User::findOrFail($evaluador_id);

                $correos_directores[] = $director->email;
                $correos_evaluadores[] = $evaluador->email;

                $cuerpo_correo['director_nombre'] = $director->name;
                $cuerpo_correo['director_correo'] = $director->email;

                $cuerpo_correo['evaluador_nombre'] = $evaluador->name;
                $cuerpo_correo['evaluador_correo'] = $evaluador->email;

                if ($codirector_id !== null) {
                    $codirector = User::findOrFail($codirector_id);

                    $correos_codirectores[] = $codirector->email;

                    $cuerpo_correo['codirector_nombre'] = $codirector->name;
                    $cuerpo_correo['codirector_correo'] = $codirector->email;

                    $asunto_correo = 'DOCENTE CODIRECTOR DE PROYECTO';
                    $cuerpo_correo['destinatario'] = 'codirector';
                    Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_codirectores, $esRespuesta));
                }

                $asunto_correo = 'DOCENTE DIRECTOR DE PROYECTO';
                $cuerpo_correo['destinatario'] = 'director';
                Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_directores, $esRespuesta));

                $asunto_correo = 'DOCENTE EVALUADOR DE PROYECTO';
                $cuerpo_correo['destinatario'] = 'evaluador';
                Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_evaluadores, $esRespuesta));
            }

            $asunto_correo = 'PROYECTO DE GRADO - FASE 1';
            $cuerpo_correo['destinatario'] = 'estudiante';
            Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    // FASE 2:

    public function fase2(Request $request)
    {
        $validator = null;

        try {
            $validator = Validator::make($request->all(), [
                'solicitud_id' => 'required',
                'doc_propuesta' => 'required',
                'doc_propuesta.*' => 'required|max:15360',
                'submited_fase2' => 'required',
            ], [
                'doc_propuesta.required' => 'El documento de la propuesta es obligatorio.'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            DB::transaction(function () use ($request) {
                $type = self::getType('fase_2');
                $campos = Campo::query()->where('tipo_solicitud_id', '=', $type->id)->where('deleted_at', '=', NULL)->get();

                foreach ($campos as $campo) {
                    if ($request->has($campo->name) && $campo->name != "doc_propuesta" && $campo->name != "recordatorio_fase2") {
                        ValorCampo::create([
                            'solicitud_id' => $request->solicitud_id,
                            'campo_id' => $campo->id,
                            'valor' => $request->input($campo->name)
                        ]);
                    }
                }

                $campo_recordatorio_fase2 = Campo::where('name', '=', 'recordatorio_fase2')->firstOrFail();
                $fecha_recordatorio = Carbon::now()->addDays(env('DIAS_RECORDATORIO'))->format('Y-m-d');

                ValorCampo::updateOrCreate(
                    [
                        'solicitud_id' => $request->input('solicitud_id'),
                        'campo_id' => $campo_recordatorio_fase2->id,
                    ],
                    [
                        'valor' => $fecha_recordatorio
                    ]
                );

                $campo_doc_propuesta = Campo::where('name', '=', 'doc_propuesta')->firstOrFail();

                if ($request->hasFile('doc_propuesta')) {
                    $nombresArchivos = [];
                    foreach ($request->file('doc_propuesta') as $archivo) {
                        $extension = $archivo->getClientOriginalExtension();
                        $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
                        $archivo->storeAs('documentos_proyectos/proyecto-00' . $request->solicitud_id, $nombreUnico, 'public');
                        $nombresArchivos[] = $nombreUnico;
                    }

                    ValorCampo::updateOrCreate(
                        [
                            'solicitud_id' => $request->input('solicitud_id'),
                            'campo_id' => $campo_doc_propuesta->id,
                        ],
                        [
                            'valor' => json_encode($nombresArchivos)
                        ]
                    );

                    self::sendEmailFase2Estudiante($request->solicitud_id);
                }
            });
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function reply_fase2(Request $request)
    {
        $validator = null;

        try {
            if (isset($request->estado_fase2)) {
                if ($request->estado_fase2 === 'Aprobado') {
                    $validator = Validator::make($request->all(), [
                        'estado_fase2' => 'required',
                        'doc_turnitin_fase2' => 'required',
                        'doc_turnitin_fase2.*' => 'required|max:15360',
                        'respuesta_fase2' => 'required',
                        'solicitud_id' => 'required',
                    ], [
                        'estado_fase2.required' => 'El estado es obligatorio.',
                        'doc_turnitin_fase2.required' => 'El informe de plagio es obligatorio.',
                        'respuesta_fase2.required' => 'La descripción de la respuesta es obligatoria.'
                    ]);
                } else {
                    $validator = Validator::make($request->all(), [
                        'estado_fase2' => 'required',
                        'doc_respuesta_fase2' => 'required',
                        'doc_respuesta_fase2.*' => 'required|max:15360',
                        'respuesta_fase2' => 'required',
                        'solicitud_id' => 'required',
                    ], [
                        'estado_fase2.required' => 'El estado es obligatorio.',
                        'doc_respuesta_fase2.required' => 'El documento de propuesta es obligatorio.',
                        'respuesta_fase2.required' => 'La descripción de la respuesta es obligatoria.'
                    ]);
                }
            } else {
                $validator = Validator::make($request->all(), [
                    'estado_fase2' => 'required',
                    'doc_turnitin_fase2' => 'required',
                    'doc_turnitin_fase2.*' => 'required|max:15360',
                    'respuesta_fase2' => 'required',
                    'solicitud_id' => 'required',
                ], [
                    'estado_fase2.required' => 'El estado es obligatorio.',
                    'doc_turnitin_fase2.required' => 'El informe de plagio es obligatorio.',
                    'respuesta_fase2.required' => 'La descripción de la respuesta es obligatoria.'
                ]);
            }

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            DB::transaction(function () use ($request) {
                $solicitud = Solicitud::query()
                    ->where('id', '=', $request->solicitud_id)
                    ->firstOrFail();

                $campos = $solicitud->camposConValores();

                if ($request->estado_fase2 === 'Aprobado') {
                    $solicitud->update(['estado' => 'Fase 3']);

                    // Se llenan los campos de la fase 3 para que el evaluador empiece a revisar

                    // 1. llenar campos de fase 3:
                    $fase_3 = self::getType('fase_3');
                    $campos_fase_3 = Campo::query()->where('tipo_solicitud_id', '=', $fase_3->id)->where('deleted_at', '=', NULL)->get();
                    $campo_doc_turnitin = Campo::where('name', '=', 'doc_turnitin')->firstOrFail();

                    foreach ($campos_fase_3 as $campo) {
                        if ($campo->name == 'submited_fase3_director') {
                            ValorCampo::create([
                                'solicitud_id' => $request->solicitud_id,
                                'campo_id' => $campo->id,
                                'valor' => 'true'
                            ]);
                        }
                    }

                    // 2. Asignar fecha de recordatorio
                    $campo_recordatorio_fase3 = Campo::where('name', '=', 'recordatorio_fase3')->firstOrFail();
                    $fecha_recordatorio = Carbon::now()->addDays(env('DIAS_RECORDATORIO'))->format('Y-m-d');

                    ValorCampo::updateOrCreate(
                        [
                            'solicitud_id' => $request->input('solicitud_id'),
                            'campo_id' => $campo_recordatorio_fase3->id,
                        ],
                        [
                            'valor' => $fecha_recordatorio
                        ]
                    );

                    // 3. Verificar si existe documento de turniting y guardarlo
                    if ($request->hasFile('doc_turnitin_fase2')) {
                        $nombresArchivos = [];
                        foreach ($request->file('doc_turnitin_fase2') as $archivo) {
                            $extension = $archivo->getClientOriginalExtension();
                            $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
                            $archivo->storeAs('documentos_proyectos/proyecto-00' . $request->input('solicitud_id'), $nombreUnico, 'public');
                            $nombresArchivos[] = $nombreUnico;
                        }

                        ValorCampo::updateOrCreate(
                            [
                                'solicitud_id' => $request->input('solicitud_id'),
                                'campo_id' => $campo_doc_turnitin->id,
                            ],
                            [
                                'valor' => json_encode($nombresArchivos)
                            ]
                        );
                    }

                    // 4. Enviar correo a evaluador para que lo revise
                    self::sendEmailFase2Response($request);
                } else {
                    $type = self::getType('fase_2');
                    $campos_valores_fase2 = self::findCamposByTipoSolicitud($campos, $type->id);
                    $campo_doc_turnitin = Campo::where('name', '=', 'doc_turnitin')->firstOrFail();

                    foreach ($campos_valores_fase2 as $item) {
                        ValorCampo::where('solicitud_id', '=', $solicitud->id)
                            ->where('campo_id', '=', $item['campo']['id'])
                            ->firstOrFail()
                            ->delete();
                    }

                    // 1. Verificar si existe documento de turniting y guardarlo
                    if ($request->hasFile('doc_turnitin_fase2')) {
                        $nombresArchivos = [];
                        foreach ($request->file('doc_turnitin_fase2') as $archivo) {
                            $extension = $archivo->getClientOriginalExtension();
                            $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
                            $archivo->storeAs('documentos_proyectos/proyecto-00' . $request->input('solicitud_id'), $nombreUnico, 'public');
                            $nombresArchivos[] = $nombreUnico;
                        }

                        ValorCampo::updateOrCreate(
                            [
                                'solicitud_id' => $request->input('solicitud_id'),
                                'campo_id' => $campo_doc_turnitin->id,
                            ],
                            [
                                'valor' => json_encode($nombresArchivos)
                            ]
                        );
                    }

                    // 2. Logica del archivo adjunto con comentarios del director
                    $nombreUnico = null;

                    if ($request->hasFile('doc_respuesta_fase2')) {
                        $nombresArchivos = [];
                        foreach ($request->file('doc_respuesta_fase2') as $archivo) {
                            $extension = $archivo->getClientOriginalExtension();
                            $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
                            $archivo->storeAs('documentos_proyectos/proyecto-00' . $request->solicitud_id, $nombreUnico, 'public');
                            $nombresArchivos[] = $nombreUnico;
                        }
                    }

                    $adjunto = public_path("storage/documentos_proyectos/proyecto-00{$request->solicitud_id}/" . $nombreUnico);

                    // 3. Enviar correo a los estudiantes con archivo adjunto para volver a enviar propuesta
                    self::sendEmailFase2Response($request, $adjunto);
                }
            });
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function getTituloProyecto($proyecto)
    {
        $campos = $proyecto->camposConValores();
        $check_idea_banco = $this->findCampoByName($campos, 'check_idea_banco');
        $titulo = null;

        if ($check_idea_banco == 'true') {
            $idea_banco = Solicitud::query()->where('id', $this->findCampoByName($campos, 'idea_banco'))->first();
            $campos_idea = $idea_banco->camposConValores();
            $titulo = $this->findCampoByName($campos_idea, 'titulo');
        } else {
            $titulo = $this->findCampoByName($campos, 'titulo');
        }

        return $titulo;
    }

    public function sendEmailFase2Estudiante($proyecto_id)
    {
        try {
            $cuerpo_correo = [];
            $correos_destinatarios = [];
            $asunto_correo = 'PROYECTO DE GRADO - FASE 2';
            $tipo_correo = 'fase_2';
            $comentarios = null;
            $esRespuesta = false;

            $proyecto = Solicitud::query()->where('id', '=', $proyecto_id)->first();
            $campos_proyecto = $proyecto->camposConValores();
            $titulo = self::getTituloProyecto($proyecto);

            $periodo_proyecto = self::findCampoByName($campos_proyecto, 'periodo');
            $fechas_proyecto = self::getFechasByPeriodo($periodo_proyecto);

            $cuerpo_correo['titulo'] = $titulo;
            $cuerpo_correo['fecha_aprobacion'] = $fechas_proyecto['fecha_aprobacion_propuesta'];

            $integrante_1 = User::query()->where('id', self::findCampoByName($campos_proyecto, 'id_integrante_1'))->first();
            $integrante_2 = User::query()->where('id', self::findCampoByName($campos_proyecto, 'id_integrante_2'))->first();
            $integrante_3 = User::query()->where('id', self::findCampoByName($campos_proyecto, 'id_integrante_3'))->first();

            $director = User::query()->where('id', self::findCampoByName($campos_proyecto, 'director_id'))->first();

            if (isset($integrante_1)) {
                $correos_destinatarios[] = $integrante_1->email;
            }

            if (isset($integrante_2)) {
                $correos_destinatarios[] = $integrante_2->email;
            }

            if (isset($integrante_3)) {
                $correos_destinatarios[] = $integrante_3->email;
            }

            $correos_destinatarios[] = $director->email;

            // se envia correo
            Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function sendEmailFase2Response($request, $adjunto = null)
    {
        try {
            $cuerpo_correo = [];
            $correos_destinatarios = [];
            $asunto_correo = 'PROYECTO DE GRADO - FASE 2';
            $tipo_correo = 'respuesta_fase_2';
            $comentarios = $request->respuesta_fase2;
            $esRespuesta = true;

            $proyecto_id = $request->solicitud_id;
            $estado = $request->estado_fase2;

            $proyecto = Solicitud::query()->where('id', '=', $proyecto_id)->first();
            $campos_proyecto = $proyecto->camposConValores();
            $titulo = self::getTituloProyecto($proyecto);

            $periodo_proyecto = self::findCampoByName($campos_proyecto, 'periodo');
            $fechas_proyecto = self::getFechasByPeriodo($periodo_proyecto);

            $cuerpo_correo['titulo'] = $titulo;
            $cuerpo_correo['estado'] = $estado;
            $cuerpo_correo['fecha_aprobacion'] = $fechas_proyecto['fecha_aprobacion_propuesta'];
            $cuerpo_correo['adjunto'] = $adjunto;

            $integrante_1 = User::query()->where('id', self::findCampoByName($campos_proyecto, 'id_integrante_1'))->first();
            $integrante_2 = User::query()->where('id', self::findCampoByName($campos_proyecto, 'id_integrante_2'))->first();
            $integrante_3 = User::query()->where('id', self::findCampoByName($campos_proyecto, 'id_integrante_3'))->first();

            if (isset($integrante_1)) {
                $correos_destinatarios[] = $integrante_1->email;
            }

            if (isset($integrante_2)) {
                $correos_destinatarios[] = $integrante_2->email;
            }

            if (isset($integrante_3)) {
                $correos_destinatarios[] = $integrante_3->email;
            }

            // Se envia correo a estudiantes
            Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));

            if ($estado === 'Aprobado') {
                // Se envia correo a evaluador para notificarle la revision en FASE 3
                $correos_destinatarios = [];
                $evaluador = User::query()->where('id', self::findCampoByName($campos_proyecto, 'evaluador_id'))->first();
                $correos_destinatarios[] = $evaluador->email;

                // Se envia correo a evaluador
                Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    // FASE 3:

    public function reply_fase3(Request $request)
    {
        $validator = null;

        try {
            if (isset($request->estado_fase3)) {
                if ($request->estado_fase3 === 'Aprobado') {
                    $validator = Validator::make($request->all(), [
                        'estado_fase3' => 'required',
                        'respuesta_fase3' => 'required',
                        'solicitud_id' => 'required',
                        'remitente' => 'required',
                    ], [
                        'estado_fase3.required' => 'El estado es obligatorio.',
                        'respuesta_fase3.required' => 'La descripción de la respuesta es obligatoria.'
                    ]);
                } else {
                    $validator = Validator::make($request->all(), [
                        'estado_fase3' => 'required',
                        'doc_respuesta_fase3' => 'required',
                        'doc_respuesta_fase3.*' => 'required|max:15360',
                        'respuesta_fase3' => 'required',
                        'solicitud_id' => 'required',
                        'remitente' => 'required',
                    ], [
                        'estado_fase3.required' => 'El estado es obligatorio.',
                        'doc_respuesta_fase3.required' => 'El documento de propuesta es obligatorio.',
                        'respuesta_fase3.required' => 'La descripción de la respuesta es obligatoria.'
                    ]);
                }
            } else {
                $validator = Validator::make($request->all(), [
                    'estado_fase3' => 'required',
                    'respuesta_fase3' => 'required',
                    'solicitud_id' => 'required',
                    'remitente' => 'required',
                ], [
                    'estado_fase3.required' => 'El estado es obligatorio.',
                    'respuesta_fase3.required' => 'La descripción de la respuesta es obligatoria.'
                ]);
            }

            if (auth()->user()->hasRole(['admin', 'super_admin'])) {
                $validator->after(function ($validator) use ($request) {
                    if (empty($request->nro_acta_fase3)) {
                        $validator->errors()->add('nro_acta_fase3', 'El número de acta es obligatorio.');
                    }

                    if (!is_numeric($request->nro_acta_fase3)) {
                        $validator->errors()->add('nro_acta_fase3', 'El número de acta debe ser un número válido.');
                    }

                    if (empty($request->fecha_acta_fase3)) {
                        $validator->errors()->add('fecha_acta_fase3', 'La fecha del acta es obligatoria.');
                    }

                    if (!strtotime($request->fecha_acta_fase3)) {
                        $validator->errors()->add('fecha_acta_fase3', 'La fecha del acta debe ser una fecha válida.');
                    }
                });
            }

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            DB::transaction(function () use ($request) {
                $solicitud = Solicitud::query()
                    ->where('id', '=', $request->solicitud_id)
                    ->firstOrFail();

                $campos = $solicitud->camposConValores();

                if ($request->estado_fase3 === 'Aprobado') {
                    if ($request->remitente == 1) {
                        $solicitud->update(['estado' => 'Fase 4']);

                        // Asignar fecha inicio y maxima para informe final
                        $fase_4 = self::getType('fase_4');
                        $campos_fase_4 = Campo::query()->where('tipo_solicitud_id', '=', $fase_4->id)->where('deleted_at', '=', NULL)->get();

                        $fecha_inicio_informe = Carbon::now()->format('Y-m-d');
                        $fecha_minima_informe = Carbon::now()->addDays(env('DIAS_MINIMOS_INFORME'))->format('Y-m-d');
                        $fecha_maxima_informe = Carbon::now()->addDays(env('DIAS_MAXIMOS_INFORME'))->format('Y-m-d');

                        $request->request->add(['fecha_minima_informe' => $fecha_minima_informe]);
                        $request->request->add(['fecha_maxima_informe' => $fecha_maxima_informe]);

                        foreach ($campos_fase_4 as $campo) {
                            if ($campo->name == 'fecha_inicio_informe') {
                                ValorCampo::create([
                                    'solicitud_id' => $request->solicitud_id,
                                    'campo_id' => $campo->id,
                                    'valor' => $fecha_inicio_informe
                                ]);
                            }

                            if ($campo->name == 'fecha_maxima_informe') {
                                ValorCampo::create([
                                    'solicitud_id' => $request->solicitud_id,
                                    'campo_id' => $campo->id,
                                    'valor' => $fecha_maxima_informe
                                ]);
                            }
                        }

                        // Crear acta de aprobación de fase 3:
                        $acta = Acta::create([
                            'numero' => $request->input('nro_acta_fase3'),
                            'fecha' => $request->input('fecha_acta_fase3'),
                            'descripcion' => 'Aprobación de la propuesta',
                            'proyecto_id' => $request->input('solicitud_id'),
                        ]);

                        $acta->save();

                        $request->merge(['acta' => $acta]);

                        // Enviar correo a estudiantes, director y evaluador notificando la aprobación:
                        self::sendEmailFase3Response($request, 'comite');
                    } else {
                        // llenar campo de aprobacion de evaluador:
                        $fase_3 = self::getType('fase_3');
                        $campos_fase_3 = Campo::query()->where('tipo_solicitud_id', '=', $fase_3->id)->where('deleted_at', '=', NULL)->get();

                        foreach ($campos_fase_3 as $campo) {
                            if ($campo->name == 'submited_fase3_evaluador') {
                                ValorCampo::create([
                                    'solicitud_id' => $request->solicitud_id,
                                    'campo_id' => $campo->id,
                                    'valor' => 'true'
                                ]);
                            }
                        }

                        // Enviar correo a estudiantes, director indicando la aprobación.
                        self::sendEmailFase3Response($request, 'evaluador');
                    }
                } else {
                    // Vaciar campos FASE 3
                    $type = self::getType('fase_3');
                    $campos_valores_fase3 = self::findCamposByTipoSolicitud($campos, $type->id);

                    foreach ($campos_valores_fase3 as $item) {
                        ValorCampo::where('solicitud_id', '=', $solicitud->id)
                            ->where('campo_id', '=', $item['campo']['id'])
                            ->firstOrFail()
                            ->delete();
                    }

                    // Vaciar campos FASE 2
                    $type = self::getType('fase_2');
                    $campos_valores_fase2 = self::findCamposByTipoSolicitud($campos, $type->id);

                    foreach ($campos_valores_fase2 as $item) {
                        ValorCampo::where('solicitud_id', '=', $solicitud->id)
                            ->where('campo_id', '=', $item['campo']['id'])
                            ->firstOrFail()
                            ->delete();
                    }

                    // Actualizar estado a FASE 2:
                    $solicitud->update(['estado' => 'Fase 2']);

                    // Enviar correo con rechazo:
                    $nombreUnico = null;

                    if ($request->hasFile('doc_respuesta_fase3')) {
                        $nombresArchivos = [];
                        foreach ($request->file('doc_respuesta_fase3') as $archivo) {
                            $extension = $archivo->getClientOriginalExtension();
                            $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
                            $archivo->storeAs('documentos_proyectos/proyecto-00' . $request->solicitud_id, $nombreUnico, 'public');
                            $nombresArchivos[] = $nombreUnico;
                        }
                    }

                    $adjunto = public_path("storage/documentos_proyectos/proyecto-00{$request->solicitud_id}/" . $nombreUnico);

                    if ($request->remitente == 1) {
                        // Generar acta de rechazo de fase 3:
                        $acta = Acta::create([
                            'numero' => $request->input('nro_acta_fase3'),
                            'fecha' => $request->input('fecha_acta_fase3'),
                            'descripcion' => 'Aplazamiento de la propuesta',
                            'proyecto_id' => $request->input('solicitud_id'),
                        ]);

                        $acta->save();

                        $request->merge(['acta' => $acta]);

                        // Enviar correo a los estudiantes, director y evaluador con archivo adjunto para volver a enviar propuesta
                        self::sendEmailFase3Response($request, 'comite', $adjunto);
                    } else {
                        // Enviar correo a los estudiantes y director con archivo adjunto para volver a enviar propuesta
                        self::sendEmailFase3Response($request, 'evaluador', $adjunto);
                    }
                }
            });
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function sendEmailFase3Response($request, $caso, $adjunto = null)
    {
        try {
            $cuerpo_correo = [];
            $correos_destinatarios = [];
            $asunto_correo = 'PROYECTO DE GRADO - FASE 3';
            $tipo_correo = 'respuesta_fase_3';
            $comentarios = $request->respuesta_fase3;
            $esRespuesta = true;

            $proyecto_id = $request->solicitud_id;
            $estado = $request->estado_fase3;

            $proyecto = Solicitud::query()->where('id', '=', $proyecto_id)->first();
            $campos_proyecto = $proyecto->camposConValores();
            $titulo = self::getTituloProyecto($proyecto);
            $codigo_modalidad = self::findCampoByName($campos_proyecto, 'codigo_modalidad');

            $periodo_proyecto = self::findCampoByName($campos_proyecto, 'periodo');
            $fechas_proyecto = self::getFechasByPeriodo($periodo_proyecto);

            $cuerpo_correo['titulo'] = $titulo;
            $cuerpo_correo['estado'] = $estado;
            $cuerpo_correo['codigo_modalidad'] = $codigo_modalidad;
            $cuerpo_correo['fecha_aprobacion'] = $fechas_proyecto['fecha_aprobacion_propuesta'];
            $cuerpo_correo['fecha_maxima_informe'] = $request->fecha_maxima_informe;
            $cuerpo_correo['fecha_minima_informe'] = $request->fecha_minima_informe;
            $cuerpo_correo['adjunto'] = $adjunto;
            $cuerpo_correo['remitente'] = $request->remitente;

            $cuerpo_correo['nro_acta'] = $request->acta->numero ?? null;
            $cuerpo_correo['fecha_acta'] = $request->acta->fecha ?? null;

            $cuerpo_correo['destinatario'] = 'estudiante';

            $integrante_1 = User::query()->where('id', self::findCampoByName($campos_proyecto, 'id_integrante_1'))->first();
            $integrante_2 = User::query()->where('id', self::findCampoByName($campos_proyecto, 'id_integrante_2'))->first();
            $integrante_3 = User::query()->where('id', self::findCampoByName($campos_proyecto, 'id_integrante_3'))->first();

            if (isset($integrante_1)) {
                $correos_destinatarios[] = $integrante_1->email;
            }

            if (isset($integrante_2)) {
                $correos_destinatarios[] = $integrante_2->email;
            }

            if (isset($integrante_3)) {
                $correos_destinatarios[] = $integrante_3->email;
            }

            // Enviar correo a estudiantes:
            Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));

            // Enviar correo a director
            $director = User::query()->where('id', self::findCampoByName($campos_proyecto, 'director_id'))->first();
            $correos_destinatarios = [];
            $correos_destinatarios[] = $director->email;
            $cuerpo_correo['destinatario'] = 'director';
            Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));

            if ($caso == 'comite') {
                // Enviar correo a evaluador
                $evaluador = User::query()->where('id', self::findCampoByName($campos_proyecto, 'evaluador_id'))->first();
                $correos_destinatarios = [];
                $correos_destinatarios[] = $evaluador->email;
                $cuerpo_correo['destinatario'] = 'evaluador';
                Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    // FASE 4:

    public function fase4(Request $request)
    {
        $validator = null;

        try {
            $validator = Validator::make($request->all(), [
                'solicitud_id' => 'required',
                'doc_informe' => 'required',
                'doc_informe.*' => 'required|max:15360',
                'doc_rejilla' => 'required',
                'doc_rejilla.*' => 'required|max:15360',
                'submited_fase4' => 'required',
            ], [
                'doc_informe.required' => 'El documento de la propuesta es obligatorio.',
                'doc_rejilla.required' => 'El documento de la rejilla es obligatorio.'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            DB::transaction(function () use ($request) {
                $type = self::getType('fase_4');
                $campos = Campo::query()->where('tipo_solicitud_id', '=', $type->id)->where('deleted_at', '=', NULL)->get();

                foreach ($campos as $campo) {
                    if ($request->has($campo->name) && $campo->name != "doc_informe" && $campo->name != "doc_rejilla") {
                        ValorCampo::create([
                            'solicitud_id' => $request->solicitud_id,
                            'campo_id' => $campo->id,
                            'valor' => $request->input($campo->name)
                        ]);
                    }
                }

                $campo_recordatorio_fase4 = Campo::where('name', '=', 'recordatorio_fase4')->firstOrFail();
                $fecha_recordatorio = Carbon::now()->addDays(env('DIAS_RECORDATORIO'))->format('Y-m-d');

                ValorCampo::updateOrCreate(
                    [
                        'solicitud_id' => $request->input('solicitud_id'),
                        'campo_id' => $campo_recordatorio_fase4->id,
                    ],
                    [
                        'valor' => $fecha_recordatorio
                    ]
                );

                $campo_doc_informe = Campo::where('name', '=', 'doc_informe')->firstOrFail();
                $campo_doc_rejilla = Campo::where('name', '=', 'doc_rejilla')->firstOrFail();

                if ($request->hasFile('doc_informe')) {
                    $nombresArchivos = [];
                    foreach ($request->file('doc_informe') as $archivo) {
                        $extension = $archivo->getClientOriginalExtension();
                        $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
                        $archivo->storeAs('documentos_proyectos/proyecto-00' . $request->solicitud_id, $nombreUnico, 'public');
                        $nombresArchivos[] = $nombreUnico;
                    }

                    ValorCampo::updateOrCreate(
                        [
                            'solicitud_id' => $request->input('solicitud_id'),
                            'campo_id' => $campo_doc_informe->id
                        ],
                        [
                            'valor' => json_encode($nombresArchivos)
                        ]
                    );
                }

                if ($request->hasFile('doc_rejilla')) {
                    $nombresArchivos = [];
                    foreach ($request->file('doc_rejilla') as $archivo) {
                        $extension = $archivo->getClientOriginalExtension();
                        $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
                        $archivo->storeAs('documentos_proyectos/proyecto-00' . $request->solicitud_id, $nombreUnico, 'public');
                        $nombresArchivos[] = $nombreUnico;
                    }

                    ValorCampo::updateOrCreate(
                        [
                            'solicitud_id' => $request->input('solicitud_id'),
                            'campo_id' => $campo_doc_rejilla->id
                        ],
                        [
                            'valor' => json_encode($nombresArchivos)
                        ]
                    );
                }

                // Enviar correo a estudiantes, director y comite
                self::sendEmailFase4($request->solicitud_id);
            });
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function reply_fase4(Request $request)
    {
        $validator = null;

        try {
            if (isset($request->estado_fase4)) {
                if ($request->estado_fase4 === 'Aprobado') {
                    $validator = Validator::make($request->all(), [
                        'estado_fase4' => 'required',
                        'doc_turnitin_fase4' => 'required',
                        'doc_turnitin_fase4.*' => 'required|max:15360',
                        'doc_rejilla_fase4' => 'required',
                        'doc_rejilla_fase4.*' => 'required|max:15360',
                        'respuesta_fase4' => 'required',
                        'solicitud_id' => 'required',
                    ], [
                        'estado_fase4.required' => 'El estado es obligatorio.',
                        'doc_turnitin_fase4.required' => 'El informe de plagio es obligatorio.',
                        'doc_rejilla_fase4.required' => 'El documento de la rejilla es obligatorio.',
                        'respuesta_fase4.required' => 'La descripción de la respuesta es obligatoria.'
                    ]);
                } else {
                    $validator = Validator::make($request->all(), [
                        'estado_fase4' => 'required',
                        'doc_respuesta_fase4' => 'required',
                        'doc_respuesta_fase4.*' => 'required|max:15360',
                        'respuesta_fase4' => 'required',
                        'solicitud_id' => 'required',
                    ], [
                        'estado_fase4.required' => 'El estado es obligatorio.',
                        'doc_respuesta_fase4.required' => 'El informe con comentarios es obligatorio.',
                        'respuesta_fase4.required' => 'La descripción de la respuesta es obligatoria.'
                    ]);
                }
            } else {
                $validator = Validator::make($request->all(), [
                    'estado_fase4' => 'required',
                    'doc_turnitin_fase4' => 'required',
                    'doc_turnitin_fase4.*' => 'required|max:15360',
                    'respuesta_fase4' => 'required',
                    'solicitud_id' => 'required',
                ], [
                    'estado_fase4.required' => 'El estado es obligatorio.',
                    'doc_turnitin_fase4.required' => 'El informe de plagio es obligatorio.',
                    'respuesta_fase4.required' => 'La descripción de la respuesta es obligatoria.'
                ]);
            }

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            DB::transaction(function () use ($request) {
                $solicitud = Solicitud::query()
                    ->where('id', '=', $request->solicitud_id)
                    ->firstOrFail();

                $campos = $solicitud->camposConValores();

                if ($request->estado_fase4 === 'Aprobado') {
                    // Se llenan los campos de la fase 5 para que el evaluador empiece a revisar

                    // 1. llenar campos de fase 5:
                    $fase_5 = self::getType('fase_5');
                    $campos_fase_5 = Campo::query()->where('tipo_solicitud_id', '=', $fase_5->id)->where('deleted_at', '=', NULL)->get();

                    foreach ($campos_fase_5 as $campo) {
                        if ($campo->name == 'submited_fase5_director') {
                            ValorCampo::create([
                                'solicitud_id' => $request->solicitud_id,
                                'campo_id' => $campo->id,
                                'valor' => 'true'
                            ]);
                        }
                    }

                    // 2. Verificar si existe documento de rejilla y guardarlo
                    $campo_doc_rejilla = Campo::where('name', '=', 'doc_rejilla')->firstOrFail();

                    if ($request->hasFile('doc_rejilla_fase4')) {
                        $nombresArchivos = [];
                        foreach ($request->file('doc_rejilla_fase4') as $archivo) {
                            $extension = $archivo->getClientOriginalExtension();
                            $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
                            $archivo->storeAs('documentos_proyectos/proyecto-00' . $request->input('solicitud_id'), $nombreUnico, 'public');
                            $nombresArchivos[] = $nombreUnico;
                        }

                        ValorCampo::updateOrCreate(
                            [
                                'solicitud_id' => $request->input('solicitud_id'),
                                'campo_id' => $campo_doc_rejilla->id
                            ],
                            [
                                'valor' => json_encode($nombresArchivos)
                            ]
                        );
                    }

                    // 3. verificar si existe documento de turniting y guardarlo
                    $campo_doc_turnitin = Campo::where('name', '=', 'doc_turnitin_informe')->firstOrFail();

                    if ($request->hasFile('doc_turnitin_fase4')) {
                        $nombresArchivos = [];
                        foreach ($request->file('doc_turnitin_fase4') as $archivo) {
                            $extension = $archivo->getClientOriginalExtension();
                            $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
                            $archivo->storeAs('documentos_proyectos/proyecto-00' . $request->input('solicitud_id'), $nombreUnico, 'public');
                            $nombresArchivos[] = $nombreUnico;
                        }

                        ValorCampo::updateOrCreate(
                            [
                                'solicitud_id' => $request->input('solicitud_id'),
                                'campo_id' => $campo_doc_turnitin->id,
                            ],
                            [
                                'valor' => json_encode($nombresArchivos)
                            ]
                        );
                    }

                    // 4. Asignar fecha de recordatorio para fase 5
                    $campo_recordatorio_fase5 = Campo::where('name', '=', 'recordatorio_fase5')->firstOrFail();
                    $fecha_recordatorio = Carbon::now()->addDays(env('DIAS_RECORDATORIO'))->format('Y-m-d');

                    ValorCampo::updateOrCreate(
                        [
                            'solicitud_id' => $request->input('solicitud_id'),
                            'campo_id' => $campo_recordatorio_fase5->id,
                        ],
                        [
                            'valor' => $fecha_recordatorio
                        ]
                    );

                    // Se actualiza el estado de la solicitud a FASE 5
                    $solicitud->update(['estado' => 'Fase 5']);

                    // Se envia correo a estudiantes y evaluador para que lo revise
                    self::sendEmailFase4Response($request);
                } else {
                    // Se vacian los campos de la fase 4
                    $type = self::getType('fase_4');
                    $campos_valores_fase4 = self::findCamposByTipoSolicitud($campos, $type->id);
                    $campo_doc_turnitin = Campo::where('name', '=', 'doc_turnitin')->firstOrFail();

                    foreach ($campos_valores_fase4 as $item) {
                        if ($item['campo']['name'] != 'fecha_inicio_informe' && $item['campo']['name'] != 'fecha_maxima_informe' && $item['campo']['name'] != 'doc_icfes' && $item['campo']['name'] != 'submited_icfes' && $item['campo']['name'] != 'beneficiarios_icfes') {
                            ValorCampo::where('solicitud_id', '=', $solicitud->id)
                                ->where('campo_id', '=', $item['campo']['id'])
                                ->firstOrFail()
                                ->delete();
                        }
                    }

                    // 1. Verificar si existe documento de turniting y guardarlo
                    $campo_doc_turnitin = Campo::where('name', '=', 'doc_turnitin_informe')->firstOrFail();

                    if ($request->hasFile('doc_turnitin_fase4')) {
                        $nombresArchivos = [];
                        foreach ($request->file('doc_turnitin_fase4') as $archivo) {
                            $extension = $archivo->getClientOriginalExtension();
                            $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
                            $archivo->storeAs('documentos_proyectos/proyecto-00' . $request->input('solicitud_id'), $nombreUnico, 'public');
                            $nombresArchivos[] = $nombreUnico;
                        }

                        ValorCampo::updateOrCreate(
                            [
                                'solicitud_id' => $request->input('solicitud_id'),
                                'campo_id' => $campo_doc_turnitin->id,
                            ],
                            [
                                'valor' => json_encode($nombresArchivos)
                            ]
                        );
                    }

                    // 2. Logica del archivo adjunto con comentarios del director
                    $nombreUnico = null;

                    if ($request->hasFile('doc_respuesta_fase4')) {
                        $nombresArchivos = [];
                        foreach ($request->file('doc_respuesta_fase4') as $archivo) {
                            $extension = $archivo->getClientOriginalExtension();
                            $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
                            $archivo->storeAs('documentos_proyectos/proyecto-00' . $request->solicitud_id, $nombreUnico, 'public');
                            $nombresArchivos[] = $nombreUnico;
                        }
                    }

                    $adjunto = public_path("storage/documentos_proyectos/proyecto-00{$request->solicitud_id}/" . $nombreUnico);

                    // 3. Enviar correo a los estudiantes con archivo adjunto para volver a enviar informe
                    self::sendEmailFase4Response($request, $adjunto);
                }
            });
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function sendEmailFase4($proyecto_id)
    {
        try {
            $cuerpo_correo = [];
            $correos_destinatarios = [];
            $asunto_correo = 'PROYECTO DE GRADO - FASE 4';
            $tipo_correo = 'fase_4';
            $comentarios = null;
            $esRespuesta = false;

            $proyecto = Solicitud::query()->where('id', '=', $proyecto_id)->first();
            $campos_proyecto = $proyecto->camposConValores();
            $titulo = self::getTituloProyecto($proyecto);

            $cuerpo_correo['titulo'] = $titulo;
            $cuerpo_correo['fecha_maxima_informe'] = self::findCampoByName($campos_proyecto, 'fecha_maxima_informe');

            $integrante_1 = User::query()->where('id', self::findCampoByName($campos_proyecto, 'id_integrante_1'))->first();
            $integrante_2 = User::query()->where('id', self::findCampoByName($campos_proyecto, 'id_integrante_2'))->first();
            $integrante_3 = User::query()->where('id', self::findCampoByName($campos_proyecto, 'id_integrante_3'))->first();

            $director = User::query()->where('id', self::findCampoByName($campos_proyecto, 'director_id'))->first();

            if (isset($integrante_1)) {
                $correos_destinatarios[] = $integrante_1->email;
            }

            if (isset($integrante_2)) {
                $correos_destinatarios[] = $integrante_2->email;
            }

            if (isset($integrante_3)) {
                $correos_destinatarios[] = $integrante_3->email;
            }

            $correos_destinatarios[] = $director->email;

            // se envia correo
            Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function sendEmailFase4Response($request, $adjunto = null)
    {
        try {
            $cuerpo_correo = [];
            $correos_destinatarios = [];
            $asunto_correo = 'PROYECTO DE GRADO - FASE 4';
            $tipo_correo = 'respuesta_fase_4';
            $comentarios = $request->respuesta_fase4;
            $esRespuesta = true;

            $proyecto_id = $request->solicitud_id;
            $estado = $request->estado_fase4;

            $proyecto = Solicitud::query()->where('id', '=', $proyecto_id)->first();
            $campos_proyecto = $proyecto->camposConValores();
            $titulo = self::getTituloProyecto($proyecto);

            $cuerpo_correo['titulo'] = $titulo;
            $cuerpo_correo['estado'] = $estado;
            $cuerpo_correo['fecha_maxima_informe'] = self::findCampoByName($campos_proyecto, 'fecha_maxima_informe');
            $cuerpo_correo['adjunto'] = $adjunto;

            $integrante_1 = User::query()->where('id', self::findCampoByName($campos_proyecto, 'id_integrante_1'))->first();
            $integrante_2 = User::query()->where('id', self::findCampoByName($campos_proyecto, 'id_integrante_2'))->first();
            $integrante_3 = User::query()->where('id', self::findCampoByName($campos_proyecto, 'id_integrante_3'))->first();

            if (isset($integrante_1)) {
                $correos_destinatarios[] = $integrante_1->email;
            }

            if (isset($integrante_2)) {
                $correos_destinatarios[] = $integrante_2->email;
            }

            if (isset($integrante_3)) {
                $correos_destinatarios[] = $integrante_3->email;
            }

            // Se envia correo a estudiantes
            Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));

            if ($estado === 'Aprobado') {
                // Se envia correo a evaluador para notificarle la revision en FASE 4
                $correos_destinatarios = [];
                $evaluador = User::query()->where('id', self::findCampoByName($campos_proyecto, 'evaluador_id'))->first();
                $correos_destinatarios[] = $evaluador->email;

                // Se envia correo a evaluador
                Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    // FASE 5:

    public function reply_fase5(Request $request)
    {
        $validator = null;

        try {
            if (isset($request->estado_fase5)) {
                if ($request->estado_fase5 === 'Aprobado') {
                    $validator = Validator::make($request->all(), [
                        'estado_fase5' => 'required',
                        'doc_rejilla_fase5' => 'required',
                        'doc_rejilla_fase5.*' => 'required|max:15360',
                        'respuesta_fase5' => 'required',
                        'solicitud_id' => 'required',
                        'remitente' => 'required',
                    ], [
                        'estado_fase5.required' => 'El estado es obligatorio.',
                        'doc_rejilla_fase5.required' => 'El documento de la rejilla es obligatorio.',
                        'respuesta_fase5.required' => 'La descripción de la respuesta es obligatoria.'
                    ]);
                } else {
                    $validator = Validator::make($request->all(), [
                        'estado_fase5' => 'required',
                        'doc_respuesta_fase5' => 'required',
                        'doc_respuesta_fase5.*' => 'required|max:15360',
                        'respuesta_fase5' => 'required',
                        'solicitud_id' => 'required',
                        'remitente' => 'required',
                    ], [
                        'estado_fase5.required' => 'El estado es obligatorio.',
                        'doc_respuesta_fase5.required' => 'El documento de informe es obligatorio.',
                        'respuesta_fase5.required' => 'La descripción de la respuesta es obligatoria.'
                    ]);
                }
            } else {
                $validator = Validator::make($request->all(), [
                    'estado_fase5' => 'required',
                    'doc_rejilla_fase5' => 'required',
                    'doc_rejilla_fase5.*' => 'required|max:15360',
                    'respuesta_fase5' => 'required',
                    'solicitud_id' => 'required',
                    'remitente' => 'required',
                ], [
                    'estado_fase5.required' => 'El estado es obligatorio.',
                    'doc_rejilla_fase5.required' => 'El documento de la rejilla es obligatorio.',
                    'respuesta_fase5.required' => 'La descripción de la respuesta es obligatoria.'
                ]);
            }

            if (auth()->user()->hasRole(['admin', 'super_admin'])) {
                $validator->after(function ($validator) use ($request) {
                    // Agregar validaciones de nro_acta_fase5 (required|numeric) y fecha_acta_fase5 (required|date)
                    if (empty($request->nro_acta_fase5)) {
                        $validator->errors()->add('nro_acta_fase5', 'El número de acta es obligatorio.');
                    }

                    if (!is_numeric($request->nro_acta_fase5)) {
                        $validator->errors()->add('nro_acta_fase5', 'El número de acta debe ser numérico.');
                    }

                    if (empty($request->fecha_acta_fase5)) {
                        $validator->errors()->add('fecha_acta_fase5', 'La fecha de acta es obligatoria.');
                    }

                    if (!strtotime($request->fecha_acta_fase5)) {
                        $validator->errors()->add('fecha_acta_fase5', 'La fecha de acta debe ser una fecha válida.');
                    }
                });
            }

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            DB::transaction(function () use ($request) {
                $solicitud = Solicitud::query()
                    ->where('id', '=', $request->solicitud_id)
                    ->firstOrFail();

                $campos = $solicitud->camposConValores();

                // Actualizar documento de la rejilla:
                $campo_doc_rejilla = Campo::where('name', '=', 'doc_rejilla')->firstOrFail();

                if ($request->hasFile('doc_rejilla_fase5')) {
                    $nombresArchivos = [];
                    foreach ($request->file('doc_rejilla_fase5') as $archivo) {
                        $extension = $archivo->getClientOriginalExtension();
                        $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
                        $archivo->storeAs('documentos_proyectos/proyecto-00' . $request->solicitud_id, $nombreUnico, 'public');
                        $nombresArchivos[] = $nombreUnico;
                    }

                    ValorCampo::updateOrCreate(
                        [
                            'solicitud_id' => $request->input('solicitud_id'),
                            'campo_id' => $campo_doc_rejilla->id
                        ],
                        [
                            'valor' => json_encode($nombresArchivos)
                        ]
                    );
                }

                if ($request->estado_fase5 === 'Aprobado') {
                    if ($request->remitente == 1) {
                        // 1. Actualizar estado a Finalizado:
                        $solicitud->update(['estado' => 'Finalizado']);

                        // Generar acta de aprobación de fase 5:
                        $acta = Acta::create([
                            'numero' => $request->input('nro_acta_fase5'),
                            'fecha' => $request->input('fecha_acta_fase5'),
                            'descripcion' => 'Aprobación del informe final',
                            'proyecto_id' => $request->input('solicitud_id'),
                        ]);

                        $acta->save();

                        $request->merge(['acta' => $acta,]);

                        // 2. Enviar correo a estudiantes, director y evaluador notificando la aprobación:
                        self::sendEmailFase5Response($request, 'comite');
                    } else {
                        // 1. llenar campo de aprobacion de evaluador:
                        $fase_5 = self::getType('fase_5');
                        $campos_fase_5 = Campo::query()->where('tipo_solicitud_id', '=', $fase_5->id)->where('deleted_at', '=', NULL)->get();

                        foreach ($campos_fase_5 as $campo) {
                            if ($campo->name == 'submited_fase5_evaluador') {
                                ValorCampo::create([
                                    'solicitud_id' => $request->solicitud_id,
                                    'campo_id' => $campo->id,
                                    'valor' => 'true'
                                ]);
                            }
                        }

                        // 2. Enviar correo a estudiantes y director indicando la aprobación:
                        self::sendEmailFase5Response($request, 'evaluador');
                    }
                } else {
                    // 1. Vaciar campos FASE 5:
                    $type = self::getType('fase_5');
                    $campos_valores_fase5 = self::findCamposByTipoSolicitud($campos, $type->id);

                    foreach ($campos_valores_fase5 as $item) {
                        ValorCampo::where('solicitud_id', '=', $solicitud->id)
                            ->where('campo_id', '=', $item['campo']['id'])
                            ->firstOrFail()
                            ->delete();
                    }

                    // 2. Vaciar campos FASE 4:
                    $type = self::getType('fase_4');
                    $campos_valores_fase4 = self::findCamposByTipoSolicitud($campos, $type->id);

                    foreach ($campos_valores_fase4 as $item) {
                        if ($item['campo']['name'] != 'fecha_inicio_informe' && $item['campo']['name'] != 'fecha_maxima_informe' && $item['campo']['name'] != 'doc_rejilla' && $item['campo']['name'] != 'doc_icfes' && $item['campo']['name'] != 'submited_icfes' && $item['campo']['name'] != 'beneficiarios_icfes') {
                            ValorCampo::where('solicitud_id', '=', $solicitud->id)
                                ->where('campo_id', '=', $item['campo']['id'])
                                ->firstOrFail()
                                ->delete();
                        }
                    }

                    // 3. Actualizar estado a FASE 4:
                    $solicitud->update(['estado' => 'Fase 4']);

                    // 4. Enviar correo con rechazo:
                    $nombreUnico = null;

                    if ($request->hasFile('doc_respuesta_fase5')) {
                        $nombresArchivos = [];
                        foreach ($request->file('doc_respuesta_fase5') as $archivo) {
                            $extension = $archivo->getClientOriginalExtension();
                            $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
                            $archivo->storeAs('documentos_proyectos/proyecto-00' . $request->solicitud_id, $nombreUnico, 'public');
                            $nombresArchivos[] = $nombreUnico;
                        }
                    }

                    $adjunto = public_path("storage/documentos_proyectos/proyecto-00{$request->solicitud_id}/" . $nombreUnico);

                    if ($request->remitente == 1) {
                        $acta = Acta::create([
                            'numero' => $request->input('nro_acta_fase5'),
                            'fecha' => $request->input('fecha_acta_fase5'),
                            'descripcion' => 'Rechazo del informe final',
                            'proyecto_id' => $request->input('solicitud_id'),
                        ]);

                        $acta->save();

                        $request->merge(['acta' => $acta,]);

                        // 5. Enviar correo a los estudiantes, director y evaluador con archivo adjunto para volver a enviar propuesta
                        self::sendEmailFase5Response($request, 'comite', $adjunto);
                    } else {
                        // 5. Enviar correo a los estudiantes y director con archivo adjunto para volver a enviar propuesta
                        self::sendEmailFase5Response($request, 'evaluador', $adjunto);
                    }
                }
            });
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo'], 500);
        }
    }

    public function sendEmailFase5Response($request, $caso, $adjunto = null)
    {
        try {
            $cuerpo_correo = [];
            $correos_destinatarios = [];
            $asunto_correo = 'PROYECTO DE GRADO - FASE 5';
            $tipo_correo = 'respuesta_fase_5';
            $comentarios = $request->respuesta_fase5;
            $esRespuesta = true;

            $proyecto_id = $request->solicitud_id;
            $estado = $request->estado_fase5;

            $proyecto = Solicitud::query()->where('id', '=', $proyecto_id)->first();
            $campos_proyecto = $proyecto->camposConValores();
            $titulo = self::getTituloProyecto($proyecto);

            $cuerpo_correo['titulo'] = $titulo;
            $cuerpo_correo['estado'] = $estado;
            $cuerpo_correo['fecha_maxima_informe'] = self::findCampoByName($campos_proyecto, 'fecha_maxima_informe');
            $cuerpo_correo['adjunto'] = $adjunto;
            $cuerpo_correo['remitente'] = $request->remitente;

            $cuerpo_correo['nro_acta'] = $request->acta->numero ?? null;
            $cuerpo_correo['fecha_acta'] = $request->acta->fecha ?? null;

            $cuerpo_correo['destinatario'] = 'estudiante';

            $integrante_1 = User::query()->where('id', self::findCampoByName($campos_proyecto, 'id_integrante_1'))->first();
            $integrante_2 = User::query()->where('id', self::findCampoByName($campos_proyecto, 'id_integrante_2'))->first();
            $integrante_3 = User::query()->where('id', self::findCampoByName($campos_proyecto, 'id_integrante_3'))->first();

            if (isset($integrante_1)) {
                $correos_destinatarios[] = $integrante_1->email;
            }

            if (isset($integrante_2)) {
                $correos_destinatarios[] = $integrante_2->email;
            }

            if (isset($integrante_3)) {
                $correos_destinatarios[] = $integrante_3->email;
            }

            // Enviar correo a estudiantes:
            Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));

            // Enviar correo a director
            $director = User::query()->where('id', self::findCampoByName($campos_proyecto, 'director_id'))->first();
            $correos_destinatarios = [];
            $correos_destinatarios[] = $director->email;
            $cuerpo_correo['destinatario'] = 'director';
            Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));

            if ($caso == 'comite') {
                // Enviar correo a evaluador
                $evaluador = User::query()->where('id', self::findCampoByName($campos_proyecto, 'evaluador_id'))->first();
                $correos_destinatarios = [];
                $correos_destinatarios[] = $evaluador->email;
                $cuerpo_correo['destinatario'] = 'evaluador';
                Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo'], 500);
        }
    }
}
