<?php
namespace App\Http\Controllers;

use App\Models\Campo;
use App\Models\Practica;
use App\Models\Solicitud;
use App\Mail\PracticasMail;
use App\Models\TipoSolicitud;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Models\PracticaValorCampo;
use App\Models\ActaPractica;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


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
        $practicas = Practica::with('user')
            ->where('tipo_solicitud_id', 9);

        if (auth()->user()->hasRole('estudiante')) {
            $practicas->where('user_id', auth()->id());
            $practicas->orderBy('id', 'desc');
        }

        // Búsqueda
        if ($request->has('search') && $search = $request->input('search.value')) {
            $practicas->where(function ($q) use ($search) {
                $q->orWhere('id', 'LIKE', "%{$search}%")
                    ->orWhere('estado', 'LIKE', "%{$search}%")
                    //->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.nombre_completo')) LIKE ?", ["%{$search}%"])
                    //->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.documento')) LIKE ?", ["%{$search}%"])
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%")
                            ->orWhere('nro_documento', 'LIKE', "%{$search}%");
                    });
            });
        }

        return DataTables::of($practicas)

            ->addColumn('descripcion', function ($p) {

                $tipo = $p->tipo_solicitud_id;
                                               
                $descripcionBase = 'Solicitud de prácticas empresariales';
                return "{$descripcionBase}";
            })

            ->addColumn('estado', function ($p) {
                $badgeClass = match ($p->estado) {
                    'Pendiente' => 'bg-yellow-100 text-yellow-800 border border-yellow-300',
                    'Aprobada'  => 'bg-green-100 text-green-800 border border-green-300',
                    'Rechazada' => 'bg-red-100 text-red-800 border border-red-300',
                    default     => 'bg-gray-100 text-gray-800 border border-gray-300',
                };

                $return_html = '<div class="flex gap-2 flex-wrap items-center justify-center">';
                
                if ($p->estado == 'Pendiente') {
                    $estadoBadge = "<span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300'>{$p->estado}</span>";
                    $comiteBadge = "<span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300'>Comité</span>";

                    return "{$return_html}{$estadoBadge} {$comiteBadge}</div>";
                }

                if ($p->estado == 'Rechazada') {
                    $badge = "<span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-red-100 text-red-800 border border-red-300'>Rechazada</span>";
                    return $return_html . $badge . "</div>";
                }

                if (str_contains($p->estado, 'Fase')) {
                    $estadoBadge = "<span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-uts-300 border border-uts-500'>{$p->estado}</span>";

                    $estado_array = explode(' ', $p->estado);
                    $fase         = $estado_array[1];

                    $estudianteBadge = "<span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300'>Estudiante</span>";
                    $comiteBadge     = "<span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300'>Comité</span>";

                    switch ($fase) {
                        case 1:
                            return $return_html . $estadoBadge . $estudianteBadge . "</div>";

                        default:
                            return $return_html . $estadoBadge . "</div>";
                    }

                }

                
                return $return_html . "<span>{$p->estado}</span></div>";
            })
            ->addColumn('acciones', function ($p) {
                $user    = auth()->user();
                $buttons = '<div class="flex items-center gap-1">';

                // Botón Ver con spinner
                $buttons .= '<button onclick="openDetailsModal(this, ' . $p->id . ')" class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                <i class="fa-regular fa-eye"></i>
                <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute inset-0 m-auto" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                    </svg>
                    </button>';
                    
                    // Botón responder (solo para admin, coordinador, super_admin y estado Pendiente)
                    if ($user->hasRole(['super_admin', 'admin', 'coordinador']) && $p->estado === 'Pendiente') {
                    $buttons .= '<button onclick="openResponderSolicitudModal('.$p->id.')" class="btn-action shadow bg-uts-500 hover:bg-uts-800 text-white px-3 py-1 rounded-lg"><i class="fa-solid fa-reply"></i></button>';
                }

                // Botones habilitar/deshabilitar (ahora con modal de acta)
                if ($user->hasRole(['super_admin', 'admin', 'coordinador'])) {
                    if (! $p->deshabilitado) {
                        $buttons .= '<button onclick="deshabilitarPracticaConActa(' . $p->id . ')" class="btn-action shadow bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded-lg"><i class="fa-regular fa-circle-xmark"></i></button>';
                    } else {
                        $buttons .= '<button onclick="habilitarPracticaConActa(' . $p->id . ')" class="btn-action shadow bg-teal-500 hover:bg-teal-700 text-white px-3 py-1 rounded-lg"><i class="fa-solid fa-clock-rotate-left"></i></button>';
                    }
                }
               

                // Botón roadmap (se mantiene igual)
                if (!in_array($p->estado, ['Pendiente', 'Rechazada'])) {
                    $buttons .= '
                    <form action="' . route('practicas.roadmap') . '" method="POST" class="inline-block">
                        ' . csrf_field() . '
                        <input type="hidden" name="practica_id" value="' . $p->id . '">
                        <button type="submit" class="btn-action shadow bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded-lg">
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

    public function store(Request $request)
    {
        $tipo = TipoSolicitud::where('nombre', 'practicas_fase_0')->first();
        $campos = Campo::where('tipo_solicitud_id', $tipo->id)->get();

        $tieneEmpresa = $request->input('tiene_empresa');
        if ($tieneEmpresa === null) {
            return response()->json(['errors' => ['tiene_empresa' => ['Debe seleccionar si tiene empresa o no.']]], 422);
        }
        $tieneEmpresa = $tieneEmpresa === '1';

        // Validación
        $errors = [];
        if (!$tieneEmpresa && !$request->hasFile('hoja_vida')) {
            $errors['hoja_vida'][] = 'Debe subir la hoja de vida si NO cuenta con empresa.';
        }
        if (!empty($errors)) {
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
            // Estos campos ya están en la tabla users, no se guardan aquí
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
                    $path = $request->file($campo->name)->store('practicas', 'public');
                    $valor = $path;
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
            

        $campos_nueva_practica =$practica->camposConValores();
        $this->sendEmailSolicitudPractica($campos_nueva_practica);
      
    

        return response()->json(['message' => 'Práctica enviada correctamente']);
    }


        public function sendEmailSolicitudPractica($campos)
        {
            try {
                $cuerpo_correo = [];
                $correos_destinatarios = [];
                $asunto_correo = 'PRÁCTICAS EMPRESARIALES - FASE 0';
                $tipo_correo = 'practicas_fase_0';
                $comentarios = null;
                $esRespuesta = false;

                // Datos del usuario autenticado
                $user = auth()->user();

                $cuerpo_correo['estudiante'] = $user->name;
                $cuerpo_correo['correo'] = $user->email;
                $cuerpo_correo['estado'] = 'Pendiente';

                $correos_destinatarios[] = $user->email;

                foreach ($campos as $campo) {
                    switch ($campo['campo']) {

                        case 'nivel':
                            $cuerpo_correo['nivel'] = \App\Models\Nivel::find($campo['valor'])->nombre ?? $campo['valor'];
                            break;

                        case 'empresa':
                            $cuerpo_correo['empresa'] = $campo['valor'];
                            break;

                        case 'hoja_vida':
                            $cuerpo_correo['hoja_vida'] = $campo['valor'];
                            break;

                        default:
                            $cuerpo_correo[$campo['campo']] = $campo['valor'];
                            break;
                    }
                }

                Mail::send(new PracticasMail(
                    $asunto_correo,
                    $cuerpo_correo,
                    $comentarios,
                    $tipo_correo,
                    $correos_destinatarios,
                    $esRespuesta
                ));

            } catch (\Exception $e) {
                dd($e->getMessage());
            }
        }




    public function getDetalle($id)
    {
        $practica = Practica::with('user.nivel', 'valoresCampos.campo')->findOrFail($id);
        
        $data = [];
        foreach ($practica->valoresCampos as $vc) {
            if ($vc->campo && $vc->campo->name) {
                $data[$vc->campo->name] = $vc->valor;
            }
        }
        
        return response()->json([
            'id'              => $practica->id,
            'estado'          => $practica->estado,
            'vencido'         => $practica->vencido,
            'deshabilitado'   => $practica->deshabilitado,
            'fecha_solicitud' => $practica->created_at->format('d/m/Y H:i'),
            'user'            => [
                'name'          => $practica->user->name,
                'email'         => $practica->user->email,
                'nivel'         => $practica->user->nivel->nombre ?? 'N/A',
                'nro_documento' => $practica->user->nro_documento ?? 'N/A',
                'nro_celular'   => $practica->user->nro_celular ?? 'N/A',
            ],
            'data'            => $data
        ]);
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
        if (!$campoRespuesta) {
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
        
        // Cambiar estado de la práctica
        $nuevoEstado = ($request->estado === 'Aprobada') ? 'Fase 1' : 'Rechazada';
        $practica->estado = $nuevoEstado;
        $practica->save();
        
        // Aquí enviar correo al estudiante (similar a proyectos)
        // ...
        
        return response()->json(['success' => 'Respuesta enviada exitosamente', 'estado' => $practica->estado]);
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
            'practica_id' => 'required|exists:practicas,id',
            'nro_acta_desactivar' => 'required|string',
            'fecha_acta_desactivar' => 'required|date',
            'descripcion_desactivar' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $practica = Practica::findOrFail($request->practica_id);
        $practica->deshabilitado = true;
        $practica->save();

        ActaPractica::create([
            'practica_id' => $practica->id,
            'numero' => $request->nro_acta_desactivar,
            'fecha' => $request->fecha_acta_desactivar,
            'descripcion' => $request->descripcion_desactivar,
        ]);

        return response()->json(['success' => 'Práctica deshabilitada correctamente']);
    }

    public function habilitarConActa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'practica_id_activar' => 'required|exists:practicas,id',
            'nro_acta_activar' => 'required|string',
            'fecha_acta_activar' => 'required|date',
            'descripcion_activar' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $practica = Practica::findOrFail($request->practica_id_activar);
        $practica->deshabilitado = false;
        $practica->save();

        ActaPractica::create([
            'practica_id' => $practica->id,
            'numero' => $request->nro_acta_activar,
            'fecha' => $request->fecha_acta_activar,
            'descripcion' => $request->descripcion_activar,
        ]);

        return response()->json(['success' => 'Práctica habilitada correctamente']);
    }

}
