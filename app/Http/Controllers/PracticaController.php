<?php
namespace App\Http\Controllers;

use App\Mail\PracticasMail;
use App\Models\ActaPractica;
use App\Models\Campo;
use App\Models\Practica;
use App\Models\PracticaValorCampo;
use App\Models\TipoSolicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PracticaController extends Controller
{
    public function index()
    {

        $tipo = TipoSolicitud::where('nombre', 'practicas_fase_0')->first();

        $campos = Campo::where('tipo_solicitud_id', $tipo->id)->get();

        return view('practicas.index', compact('campos'));
    }

    public function getData(Request $request)
{
    // ✅ CORREGIDO: Mostrar TODAS las prácticas, no solo tipo_solicitud_id = 9
    $practicas = Practica::with('user')
        ->where('tipo_solicitud_id', '>=', 9);  // Todas las fases de prácticas (9, 10, 11, 12, 13, 14)

    if (auth()->user()->hasRole('estudiante')) {
        // Mostrar prácticas donde:
        // 1. El usuario es el creador (user_id)
        // 2. O el usuario es el segundo integrante (id_integrante_2)
        $practicas->where(function ($q) {
            $q->where('user_id', auth()->id())
                ->orWhereHas('valoresCampos', function ($vc) {
                    $vc->whereHas('campo', function ($c) {
                        $c->where('name', 'id_integrante_2');
                    })->where('valor', auth()->id());
                });
        });
        $practicas->orderBy('id', 'desc');
    }

    if (auth()->user()->hasRole(['super_admin', 'admin', 'coordinador'])) {
        $filter = $request->input('filter');
        $practicas->orderBy('id', 'desc');

        switch ($filter) {
            case 'pendientes_comite':
                // Prácticas donde el responsable es COMITÉ y están pendientes de acción
                $practicas->whereIn('estado', ['Pendiente', 'Fase 1', 'Fase 5']);
                break;
            case 'pendientes_director':
                $practicas->where('estado', 'Fase 3');
                break;
            case 'pendientes_evaluador':
                $practicas->where('estado', 'Fase 4');
                break;
            case 'propuestas_pendientes':
                $practicas->where('estado', 'Fase 1');
                break;
            case 'informes_pendientes':
                $practicas->where('estado', 'Fase 4');
                break;
        }
    }

    // BÚSQUEDA AVANZADA (igual que antes)
    if ($request->has('search') && $search = $request->input('search.value')) {
        $practicas->where(function ($q) use ($search) {
            if (preg_match('/PRA-(\d+)/i', $search, $matches)) {
                $idNumero = intval($matches[1]);
                $q->orWhere('id', $idNumero);
            }
            if (is_numeric($search)) {
                $q->orWhere('id', $search);
            }
            $q->orWhere('estado', 'LIKE', "%{$search}%");
            $q->orWhereHas('user', function ($uq) use ($search) {
                $uq->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('nro_documento', 'LIKE', "%{$search}%")
                    ->orWhere('nro_celular', 'LIKE', "%{$search}%");
            });
            $q->orWhereHas('user.nivel', function ($nq) use ($search) {
                $nq->where('nombre', 'LIKE', "%{$search}%");
            });
            $q->orWhereHas('valoresCampos', function ($vcq) use ($search) {
                $vcq->whereHas('campo', function ($cq) use ($search) {
                    $cq->whereIn('name', ['titulo', 'nombre_empresa']);
                })->where('valor', 'LIKE', "%{$search}%");
            });
        });
    }

    return DataTables::of($practicas)
        ->addColumn('formatted_id', function ($p) {
            return 'PRA-' . str_pad($p->id, 5, '0', STR_PAD_LEFT);
        })
        ->addColumn('descripcion', function ($p) {
            return 'Solicitud de prácticas empresariales';
        })
        ->addColumn('estado', function ($p) {
            $return_html = '<div class="flex gap-2 flex-wrap items-center justify-center">';

            if ($p->estado === 'Rechazada') {
                $badge = "<span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-red-100 text-red-800 border border-red-300'>Rechazada</span>";
                return $return_html . $badge . "</div>";
            }

            $htmlEstado = '';

            if ($p->estado === 'Pendiente') {
                $htmlEstado = "<span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300'>Pendiente</span>
                               <span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300'>Comité</span>";
            } 
            elseif ($p->estado === 'Fase 1') {
                // ✅ CORREGIDO: Verificar quién debe actuar
                $submited = $p->valoresCampos->where('campo.name', 'submited_fase1')->first();
                $yaEnvio = $submited && $submited->valor === 'true';
                
                if ($yaEnvio) {
                    // El estudiante ya envió, ahora le toca al COMITÉ
                    $htmlEstado = "<span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-uts-300 border border-uts-500'>Fase 1</span>
                                   <span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300'>Comité</span>";
                } else {
                    // El estudiante aún no ha enviado
                    $htmlEstado = "<span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-uts-300 border border-uts-500'>Fase 1</span>
                                   <span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300'>Estudiante</span>";
                }
            } 
            elseif ($p->estado === 'Fase 2') {
                $htmlEstado = "<span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-uts-300 border border-uts-500'>Fase 2</span>
                               <span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300'>Estudiante</span>";
            } 
            elseif ($p->estado === 'Fase 3') {
                $htmlEstado = "<span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-uts-300 border border-uts-500'>Fase 3</span>
                               <span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300'>Estudiante</span>";
            } 
            elseif ($p->estado === 'Fase 4') {
                $htmlEstado = "<span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-uts-300 border border-uts-500'>Fase 4</span>
                               <span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300'>Estudiante</span>";
            } 
            elseif ($p->estado === 'Fase 5') {
                $htmlEstado = "<span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-uts-300 border border-uts-500'>Fase 5</span>
                               <span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300'>Comité</span>";
            } 
            elseif ($p->estado === 'Finalizado') {
                $htmlEstado = "<span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-green-100 text-green-800 border border-green-300'>Finalizado</span>";
            }

            if ($p->deshabilitado && $p->estado !== 'Rechazada') {
                $deshabilitadoBadge = "<span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-red-100 text-red-800 border border-red-300'>Deshabilitado</span>";
                return $return_html . $htmlEstado . ' ' . $deshabilitadoBadge . "</div>";
            }

            return $return_html . $htmlEstado . "</div>";
        })
        ->addColumn('acciones', function ($p) {
            // Tu código de acciones existente...
            $user = auth()->user();
            $buttons = '<div class="flex items-center justify-center gap-1">';

            // Botón Ver
            $buttons .= '<button onclick="openDetailsModal(this, ' . $p->id . ')" class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white w-10 h-10 rounded-lg relative inline-flex items-center justify-center">
                <i class="fa-regular fa-eye"></i>
            </button>';

            // Botón Responder (solo para admin/comité en estado Pendiente o Fase 1 con submited=true)
            if ($user->hasRole(['super_admin', 'admin', 'coordinador'])) {
                if ($p->estado === 'Pendiente' && !$p->deshabilitado) {
                    $buttons .= '<button onclick="openResponderSolicitudModal(' . $p->id . ')"
                        class="btn-action shadow bg-uts-500 hover:bg-uts-800 text-white w-10 h-10 rounded-lg inline-flex items-center justify-center">
                        <i class="fa-solid fa-reply"></i>
                    </button>';
                }
                
                // Para Fase 1, verificar si el estudiante ya envió
                if ($p->estado === 'Fase 1' && !$p->deshabilitado) {
                    $submited = $p->valoresCampos->where('campo.name', 'submited_fase1')->first();
                    $yaEnvio = $submited && $submited->valor === 'true';
                    
                    if ($yaEnvio) {
                        $buttons .= '<button onclick="abrirRespuestaFase1(' . $p->id . ')"
                            class="btn-action shadow bg-uts-500 hover:bg-uts-800 text-white w-10 h-10 rounded-lg inline-flex items-center justify-center">
                            <i class="fa-solid fa-reply"></i>
                        </button>';
                    }
                }
            }

            // Botones Habilitar/Deshabilitar (tu código existente)
            // ... 

            // Botón Roadmap
            $puedeVerRoadmap = !$p->deshabilitado && !in_array($p->estado, ['Pendiente', 'Rechazada']);
            if ($puedeVerRoadmap) {
                $buttons .= '
                    <form action="' . route('practicas.roadmap') . '" method="POST" class="inline-block m-0">
                        ' . csrf_field() . '
                        <input type="hidden" name="practica_id" value="' . $p->id . '">
                        <button type="submit" class="btn-action shadow bg-blue-500 hover:bg-blue-700 text-white w-10 h-10 rounded-lg inline-flex items-center justify-center">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </button>
                    </form>';
            }

            $buttons .= '</div>';
            return $buttons;
        })
        ->rawColumns(['estado', 'acciones', 'descripcion'])
        ->make(true);
}

    

    public function buscarEstudiantes(Request $request)
    {
        try {
            $search = $request->get('search');

            if (strlen($search) < 5) {
                return response()->json([]);
            }

            $userId      = auth()->id();
            $userNivelId = auth()->user()->nivel_id;

            // Subconsulta para encontrar estudiantes que NO tienen prácticas activas
            // Estados que se consideran "activos" (no se puede agregar a estos estudiantes)
            $estadosActivos = ['Pendiente', 'Fase 1', 'Fase 2', 'Fase 3', 'Fase 4', 'Fase 5'];

            $estudiantes = \App\Models\User::where('id', '!=', $userId)
                ->where('nivel_id', $userNivelId) // Mismo nivel académico
                ->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('nro_documento', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                })
            // Excluir estudiantes que tienen prácticas activas
                ->whereNotIn('id', function ($subquery) use ($estadosActivos) {
                    $subquery->select('user_id')
                        ->from('practicas')
                        ->whereIn('estado', $estadosActivos)
                        ->where('tipo_solicitud_id', 9); // Solo prácticas, no proyectos
                })
            // También excluir estudiantes que son segundo integrante en prácticas activas
                ->whereNotIn('id', function ($subquery) use ($estadosActivos) {
                    $subquery->select('valor')
                        ->from('practica_valores_campos')
                        ->whereIn('practica_id', function ($q) use ($estadosActivos) {
                            $q->select('id')
                                ->from('practicas')
                                ->whereIn('estado', $estadosActivos)
                                ->where('tipo_solicitud_id', 9);
                        })
                        ->where('campo_id', function ($cq) {
                            $cq->select('id')
                                ->from('campos')
                                ->where('name', 'id_integrante_2')
                                ->where('tipo_solicitud_id', 9);
                        });
                })
                ->with('nivel')
                ->limit(10)
                ->get();

            $resultado = $estudiantes->map(function ($user) {
                return [
                    'id'              => $user->id,
                    'nombre_completo' => $user->name,
                    'documento'       => $user->nro_documento,
                    'email'           => $user->email,
                    'nivel'           => $user->nivel ? $user->nivel->nombre : 'N/A',
                ];
            });

            return response()->json($resultado);

        } catch (\Exception $e) {
            \Log::error('Error en buscarEstudiantes: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    public function store(Request $request)
    {
        $tipo   = TipoSolicitud::where('nombre', 'practicas_fase_0')->first();
        $campos = Campo::where('tipo_solicitud_id', $tipo->id)->get();

        // Verificar si se envió tiene_empresa
        $tieneEmpresa = $request->input('tiene_empresa');
        if ($tieneEmpresa === null) {
            return response()->json(['errors' => ['tiene_empresa' => ['Debe seleccionar si tiene empresa o no.']]], 422);
        }
        $tieneEmpresa = $tieneEmpresa === '1';

        // Validación de hoja de vida
        $errors = [];
        if (! $tieneEmpresa && ! $request->hasFile('hoja_vida')) {
            $errors['hoja_vida'][] = 'Debe subir la hoja de vida si NO cuenta con empresa.';
        }

        // Validar que el segundo integrante no sea el mismo usuario
        if ($request->filled('id_integrante_2') && $request->id_integrante_2 == auth()->id()) {
            $errors['id_integrante_2'][] = 'No puede seleccionarse a sí mismo como compañero.';
        }

        if (! empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        // Crear la práctica
        $practica = Practica::create([
            'user_id'           => auth()->id(),
            'tipo_solicitud_id' => $tipo->id,
            'estado'            => 'Pendiente',
            'vencido'           => false,
            'deshabilitado'     => false,
        ]);

        // Guardar cada campo dinámico
        foreach ($campos as $campo) {
            // Saltar campos que se llenan automáticamente desde la sesión
            if (in_array($campo->name, ['nombre_completo', 'correo', 'nivel', 'documento', 'celular'])) {
                continue;
            }

            $valor = null;

            if ($campo->type == 'checkbox') {
                $valor = $request->input($campo->name) === '1' ? 'true' : 'false';
            } elseif ($campo->type == 'file') {
                if ($campo->name == 'hoja_vida' && $tieneEmpresa) {
                    continue;
                }
                if ($request->hasFile($campo->name)) {
                    $path  = $request->file($campo->name)->store('practicas', 'public');
                    $valor = $path;
                }
            } elseif ($campo->name == 'id_integrante_2') {
                // Solo guardar si se seleccionó un integrante
                $valor = $request->input($campo->name);
                if (empty($valor)) {
                    continue; // No guardar si está vacío
                }
            } else {
                $valor = $request->input($campo->name);
            }

            if ($valor !== null) {
                PracticaValorCampo::create([
                    'practica_id' => $practica->id,
                    'campo_id'    => $campo->id,
                    'valor'       => $valor,
                ]);
            }
        }

        // Enviar correos
       // $campos_nueva_practica = $practica->camposConValores();
       // $this->sendEmailSolicitudPractica($campos_nueva_practica);

        return response()->json(['message' => 'Práctica enviada correctamente']);
    }

    public function sendEmailSolicitudPractica($campos)
    {
        try {
            $cuerpo_correo         = [];
            $correos_destinatarios = [];
            $asunto_correo         = 'PRÁCTICAS EMPRESARIALES - FASE 0';
            $tipo_correo           = 'practicas_fase_0';
            $comentarios           = null;
            $esRespuesta           = false;

            $user = auth()->user();

            // Datos básicos
            $cuerpo_correo['estudiante']             = $user;
            $cuerpo_correo['correo']                 = $user->email;
            $cuerpo_correo['estado']                 = 'Pendiente';
            $cuerpo_correo['periodo']                = '2026-1';
            $cuerpo_correo['celular']                = $user->nro_celular ?? '';
            $cuerpo_correo['nivel'] = $user->nivel->nombre ?? '';
            $cuerpo_correo['integrante_2']           = null;
            $cuerpo_correo['integrante_2_documento'] = null;
           

            $correos_destinatarios[] = $user->email;

            foreach ($campos as $campo) {

                $nombreCampo = $campo['campo'];
                $valorCampo  = $campo['valor'];

                switch ($nombreCampo) {
                    case 'nivel':
                        $cuerpo_correo['nivel'] = \App\Models\Nivel::find($valorCampo)->nombre ?? $valorCampo;
                        break;

                    case 'empresa':
                        $cuerpo_correo['empresa'] = $valorCampo;
                        break;

                    case 'hoja_vida':
                        $cuerpo_correo['hoja_vida'] = $valorCampo;
                        break;
                    case 'id_integrante_2':
                        if (! empty($valorCampo)) {
                            $integrante = \App\Models\User::find($valorCampo);

                            if ($integrante) {
                                $cuerpo_correo['integrante_2'] = $integrante->name;

                                $cuerpo_correo['integrante_2_documento'] =
                                    ($integrante->tipo_documento->tag ?? '') . ' ' . ($integrante->nro_documento ?? '');

                                $cuerpo_correo['integrante_2_correo'] = $integrante->email ?? '';

                                $cuerpo_correo['integrante_2_celular'] = $integrante->nro_celular ?? '';
                            }
                        }
                        break;

                    default:
                        if ($nombreCampo !== 'id_integrante_2') {
                            $cuerpo_correo[$nombreCampo] = $valorCampo;
                        }
                        break;
                }
            }

            //  enviar campos dinámicos al Blade
            $cuerpo_correo['campos'] = $campos;

            Mail::to($correos_destinatarios)->send(
                new PracticasMail(
                    $asunto_correo,
                    $cuerpo_correo,
                    $comentarios,
                    $tipo_correo,
                    $correos_destinatarios,
                    $esRespuesta
                )
            );

        } catch (\Exception $e) {
            //dd($e->getMessage());
        }
    }

    public function responderSolicitud(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'solicitudPractica_id' => 'required',
            'estado'               => 'required|in:Aprobada,Rechazada',
            'mensaje'              => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $practica = Practica::findOrFail($request->solicitudPractica_id);

        // Buscar el campo 'respuesta_comite'
        $campoRespuesta = Campo::where('name', 'respuesta_comite')->firstOrFail();
        if (! $campoRespuesta) {
            return response()->json(['message' => 'Campo de respuesta no configurado'], 500);
        }

        // Guardar la respuesta en practica_valores_campos
        PracticaValorCampo::updateOrCreate(
            [
                'practica_id' => $practica->id,
                'campo_id'    => $campoRespuesta->id,
            ],
            [
                'valor' => $request->mensaje,
            ]
        );

        $estadoActual = $practica->estado;

        if ($request->estado === 'Aprobada') {
            // Mapeo de estados actuales → siguiente fase
            $nuevoEstado = match ($estadoActual) {
                'Pendiente' => 'Fase 1',
                'Fase 1'    => 'Fase 2',
                'Fase 2'    => 'Fase 3',
                'Fase 3'    => 'Fase 4',
                'Fase 4'    => 'Fase 5',
                'Fase 5'    => 'Finalizado',
                default     => $estadoActual
            };
        } else {
            // Si es rechazada, el nuevo estado es 'Rechazada'
            $nuevoEstado = 'Rechazada';
        }

        // Asignar el nuevo estado al objeto
        $practica->estado = $nuevoEstado;
        $practica->save();

        // Enviar correo al estudiante (implementar después)
       // $this->sendEmailRespuesta($practica, $nuevoEstado, $request->mensaje, $request->estado);

        return response()->json(['success' => 'Respuesta enviada exitosamente', 'estado' => $practica->estado]);
    }

    public function sendEmailRespuesta($practica, $nuevoEstado, $mensaje, $estadoRespuesta)
    {
        try {
            $cuerpo_correo         = [];
            $correos_destinatarios = [];
            $asunto_correo         = 'RESPUESTA COMITÉ - PRÁCTICAS';
            $tipo_correo           = 'respuesta_comite';
            $comentarios           = $mensaje;
            $esRespuesta           = true;

            $user = $practica->user;

            //Datos básicos
            $cuerpo_correo['estudiante'] = $user; 
            $cuerpo_correo['correo']     = $user->email ?? '';
            $cuerpo_correo['estado']     = $estadoRespuesta;
            $cuerpo_correo['mensaje']    = $mensaje;

            $cuerpo_correo['celular'] = $user->nro_celular ?? '';
            $cuerpo_correo['nivel']   = $user->nivel->nombre ?? '';
            $cuerpo_correo['periodo'] = $practica->periodo ?? '2026-1';

            // Integrante 2
            $cuerpo_correo['integrante_2']           = null;
            $cuerpo_correo['integrante_2_documento'] = null;
            $cuerpo_correo['integrante_2_correo']    = null;
            $cuerpo_correo['integrante_2_celular']   = null;

            // Destinatario
            if (!empty($user->email)) {
                $correos_destinatarios[] = $user->email;
            }

            // Campos dinámicos
            $campos = $practica->camposConValores();

            foreach ($campos as $campo) {

                $nombreCampo = $campo['campo'];
                $valorCampo  = $campo['valor'];

                switch ($nombreCampo) {

                    case 'nivel':
                        $cuerpo_correo['nivel'] =
                            \App\Models\Nivel::find($valorCampo)->nombre ?? $valorCampo;
                        break;

                    case 'empresa':
                        $cuerpo_correo['empresa'] = $valorCampo;
                        break;

                    case 'id_integrante_2':
                        if (!empty($valorCampo)) {
                            $integrante = \App\Models\User::find($valorCampo);

                            if ($integrante) {
                                $cuerpo_correo['integrante_2'] = $integrante;

                                $cuerpo_correo['integrante_2_documento'] =
                                    ($integrante->tipo_documento->tag ?? '') . ' ' .
                                    ($integrante->nro_documento ?? '');

                                $cuerpo_correo['integrante_2_correo']  = $integrante->email ?? '';
                                $cuerpo_correo['integrante_2_celular'] = $integrante->nro_celular ?? '';
                            }
                        }
                        break;

                    default:
                        if ($nombreCampo !== 'id_integrante_2') {
                            $cuerpo_correo[$nombreCampo] = $valorCampo;
                        }
                        break;
                }
            }

            // Campos al blade
            $cuerpo_correo['campos'] = $campos;

            // Enviar correo
            Mail::to($correos_destinatarios)->send(
                new PracticasMail(
                    $asunto_correo,
                    $cuerpo_correo,
                    $comentarios,
                    $tipo_correo,
                    $correos_destinatarios,
                    $esRespuesta
                )
            );

        } catch (\Exception $e) {
            \Log::error('Error correo: '.$e->getMessage());
        }
    }



    public function getDetalle($id)
{
    try {
        $practica = Practica::with('user.nivel', 'valoresCampos.campo')->findOrFail($id);
        
        $data = [];
        foreach ($practica->valoresCampos as $vc) {
            if ($vc->campo && $vc->campo->name) {
                $data[$vc->campo->name] = $vc->valor;
            }
        }
        
        // Determinar si el usuario es estudiante
        $esEstudiante = auth()->user()->hasRole('estudiante');

        // Obtener el segundo integrante
        $integrante2 = null;
        if (isset($data['id_integrante_2']) && !empty($data['id_integrante_2'])) {
            $integrante2 = \App\Models\User::find($data['id_integrante_2']);
        }

        // Construir HTML de integrantes
        $integrantesHtml = '';
        
        // Integrante 1
        $integrantesHtml .= '<div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">';
        $integrantesHtml .= '<p class="font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Integrante:</p>';
        $integrantesHtml .= '<div class="text-gray-800 w-full sm:flex-1 sm:ml-2">';
        $integrantesHtml .= e($practica->user->name) . '<br>';
        $integrantesHtml .= 'C.C ' . e($practica->user->nro_documento ?? 'N/A') . '<br>';
        $integrantesHtml .= e($practica->user->email) . '<br>';
        $integrantesHtml .= e($practica->user->nro_celular ?? 'N/A');
        $integrantesHtml .= '</div></div>';
        
        // Integrante 2 (si existe)
        if ($integrante2) {
            $integrantesHtml .= '<div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">';
            $integrantesHtml .= '<p class="font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Integrante:</p>';
            $integrantesHtml .= '<div class="text-gray-800 w-full sm:flex-1 sm:ml-2">';
            $integrantesHtml .= e($integrante2->name) . '<br>';
            $integrantesHtml .= 'C.C ' . e($integrante2->nro_documento ?? 'N/A') . '<br>';
            $integrantesHtml .= e($integrante2->email) . '<br>';
            $integrantesHtml .= e($integrante2->nro_celular ?? 'N/A');
            $integrantesHtml .= '</div></div>';
        }
        
        // Valores por defecto
        $director = $data['director_id'] ?? 'No asignado';
        $evaluador = $data['evaluador_id'] ?? 'No asignado';
        $codirector = $data['codirector_id'] ?? 'No asignado';
        
        // Si es estudiante, ocultar evaluador y codirector
        if ($esEstudiante) {
            $docentesHtml = '<div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">';
            $docentesHtml .= '<p class="font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Docentes:</p>';
            $docentesHtml .= '<div class="text-gray-800 w-full sm:flex-1 sm:ml-2">';
            $docentesHtml .= '<span><b>Director:</b> ' . e($director) . '</span><br>';
            $docentesHtml .= '<span><b>Evaluador:</b> <span class="text-gray-400 italic">No disponible</span></span><br>';
            $docentesHtml .= '<span><b>Codirector:</b> ' . e($codirector) . '</span>';
            $docentesHtml .= '</div></div>';
        } else {
            $docentesHtml = '<div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">';
            $docentesHtml .= '<p class="font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Docentes:</p>';
            $docentesHtml .= '<div class="text-gray-800 w-full sm:flex-1 sm:ml-2">';
            $docentesHtml .= '<span><b>Director:</b> ' . e($director) . '</span><br>';
            $docentesHtml .= '<span><b>Evaluador:</b> ' . e($evaluador) . '</span><br>';
            $docentesHtml .= '<span><b>Codirector:</b> ' . e($codirector) . '</span>';
            $docentesHtml .= '</div></div>';
        }
        
        // Título
        $titulo = $data['titulo'] ?? 'No disponible';
        
        // Nivel académico
        $nivel = $practica->user->nivel->nombre ?? 'N/A';
        
        // Periodo académico
        $periodo = $data['periodo'] ?? (date('Y') . '-' . (date('n') <= 6 ? '1' : '2'));
        
        // Modalidad
        $modalidad = 'Prácticas empresariales';
        
        // Empresa
        $tieneEmpresa = $data['tiene_empresa'] ?? 'false';
        $hojaVida = $data['hoja_vida'] ?? null;
        
        return response()->json([
            'id' => $practica->id,
            'estado' => $practica->estado,
            'vencido' => $practica->vencido,
            'deshabilitado' => $practica->deshabilitado,
            'fecha_solicitud' => $practica->created_at->format('d/m/Y H:i'),
            'integrantes_html' => $integrantesHtml,
            'docentes_html' => $docentesHtml,
            'titulo' => $titulo,
            'nivel' => $nivel,
            'periodo' => $periodo,
            'modalidad' => $modalidad,
            'tiene_empresa' => $tieneEmpresa === 'true',
            'hoja_vida' => $hojaVida,
            'es_estudiante' => $esEstudiante
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error en getDetalle: ' . $e->getMessage());        \Log::error('Error en getDetalle: ' . $e->getMessage());
        return response()->json(['error' => 'Error al cargar los detalles'], 500);
    }
}

    public function show($id)
    {
        $practica = Practica::with('user')->findOrFail($id);
        return view('practicas.show', compact('practica'));
    }

    // Habilitar una práctica (cambiar deshabilitado a false)
    public function habilitar(Request $request)
    {
        $practica = Practica::findOrFail($request->id);
        $practica->update(['deshabilitado' => false]);
        return response()->json(['success' => true]);
    }

    // Deshabilitar una práctica
    public function deshabilitar(Request $request)
    {
        $practica = Practica::findOrFail($request->id);
        $practica->update(['deshabilitado' => true]);
        return response()->json(['success' => true]);
    }

    public function deshabilitarConActa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'practica_id'            => 'required|exists:practicas,id',
            'nro_acta_desactivar'    => 'required|string',
            'fecha_acta_desactivar'  => 'required|date',
            'descripcion_desactivar' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $practica                = Practica::findOrFail($request->practica_id);
        $practica->deshabilitado = true;
        $practica->save();

        ActaPractica::create([
            'practica_id' => $practica->id,
            'numero'      => $request->nro_acta_desactivar,
            'fecha'       => $request->fecha_acta_desactivar,
            'descripcion' => $request->descripcion_desactivar,
        ]);

        return response()->json(['success' => 'Práctica deshabilitada correctamente']);
    }

    public function habilitarConActa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'practica_id_activar' => 'required|exists:practicas,id',
            'nro_acta_activar'    => 'required|string',
            'fecha_acta_activar'  => 'required|date',
            'descripcion_activar' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $practica                = Practica::findOrFail($request->practica_id_activar);
        $practica->deshabilitado = false;
        $practica->save();

        ActaPractica::create([
            'practica_id' => $practica->id,
            'numero'      => $request->nro_acta_activar,
            'fecha'       => $request->fecha_acta_activar,
            'descripcion' => $request->descripcion_activar,
        ]);

        return response()->json(['success' => 'Práctica habilitada correctamente']);
    }

}
