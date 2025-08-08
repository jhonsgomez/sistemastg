<?php

namespace App\Http\Controllers;

use App\Mail\PropuestaBancoMail;
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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SolicitudBancoController extends Controller
{
    public function getType()
    {
        $type = TipoSolicitud::query()->where('nombre', '=', 'solicitud_banco')->where('deleted_at', '=', NULL)->first();
        return $type;
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

    public function index()
    {
        $type = null;
        $campos = null;
        $modalidades = null;
        $niveles = null;
        $fechas = null;

        try {
            $type = self::getType();
            $campos = Campo::query()->where('tipo_solicitud_id', '=', $type->id)->where('deleted_at', '=', NULL)->get();
            $modalidades = Modalidad::query()->where('deleted_at', '=', NULL)->get();
            $niveles = Nivel::query()->where('deleted_at', '=', NULL)->get();
            $lineas_investigacion = LineaInvestigacion::query()->where('deleted_at', '=', NULL)->get();

            $periodo_actual = self::getPeriodoActual();
            $fechas = self::getFechasByPeriodo($periodo_actual);

            return view('banco.propuestas', compact(['campos', 'modalidades', 'niveles', 'lineas_investigacion', 'fechas']));
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function getData(Request $request)
    {
        $type = null;
        $solicitudes = null;

        try {
            $type = self::getType();

            if (auth()->user()->hasRole(['super_admin', 'admin', 'coordinador', 'lider_investigacion'])) {
                $solicitudes = Solicitud::query()
                    ->where('tipo_solicitud_id', '=', $type->id)
                    ->selectRaw('*, CONCAT("BAN-00", id) as formatted_id');
            } else {
                $solicitudes = Solicitud::query()
                    ->where('tipo_solicitud_id', '=', $type->id)
                    ->where('user_id', '=', auth()->user()->id)
                    ->selectRaw('*, CONCAT("BAN-00", id) as formatted_id');
            }

            return DataTables::of($solicitudes)
                ->addColumn('actions', function ($solicitud) {
                    $campos = $solicitud->camposConValores();
                    $periodo_solicitud = self::findCampoByName($campos, 'periodo');
                    $disponible_solicitud = self::findCampoByName($campos, 'disponible');
                    $periodo_actual = self::getPeriodoActual();

                    $fecha_actual = Carbon::now()->format('Y-m-d');
                    $fechas_periodo = self::getFechasByPeriodo($periodo_actual);

                    $solicitud_array = [
                        'id' => $solicitud->id,
                        'user_id' => $solicitud->user_id
                    ];

                    $solicitudJson = htmlspecialchars(json_encode($solicitud_array));

                    $buttons = '<div class="flex items-center justify-center container-actions">';

                    $buttons .= '<button type="button" onclick="openDetailsModal(' . $solicitudJson . ')" 
                                    id="openModalButton-' . $solicitud->id . '"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                                    <i class="fa-regular fa-eye"></i>
                                    <svg id="loadingSpinner-' . $solicitud->id . '" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                        <path
                                            d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                            stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path
                                            d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                            stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                        </path>
                                    </svg>
                                </button>';

                    if (isset($fechas_periodo)) {
                        if ($fecha_actual >= $fechas_periodo['fecha_inicio_banco'] && $fecha_actual <= $fechas_periodo['fecha_fin_banco']) {
                            if ($periodo_actual == $periodo_solicitud && $solicitud->user_id == auth()->user()->id && $solicitud->estado == 'Rechazada') {
                                $buttons .= '<button type="button" onclick="openEditModal(' . $solicitudJson . ')" 
                                            id="openEditModalButton-' . $solicitud->id . '"
                                            class="btn-action shadow bg-indigo-500 hover:bg-indigo-700 text-white px-3 py-1 rounded-lg relative">
                                            <i class="fa-solid fa-file-pen"></i>
                                            <svg id="loadingEditSpinner-' . $solicitud->id . '" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                                <path
                                                    d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                                    stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path
                                                    d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                                    stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                                </path>
                                            </svg>
                                        </button>';
                            }

                            if (
                                $solicitud->user_id == auth()->user()->id &&
                                $periodo_actual != $periodo_solicitud &&
                                (
                                    $solicitud->estado == 'Aprobada' ||
                                    $solicitud->estado == 'Pendiente'
                                ) &&
                                $disponible_solicitud == "true"
                            ) {
                                $buttons .= '
                                <form id="repostSolicitudForm-' . $solicitud->id . '" onsubmit="repostSolicitud(event, ' . $solicitud->id . ')">
                                    ' . csrf_field() . '
                                    <input type="hidden" name="solicitud_idRepost" id="solicitud_idRepost" value="' . $solicitud->id . '" />
                                    <input type="hidden" name="periodoRepost" id="periodoRepost" value=\'' . self::getPeriodoActual() . '\' />
                                    <input type="hidden" name="disponibleRepost" id="disponibleRepost" value="false" />
                                    <button type="submit"
                                        id="repostSolicitudButton-' . $solicitud->id . '"
                                        class="btn-action shadow bg-uts-500 hover:bg-uts-800 text-white px-3 py-1 rounded-lg relative">
                                        <i class="fa-solid fa-share"></i>
                                        <svg id="loadingRepostSpinner-' . $solicitud->id . '" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                            <path
                                                d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                                stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path
                                                d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                                stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                            </path>
                                        </svg>
                                    </button>
                                </form>';
                            }
                        }
                    }

                    if (
                        auth()->user()->can('reply_propuesta_banco') &&
                        $solicitud->estado == 'Pendiente' &&
                        $periodo_actual == $periodo_solicitud
                    ) {
                        $buttons .= '<button type="button" id="replySolicitudButton" onclick="openReplySolicitudModal(' . $solicitud->id . ')" 
                                    class="btn-action shadow bg-uts-500 hover:bg-uts-800 text-white px-3 py-1 rounded-lg">
                                    <i class="fa-solid fa-share"></i>
                                </button>';
                    }

                    $buttons .= '</div>';
                    return $buttons;
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->input('search.value'))) {
                        $searchValue = $request->input('search.value');

                        $query->where(function ($q) use ($searchValue) {
                            // Búsqueda en valores_campos usando joins
                            $q->whereHas('valoresCampos', function ($q) use ($searchValue) {
                                $q->whereHas('campo', function ($q) use ($searchValue) {
                                    $q->where(function ($q) use ($searchValue) {
                                        $q->whereIn('name', ['titulo', 'objetivo', 'periodo'])
                                            ->where('valores_campos.valor', 'LIKE', "%{$searchValue}%");
                                    });
                                    $q->orWhere(function ($q) use ($searchValue) {
                                        // Para modalidad
                                        $q->where('name', '=', 'modalidad')
                                            ->whereIn('valores_campos.valor', function ($q) use ($searchValue) {
                                                $q->select('id')
                                                    ->from('modalidades')
                                                    ->where('nombre', 'LIKE', "%{$searchValue}%");
                                            });
                                    });
                                    $q->orWhere(function ($q) use ($searchValue) {
                                        // Para lineas de investigación
                                        $q->where('name', '=', 'linea_investigacion')
                                            ->whereIn('valores_campos.valor', function ($q) use ($searchValue) {
                                                $q->select('id')
                                                    ->from('lineas_investigacion')
                                                    ->where('nombre', 'LIKE', "%{$searchValue}%");
                                            });
                                    });
                                    $q->orWhere(function ($q) use ($searchValue) {
                                        // Para Niveles
                                        $q->where('name', '=', 'nivel')
                                            ->whereIn('valores_campos.valor', function ($q) use ($searchValue) {
                                                $q->select('id')
                                                    ->from('niveles')
                                                    ->where('nombre', 'LIKE', "%{$searchValue}%");
                                            });
                                    });
                                });
                            })

                                // Búsqueda en id
                                ->orWhereRaw('CONCAT("BAN-00", solicitudes.id) LIKE ?', ["%{$searchValue}%"])
                                // Búsqueda en estado
                                ->orWhere('solicitudes.estado', 'LIKE', "%{$searchValue}%")
                                // Búsqueda en nombre de usuario
                                ->orWhereHas('user', function ($q) use ($searchValue) {
                                    $q->where('name', 'LIKE', "%{$searchValue}%")
                                        ->orWhere('email', 'LIKE', "%{$searchValue}%");
                                });
                        });
                    }
                })
                ->rawColumns(['actions'])
                ->make(true);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function store(Request $request)
    {
        $type = null;
        $campos = null;
        $rules = [];

        try {
            $type = self::getType();
            $campos = Campo::query()->where('tipo_solicitud_id', '=', $type->id)->where('deleted_at', '=', NULL)->get();

            if ($request->has('solicitud_idEdit')) {
                foreach ($campos as $campo) {
                    $rules[$campo->name . 'Edit'] = $campo->required ? 'required' : 'nullable';

                    if ($campo->type == 'text' || $campo->type == 'textarea') {
                        $rules[$campo->name . 'Edit'] .= '|string';
                    } elseif ($campo->type == 'number') {
                        $rules[$campo->name . 'Edit'] .= '|numeric';
                    } elseif ($campo->type == 'email') {
                        $rules[$campo->name . 'Edit'] .= '|email';
                    }
                }
            } else {
                foreach ($campos as $campo) {
                    $rules[$campo->name] = $campo->required ? 'required' : 'nullable';

                    if ($campo->type == 'text' || $campo->type == 'textarea') {
                        $rules[$campo->name] .= '|string';
                    } elseif ($campo->type == 'number') {
                        $rules[$campo->name] .= '|numeric';
                    } elseif ($campo->type == 'email') {
                        $rules[$campo->name] .= '|email';
                    }
                }
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $solicitud = null;

            if ($request->has('solicitud_idEdit')) {
                $solicitud = Solicitud::query()
                    ->where('id', '=', $request->input('solicitud_idEdit'))
                    ->firstOrFail();

                $campos_solicitud = ValorCampo::query()->where('solicitud_id', '=', $solicitud->id)->where('deleted_at', '=', NULL)->get();

                foreach ($campos_solicitud as $item) {
                    $campo = Campo::query()
                        ->where('id', '=', $item->campo_id)
                        ->firstOrFail();

                    if ($request->has($campo->name . 'Edit')) {
                        $item->update(['valor' => $request->input($campo->name . 'Edit')]);
                    }
                }

                $solicitud->update(['estado' => 'Pendiente']);
            } else {
                $solicitud = Solicitud::create([
                    'user_id' => auth()->user()->id,
                    'tipo_solicitud_id' => $type->id,
                    'descripcion' => 'Propuesta de docente para publicar en el banco de ideas institucional'
                ]);

                foreach ($campos as $campo) {
                    if ($request->has($campo->name)) {
                        ValorCampo::create([
                            'solicitud_id' => $solicitud->id,
                            'campo_id' => $campo->id,
                            'valor' => $request->input($campo->name)
                        ]);
                    }
                }
            }

            self::enviarCorreoPropuestasBanco($solicitud);

            return response()->json(['success' => 'Propuesta enviada exitosamente']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function getValorCampos($id)
    {
        try {
            $solicitud = Solicitud::findOrFail($id);
            return response()->json([
                'campos' => $solicitud->camposConValores()
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function repostSolicitud(Request $request)
    {
        try {
            $solicitud = Solicitud::query()
                ->where('id', '=', $request->input('solicitud_idRepost'))
                ->firstOrFail();

            $campos_solicitud = ValorCampo::query()->where('solicitud_id', '=', $solicitud->id)->where('deleted_at', '=', NULL)->get();

            foreach ($campos_solicitud as $item) {
                $campo = Campo::query()
                    ->where('id', '=', $item->campo_id)
                    ->firstOrFail();

                if ($request->has($campo->name . 'Repost')) {
                    $item->update(['valor' => $request->input($campo->name . 'Repost')]);
                }
            }

            $solicitud->update(['estado' => 'Pendiente']);

            self::enviarCorreoPropuestasBanco($solicitud);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function responderSolicitud(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'solicitud_id' => 'required',
                'estado' => 'required',
                'mensaje' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $solicitud = Solicitud::findOrFail($request->post('solicitud_id'));
            $solicitud->estado = $request->post('estado');
            if ($request->post('estado') === 'Aprobada') {
                $campos = $solicitud->camposConValores();
                $campoDisponible = null;
                foreach ($campos as $item) {
                    if (isset($item['campo']['name']) && $item['campo']['name'] === 'disponible') {
                        $campoDisponible = $item['campo'];
                        break;
                    }
                }
                $campo = ValorCampo::where('solicitud_id', '=', $solicitud->id)->where('campo_id', '=', $campoDisponible['id'])->firstOrFail();
                $campo->valor = 'true';
                $campo->save();
            }

            $solicitud->save();

            self::enviarCorreoPropuestasBanco($solicitud, $request->post('mensaje'), true);

            return response()->json(['success' => 'Respuesta enviada exitosamente']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function enviarCorreoPropuestasBanco($solicitud, $comentarios = null, $esRespuesta = false)
    {
        try {
            $docente = User::findOrFail($solicitud->user_id);
            $correo_docente = $docente->email;

            $campos = $solicitud->camposConValores();

            $cuerpo_correo = [
                'nombre' => $docente->name,
                'titulo' => self::findCampoByName($campos, 'titulo'),
                'modalidad' => Modalidad::findOrFail(self::findCampoByName($campos, 'modalidad'))->nombre,
                'linea_investigacion' => LineaInvestigacion::findOrFail(self::findCampoByName($campos, 'linea_investigacion'))->nombre,
                'nivel' => Nivel::findOrFail(self::findCampoByName($campos, 'nivel'))->nombre,
                'periodo_academico' => self::findCampoByName($campos, 'periodo'),
                'comentarios' => $comentarios,
                'estado_solicitud' => $solicitud->estado
            ];

            // Enviar correos
            if (isset($esRespuesta) && $esRespuesta) {
                Mail::queue(new PropuestaBancoMail($cuerpo_correo, $comentarios,  'respuesta', $correo_docente, $esRespuesta));
            } else {
                Mail::queue(new PropuestaBancoMail($cuerpo_correo, $comentarios,  'propuesta', $correo_docente, $esRespuesta));
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function bancoIndex()
    {
        try {
            return view('banco.index');
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function findCampoByName($campos, $name)
    {
        foreach ($campos as $item) {
            if (isset($item['campo']['name']) && $item['campo']['name'] === $name) {
                return $item['valor'] ? $item['valor'] : null;
            }
        }
    }

    public function bancoData()
    {
        $type = null;
        $solicitudes = null;

        try {
            $type = self::getType();
            $solicitudes = Solicitud::query()
                ->where('estado', '=', 'Aprobada')
                ->where('tipo_solicitud_id', '=', $type->id)
                ->selectRaw('*, CONCAT("BAN-00", id) as formatted_id')
                ->get();

            $periodo_actual = self::getPeriodoActual();
            $ideas_banco = [];

            foreach ($solicitudes as $solicitud) {
                $campos = $solicitud->camposConValores();
                $periodo = self::findCampoByName($campos, 'periodo');
                if ($periodo === $periodo_actual) {
                    $ideas_banco[] = $solicitud;
                }
            }

            return DataTables::of($ideas_banco)
                ->addColumn('propuesta_periodo', function ($solicitud) {
                    $campos = $solicitud->camposConValores();
                    return self::findCampoByName($campos, 'periodo');
                })
                ->addColumn('propuesta_nivel', function ($solicitud) {
                    $campos = $solicitud->camposConValores();
                    $nivel = Nivel::findOrFail(self::findCampoByName($campos, 'nivel'));
                    return $nivel ? $nivel->nombre : null;
                })
                ->addColumn('propuesta_titulo', function ($solicitud) {
                    $campos = $solicitud->camposConValores();
                    return self::findCampoByName($campos, 'titulo');
                })
                ->addColumn('propuesta_disponible', function ($solicitud) {
                    $campos = $solicitud->camposConValores();
                    return self::findCampoByName($campos, 'disponible');
                })
                ->addColumn('propuesta_docente', function ($solicitud) {
                    $docente = User::findOrFail($solicitud->user_id);
                    return $docente->name;
                })
                ->addColumn('propuesta_correo', function ($solicitud) {
                    $docente = User::findOrFail($solicitud->user_id);
                    return $docente->email;
                })
                ->addColumn('propuesta_objetivo', function ($solicitud) {
                    $campos = $solicitud->camposConValores();
                    return self::findCampoByName($campos, 'objetivo');
                })
                ->addColumn('propuesta_linea_investigacion', function ($solicitud) {
                    $campos = $solicitud->camposConValores();
                    $linea_investigacion = LineaInvestigacion::FindOrFail(self::findCampoByName($campos, 'linea_investigacion'));
                    return $linea_investigacion ? $linea_investigacion->nombre : null;
                })
                ->addColumn('propuesta_modalidad', function ($solicitud) {
                    $campos = $solicitud->camposConValores();
                    $modalidad = Modalidad::findOrFail(self::findCampoByName($campos, 'modalidad'));
                    return $modalidad ? $modalidad->nombre : null;
                })
                ->addColumn('acciones', function ($solicitud) {
                    $campos = $solicitud->camposConValores();
                    $disponible = self::findCampoByName($campos, 'disponible');
                    $html = null;
                    if (auth()->user()->can('delete_banco_ideas') && $disponible === 'true') {
                        $html .= '
                        <div class="flex items-center justify-start container-actions">
                            <button onclick="deleteSolicitud(' . $solicitud->id . ')" class="btn-action shadow bg-red-500 hover:bg-red-800 text-white px-3 py-1 rounded-lg mr-2 btn-delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>';
                    }
                    return $html ? $html : null;
                })
                ->rawColumns(['acciones'])
                ->make(true);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function destroySolicitud($id)
    {
        try {
            $solicitud = Solicitud::findOrFail($id);
            $campos = $solicitud->camposConValores();

            $disponible = self::findCampoByName($campos, 'disponible');
            if ($disponible === 'true') {
                $solicitud->update(['estado' => 'Rechazada']);
                self::enviarCorreoPropuestasBanco($solicitud, 'Su propuesta ha sido rechazada del banco de ideas.', true);
            } else {
                return response()->json(['message' => 'No se puede eliminar una propuesta que no está disponible.'], 422);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function generarReporte(Request $request)
    {
        $type = null;
        $periodo = null;
        $formato_reporte = null;
        $ideas = null;
        $validator = null;

        try {
            $validator = Validator::make($request->all(), [
                'periodo_reporte' => 'required',
            ], [
                'periodo_reporte.required' => 'El campo periodo es obligatorio',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $type = self::getType('solicitud_banco');
            $periodo = $request->periodo_reporte;
            $formato_reporte = public_path('formatos/informe_ideas.xlsx');

            // Cargar el archivo de Excel existente
            $spreadsheet = IOFactory::load($formato_reporte);
            $sheet = $spreadsheet->getActiveSheet();
            $fila = 3;

            $ideas = Solicitud::query()
                ->where('tipo_solicitud_id', $type->id)
                ->whereIn('estado', ['Aprobada'])
                ->whereHas('valoresCampos', function ($q) use ($periodo) {
                    $q->whereHas('campo', function ($q) use ($periodo) {
                        $q->where(function ($q) use ($periodo) {
                            $q->where('name', 'periodo')
                                ->where('valores_campos.valor', $periodo);
                        });
                    });
                })
                ->get();

            foreach ($ideas as $idea) {
                $campos = $idea->camposConValores();

                $id = 'BAN-00' . $idea->id;
                $periodo_academico = self::findCampoByName($campos, 'periodo');
                $titulo = mb_strtoupper(self::findCampoByName($campos, 'titulo'));
                $modalidad = Modalidad::findOrFail(self::findCampoByName($campos, 'modalidad'))->nombre;
                $nivel = Nivel::findOrFail(self::findCampoByName($campos, 'nivel'))->nombre;
                $linea_investigacion = LineaInvestigacion::findOrFail(self::findCampoByName($campos, 'linea_investigacion'))->nombre;
                $disponible = self::findCampoByName($campos, 'disponible') === 'true' ? 'Sí' : 'No';
                $objetivo = ucfirst(strtolower(self::findCampoByName($campos, 'objetivo')));
                $docente = mb_strtoupper(User::findOrFail($idea->user_id)->name);

                // Insertar valores
                $sheet->setCellValue("A{$fila}", $id);
                $sheet->setCellValue("B{$fila}", $periodo_academico);
                $sheet->setCellValue("C{$fila}", $titulo);
                $sheet->setCellValue("D{$fila}", $modalidad);
                $sheet->setCellValue("E{$fila}", $nivel);
                $sheet->setCellValue("F{$fila}", $linea_investigacion);
                $sheet->setCellValue("G{$fila}", $disponible);
                $sheet->setCellValue("H{$fila}", $objetivo);
                $sheet->setCellValue("I{$fila}", $docente);

                // Aplicar bordes a cada celda de la fila
                foreach (range('A', 'I') as $col) {
                    $sheet->getStyle("{$col}{$fila}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle("{$col}{$fila}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                }

                $fila++;
            }

            // Definir el nombre del archivo a descargar
            $fileName = "Informe - Banco de ideas ({$periodo}).xlsx";

            // Guardar en un stream para descargar sin escribir en disco
            $response = new StreamedResponse(function () use ($spreadsheet) {
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save('php://output');
            });

            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', "attachment; filename=\"{$fileName}\"");

            return $response;
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }
}
