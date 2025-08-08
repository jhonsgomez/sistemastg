<?php

namespace App\Http\Controllers;

use App\Mail\DeshabilitarProyectoMail;
use App\Mail\HabilitarProyectoMail;
use App\Mail\ProyectosGradoMail;
use App\Mail\SolicitudEstimuloIcfesMail;
use App\Models\Acta;
use App\Models\Campo;
use App\Models\Fecha;
use App\Models\LineaInvestigacion;
use App\Models\Modalidad;
use App\Models\Nivel;
use App\Models\Solicitud;
use App\Models\TipoDocumento;
use App\Models\TipoSolicitud;
use App\Models\User;
use App\Models\ValorCampo;
use App\Rules\ProyectoEnCurso;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProyectoGradoController extends Controller
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

        return null;
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

    public function index()
    {
        $type = null;
        $campos = null;
        $modalidades = null;
        $niveles = null;
        $fechas = null;

        try {
            $type = self::getType('fase_0');
            $campos = Campo::query()->where('tipo_solicitud_id', '=', $type->id)->where('deleted_at', '=', NULL)->get();
            $modalidades = Modalidad::query()->where('deleted_at', '=', NULL)->get();
            $niveles = Nivel::query()->where('deleted_at', '=', NULL)->get();
            $estudiantes = User::role('estudiante')
                ->where('id', '!=', auth()->user()->id)
                ->where('nivel_id', auth()->user()->nivel_id)->get();

            $periodo_actual = self::getPeriodoActual();
            $fechas = self::getFechasByPeriodo($periodo_actual);

            return view('proyectos.solicitudes', compact(['campos', 'modalidades', 'niveles', 'fechas', 'estudiantes']));
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function getActionFormRoadMap()
    {
        $ultimaRuta = url()->previous();
        $action = null;

        if ($ultimaRuta == route('director.index')) {
            $action = route('director.roadmap');
        } else if ($ultimaRuta == route('evaluador.index')) {
            $action = route('evaluador.roadmap');
        } else {
            $action = route('roadmap.index');
        }

        return $action;
    }

    public function getData(Request $request)
    {
        $type = null;
        $ruta = null;

        try {
            $type = self::getType('fase_0');
            $ruta = url()->previous();

            if (auth()->user()->hasRole(['super_admin', 'admin', 'coordinador', 'lider_investigacion'])) {
                $solicitudes = Solicitud::query()->where('tipo_solicitud_id', '=', $type->id)->selectRaw('*, CONCAT("GRA-00", id) as formatted_id')->orderBy('id', 'desc');
            } else if (auth()->user()->hasRole('estudiante')) {
                $solicitudes = Solicitud::query()->where('tipo_solicitud_id', '=', $type->id)
                    ->whereHas('valoresCampos', function ($q) {
                        $q->whereHas('campo', function ($q) {
                            $q->where(function ($q) {
                                $q->whereIn('name', ['id_integrante_1', 'id_integrante_2', 'id_integrante_3'])
                                    ->where('valores_campos.valor', auth()->user()->id);
                            });
                        });
                    })
                    ->selectRaw('*, CONCAT("GRA-00", id) as formatted_id')
                    ->orderBy('id', 'desc');
            } else if (auth()->user()->hasRole('director') && $ruta === route('director.index')) {
                $solicitudes = Solicitud::query()->where('tipo_solicitud_id', '=', $type->id)
                    ->whereHas('valoresCampos', function ($q) {
                        $q->whereHas('campo', function ($q) {
                            $q->where(function ($q) {
                                $q->where('name', 'director_id')
                                    ->where('valores_campos.valor', auth()->user()->id);
                            });
                        });
                    })
                    ->selectRaw('*, CONCAT("GRA-00", id) as formatted_id')
                    ->orderBy('id', 'desc');
            } else if (auth()->user()->hasRole('evaluador') && $ruta === route('evaluador.index')) {
                $solicitudes = Solicitud::query()->where('tipo_solicitud_id', '=', $type->id)
                    ->whereHas('valoresCampos', function ($q) {
                        $q->whereHas('campo', function ($q) {
                            $q->where(function ($q) {
                                $q->where('name', 'evaluador_id')
                                    ->where('valores_campos.valor', auth()->user()->id);
                            });
                        });
                    })
                    ->selectRaw('*, CONCAT("GRA-00", id) as formatted_id')
                    ->orderBy('id', 'desc');
            }

            return DataTables::of($solicitudes)
                ->addColumn('actions', function ($solicitud) {
                    $buttons = '<div class="flex items-center justify-center container-actions">';

                    $buttons .= '<button type="button" onclick="openDetailsModal(' . $solicitud->id . ')" 
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

                    if (auth()->user()->can('reply_proyecto_grado') && $solicitud->estado == 'Pendiente') {
                        $buttons .= '<button type="button" id="replySolicitudButton" onclick="openReplySolicitudModal(' . $solicitud->id . ')" 
                                    class="btn-action shadow bg-uts-500 hover:bg-uts-800 text-white px-3 py-1 rounded-lg">
                                    <i class="fa-solid fa-share"></i>
                                </button>';
                    }

                    if (!$solicitud->vencido && !$solicitud->deshabilitado) {
                        if (str_contains($solicitud->estado, 'Fase') || str_contains($solicitud->estado, 'Finalizado')) {

                            $acceso = self::esBeneficiarioIcfesLista($solicitud);

                            if (!$acceso) {
                                $buttons .= '
                                    <form class="roadMapForm flex" id="roadmapForm-' . $solicitud->id . '" method="POST" onsubmit="openRoadMap(event, ' . $solicitud->id . ', \'' . self::getActionFormRoadMap() . '\')">
                                        ' . csrf_field() . '
                                        <input type="hidden" name="solicitud_id" value="' . $solicitud->id . '" />
                                        <button type="submit" id="roadMapButton-' . $solicitud->id . '"
                                            class="btn-action shadow bg-indigo-500 hover:bg-indigo-800 text-white px-3 py-1 rounded-lg">
                                            <i class="fa-solid fa-map-location-dot"></i>
                                            <svg id="loadingSpinnerRoadMap-' . $solicitud->id . '" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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

                        if (str_contains($solicitud->estado, 'Fase') && auth()->user()->hasRole(['super_admin', 'admin'])) {
                            $buttons .= '
                                <button 
                                    type="submit"
                                    class="btn-action shadow bg-red-500 hover:bg-red-800 text-white px-3 py-1 rounded-lg relative"
                                    onclick="openDesactivarProyectoModal(' . $solicitud->id  . ')">
                                    <i class="fa-regular fa-circle-xmark"></i>
                                </button>
                            ';
                        }
                    } else {
                        if (auth()->user()->hasRole(['super_admin', 'admin'])) {
                            $buttons .= '
                                <button 
                                    type="submit"
                                    class="btn-action shadow bg-teal-500 hover:bg-teal-800 text-white px-3 py-1 rounded-lg relative"
                                    onclick="openActivarProyectoModal(' . $solicitud->id  . ')">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </button>';
                        }
                    }

                    $buttons .= '</div>';

                    return $buttons;
                })
                ->addColumn('estado', function ($solicitud) {
                    $estado = $solicitud->estado;
                    $return_html = '<div class="flex gap-2 flex-wrap items-center justify-center">';
                    $acceso = self::esBeneficiarioIcfesLista($solicitud);

                    if (str_contains($estado, 'Fase')) {
                        $estado_array = explode(' ', $estado);
                        $estado = $estado_array[1];
                        $return_html = '<div class="flex gap-2 flex-wrap items-center justify-center">';

                        $badge_estado = '<span class="shadow bg-uts-300 text-sm font-medium px-2.5 py-0.5 rounded border border-uts-500">' . $solicitud->estado . '</span>';
                        $badge_estudiante = '<span class="shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300">Estudiante</span>';
                        $badge_director = '<span class="shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300">Director</span>';
                        $badge_evaluador = '<span class="shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300">Evaluador</span>';
                        $badge_comite = '<span class="shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300">Comité</span>';

                        $badge_beneficiario_icfes = '<span class="shadow bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded border border-blue-300">Beneficiario ICFES</span>';

                        $campos_valores = $solicitud->camposConValores();

                        $submited_fase1 = self::findCampoByName($campos_valores, 'submited') === null ? "false" : self::findCampoByName($campos_valores, 'submited');
                        $submited_fase2 = self::findCampoByName($campos_valores, 'submited_fase2') === null ? "false" : self::findCampoByName($campos_valores, 'submited_fase2');
                        $submited_fase3_director = self::findCampoByName($campos_valores, 'submited_fase3_director') === null ? "false" : self::findCampoByName($campos_valores, 'submited_fase3_director');
                        $submited_fase3_evaluador = self::findCampoByName($campos_valores, 'submited_fase3_evaluador') === null ? "false" : self::findCampoByName($campos_valores, 'submited_fase3_evaluador');
                        $submited_fase4 = self::findCampoByName($campos_valores, 'submited_fase4') === null ? "false" : self::findCampoByName($campos_valores, 'submited_fase4');
                        $submited_fase5_director = self::findCampoByName($campos_valores, 'submited_fase5_director') === null ? "false" : self::findCampoByName($campos_valores, 'submited_fase5_director');
                        $submited_fase5_evaluador = self::findCampoByName($campos_valores, 'submited_fase5_evaluador') === null ? "false" : self::findCampoByName($campos_valores, 'submited_fase5_evaluador');

                        switch ($estado) {
                            case 1:
                                if ($submited_fase1 == "true") {
                                    $return_html .= $badge_estado . $badge_comite;
                                } else {
                                    $return_html .= $badge_estado . $badge_estudiante;
                                }
                                break;
                            case 2:
                                if ($submited_fase2 == "true") {
                                    $return_html .= $badge_estado . $badge_director;
                                } else {
                                    $return_html .= $badge_estado . $badge_estudiante;
                                }
                                break;
                            case 3:
                                if ($submited_fase3_director === "true" && $submited_fase3_evaluador === "true") {
                                    $return_html .= $badge_estado . $badge_comite;
                                } else if ($submited_fase3_director == "true") {
                                    $return_html .= $badge_estado . $badge_evaluador;
                                } else {
                                    $return_html .= $badge_estado . $badge_director;
                                }
                                break;
                            case 4:
                                if ($submited_fase4 == "true") {
                                    $return_html .= $badge_estado . $badge_director;
                                } else {
                                    $return_html .= $badge_estado . $badge_estudiante;
                                }
                                break;
                            case 5:
                                if ($submited_fase5_director == "true" && $submited_fase5_evaluador == "true") {
                                    $return_html .= $badge_estado . $badge_comite;
                                } else if ($submited_fase5_director == "true") {
                                    $return_html .= $badge_estado . $badge_evaluador;
                                } else {
                                    $return_html .= $badge_estado . $badge_director;
                                }
                                break;
                            default:
                                $return_html .= $badge_estado;
                                break;
                        }

                        if ($solicitud->vencido) {
                            $return_html .= '<span class="shadow bg-red-100 text-red-800 text-sm font-medium px-2.5 py-0.5 rounded border border-red-300">Vencido</span>';
                        }

                        if ($solicitud->deshabilitado) {
                            $return_html .= '<span class="shadow bg-red-100 text-red-800 text-sm font-medium px-2.5 py-0.5 rounded border border-red-300">Deshabilitado</span>';
                        }

                        if ($acceso && auth()->user()->hasRole('estudiante')) {
                            $return_html .= $badge_beneficiario_icfes;
                        }

                        $return_html .= '</div>';
                        return $return_html;
                    }

                    if ($estado === 'Finalizado') {
                        $return_html .= '<span class="shadow bg-green-100 text-green-800 text-sm font-medium px-2.5 py-0.5 rounded border border-green-300">' . $solicitud->estado . '</span>';
                    }

                    if ($estado === 'Pendiente') {
                        $return_html .= '<span class="shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300">' . $solicitud->estado . '</span>';
                        $return_html .= '<span class="shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300">Comité</span>';
                    }

                    if ($estado === 'Rechazado' || $estado === 'Rechazada') {
                        $return_html .= '<span class="shadow bg-red-100 text-red-800 text-sm font-medium px-2.5 py-0.5 rounded border border-red-300">' . $solicitud->estado . '</span>';
                    }

                    if ($solicitud->vencido) {
                        $return_html .= '<span class="shadow bg-red-100 text-red-800 text-sm font-medium px-2.5 py-0.5 rounded border border-red-300">Vencido</span>';
                    }

                    if ($solicitud->deshabilitado) {
                        $return_html .= '<span class="shadow bg-red-100 text-red-800 text-sm font-medium px-2.5 py-0.5 rounded border border-red-300">Deshabilitado</span>';
                    }

                    $return_html .= '</div>';
                    return $return_html;
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->input('search.value'))) {
                        $searchValue = $request->input('search.value');

                        $query->where(function ($q) use ($searchValue) {
                            // Búsqueda en valores_campos usando joins
                            $q->whereHas('valoresCampos', function ($q) use ($searchValue) {
                                $q->whereHas('campo', function ($q) use ($searchValue) {
                                    $q->where(function ($q) use ($searchValue) {
                                        $q->whereIn('name', ['titulo', 'periodo'])
                                            ->where('valores_campos.valor', 'LIKE', "%{$searchValue}%");
                                    });
                                    $q->orWhere(function ($q) use ($searchValue) {
                                        // Para Niveles
                                        $q->where('name', '=', 'nivel')
                                            ->whereIn(
                                                'valores_campos.valor',
                                                Nivel::select('id')
                                                    ->where('nombre', 'LIKE', "%{$searchValue}%")
                                                    ->pluck('id')
                                            );
                                    });
                                    $q->orWhere(function ($q) use ($searchValue) {
                                        // Para modalidad
                                        $q->where('name', '=', 'modalidad')
                                            ->whereIn(
                                                'valores_campos.valor',
                                                Modalidad::select('id')
                                                    ->where('nombre', 'LIKE', "%{$searchValue}%")
                                                    ->pluck('id')
                                            );
                                    });
                                    $q->orWhere(function ($q) use ($searchValue) {
                                        // Para integrantes
                                        $q->whereIn('name', ['id_integrante_1', 'id_integrante_2', 'id_integrante_3'])
                                            ->whereIn(
                                                'valores_campos.valor',
                                                User::select('id')
                                                    ->where('name', 'LIKE', "%{$searchValue}%")
                                                    ->orWhere('nro_documento', 'LIKE', "%{$searchValue}%")
                                                    ->orWhere('email', 'LIKE', "%{$searchValue}%")
                                                    ->pluck('id')
                                            );
                                    });
                                    $q->orWhere(function ($q) use ($searchValue) {
                                        // Para título en banco
                                        $q->where('name', '=', 'idea_banco')
                                            ->whereIn(
                                                'valores_campos.valor',
                                                Solicitud::select('id')
                                                    ->whereHas('valoresCampos.campo', function ($query) use ($searchValue) {
                                                        $query->where('name', '=', 'titulo')
                                                            ->whereHas('valoresCampos', function ($q) use ($searchValue) {
                                                                $q->where('valor', 'LIKE', "%{$searchValue}%");
                                                            });
                                                    })
                                                    ->pluck('id')
                                            );
                                    });
                                });
                            })

                                // Búsqueda en id
                                ->orWhereRaw('CONCAT("GRA-00", solicitudes.id) LIKE ?', ["%{$searchValue}%"])
                                //Búsqueda en estado
                                ->orWhere('solicitudes.estado', 'LIKE', "%{$searchValue}%")
                                // Búsqueda en nombre de usuario
                                ->orWhereHas('user', function ($q) use ($searchValue) {
                                    $q->where('name', 'LIKE', "%{$searchValue}%")
                                        ->orWhere('email', 'LIKE', "%{$searchValue}%");
                                });
                        });
                    }

                    if ($request->has('comite_filter') && $request->input('comite_filter') === 'true') {
                        $query->where(function ($q) {
                            $q->where('solicitudes.estado', '!=', 'Finalizado')
                                ->where('solicitudes.vencido', '=', false)
                                ->where('solicitudes.deshabilitado', '=', false)
                                ->where(function ($subQuery) {
                                    // Condiciones para Fase 1
                                    $subQuery->where(function ($phase1) {
                                        $phase1->where('solicitudes.estado', '=', 'Fase 1')
                                            ->whereHas('valoresCampos', function ($vc) {
                                                $vc->whereHas('campo', function ($campo) {
                                                    $campo->where('name', 'submited');
                                                })->where('valor', 'true');
                                            });
                                    })
                                        // O condiciones para Fase 3
                                        ->orWhere(function ($phase3) {
                                            $phase3->where('solicitudes.estado', '=', 'Fase 3')
                                                ->whereHas('valoresCampos', function ($vc) {
                                                    $vc->whereHas('campo', function ($campo) {
                                                        $campo->where('name', 'submited_fase3_evaluador');
                                                    })->where('valor', 'true');
                                                });
                                        })
                                        // O condiciones para Fase 5
                                        ->orWhere(function ($phase5) {
                                            $phase5->where('solicitudes.estado', '=', 'Fase 5')
                                                ->whereHas('valoresCampos', function ($vc) {
                                                    $vc->whereHas('campo', function ($campo) {
                                                        $campo->where('name', 'submited_fase5_evaluador');
                                                    })->where('valor', 'true');
                                                });
                                        });
                                })
                                ->orWhere('solicitudes.estado', 'Pendiente');
                        });
                    }

                    if ($request->has('director_filter') && $request->input('director_filter') === 'true') {
                        $query->where(function ($q) {
                            $q->where('solicitudes.estado', '!=', 'Finalizado')
                                ->where('solicitudes.vencido', '=', false)
                                ->where('solicitudes.deshabilitado', '=', false)
                                ->where(function ($subQuery) {
                                    // Condiciones específicas para Fase 2
                                    $subQuery->where(function ($phase2) {
                                        $phase2->where('solicitudes.estado', '=', 'Fase 2')
                                            ->whereHas('valoresCampos', function ($vc) {
                                                $vc->whereHas('campo', function ($campo) {
                                                    $campo->where('name', 'submited_fase2');
                                                })->where('valor', 'true');
                                            })
                                            ->where(function ($q) {
                                                $q->whereDoesntHave('valoresCampos', function ($vc) {
                                                    $vc->whereHas('campo', function ($campo) {
                                                        $campo->where('name', 'submited_fase3_director');
                                                    });
                                                })
                                                    ->orWhereHas('valoresCampos', function ($vc) {
                                                        $vc->whereHas('campo', function ($campo) {
                                                            $campo->where('name', 'submited_fase3_director');
                                                        })->where('valor', 'false');
                                                    });
                                            });
                                    })
                                        // O condiciones para Fase 4
                                        ->orWhere(function ($phase4) {
                                            $phase4->where('solicitudes.estado', '=', 'Fase 4')
                                                ->whereHas('valoresCampos', function ($vc) {
                                                    $vc->whereHas('campo', function ($campo) {
                                                        $campo->where('name', 'submited_fase4');
                                                    })->where('valor', 'true');
                                                })
                                                ->where(function ($q) {
                                                    $q->whereDoesntHave('valoresCampos', function ($vc) {
                                                        $vc->whereHas('campo', function ($campo) {
                                                            $campo->where('name', 'submited_fase5_director');
                                                        });
                                                    })
                                                        ->orWhereHas('valoresCampos', function ($vc) {
                                                            $vc->whereHas('campo', function ($campo) {
                                                                $campo->where('name', 'submited_fase5_director');
                                                            })->where('valor', 'false');
                                                        });
                                                });
                                        });
                                });
                        });
                    }

                    if ($request->has('evaluador_filter') && $request->input('evaluador_filter') === 'true') {
                        $query->where(function ($q) {
                            $q->where('solicitudes.estado', '!=', 'Finalizado')
                                ->where('solicitudes.vencido', '=', false)
                                ->where('solicitudes.deshabilitado', '=', false)
                                ->where(function ($subQuery) {
                                    // Condiciones específicas para Fase 3
                                    $subQuery->where(function ($phase3) {
                                        $phase3->where('solicitudes.estado', '=', 'Fase 3')
                                            ->whereHas('valoresCampos', function ($vc) {
                                                $vc->whereHas('campo', function ($campo) {
                                                    $campo->where('name', 'submited_fase3_director');
                                                })->where('valor', 'true');
                                            })
                                            ->where(function ($q) {
                                                $q->whereDoesntHave('valoresCampos', function ($vc) {
                                                    $vc->whereHas('campo', function ($campo) {
                                                        $campo->where('name', 'submited_fase3_evaluador');
                                                    });
                                                })
                                                    ->orWhereHas('valoresCampos', function ($vc) {
                                                        $vc->whereHas('campo', function ($campo) {
                                                            $campo->where('name', 'submited_fase3_evaluador');
                                                        })->where('valor', 'false');
                                                    });
                                            });
                                    })
                                        // O condiciones para Fase 5
                                        ->orWhere(function ($phase5) {
                                            $phase5->where('solicitudes.estado', '=', 'Fase 5')
                                                ->whereHas('valoresCampos', function ($vc) {
                                                    $vc->whereHas('campo', function ($campo) {
                                                        $campo->where('name', 'submited_fase5_director');
                                                    })->where('valor', 'true');
                                                })
                                                ->where(function ($q) {
                                                    $q->whereDoesntHave('valoresCampos', function ($vc) {
                                                        $vc->whereHas('campo', function ($campo) {
                                                            $campo->where('name', 'submited_fase5_evaluador');
                                                        });
                                                    })
                                                        ->orWhereHas('valoresCampos', function ($vc) {
                                                            $vc->whereHas('campo', function ($campo) {
                                                                $campo->where('name', 'submited_fase5_evaluador');
                                                            })->where('valor', 'false');
                                                        });
                                                });
                                        });
                                });
                        });
                    }

                    if ($request->has('propuestas_pendientes') && $request->input('propuestas_pendientes') === 'true') {
                        $query->where(function ($q) {
                            $q->where('solicitudes.estado', '=', 'Fase 1')
                                ->orWhere('solicitudes.estado', '=', 'Fase 2')
                                ->orWhere('solicitudes.estado', '=', 'Fase 3');
                        })
                            ->where('solicitudes.vencido', '=', false)
                            ->where('solicitudes.deshabilitado', '=', false);
                    }

                    if ($request->has('informes_pendientes') && $request->input('informes_pendientes') === 'true') {
                        $query->where(function ($q) {
                            $q->where('solicitudes.estado', '=', 'Fase 4')
                                ->orWhere('solicitudes.estado', '=', 'Fase 5');
                        })
                            ->where('solicitudes.vencido', '=', false)
                            ->where('solicitudes.deshabilitado', '=', false);
                    }

                    if ($request->has('proyectos_finalizados') && $request->input('proyectos_finalizados') === 'true') {
                        $query->where('solicitudes.estado', '=', 'Finalizado')
                            ->where('solicitudes.vencido', '=', false)
                            ->where('solicitudes.deshabilitado', '=', false);
                    }

                    if ($request->has('proyectos_vencidos') && $request->input('proyectos_vencidos') === 'true') {
                        $query->where('solicitudes.vencido', '=', true)
                            ->where('solicitudes.deshabilitado', '=', false);
                    }

                    if ($request->has('proyectos_deshabilitados') && $request->input('proyectos_deshabilitados') === 'true') {
                        $query->where('solicitudes.deshabilitado', '=', true);
                    }
                })
                ->rawColumns(['estado', 'actions'])
                ->make(true);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function esBeneficiarioIcfesLista($solicitud)
    {
        if (auth()->user()->hasRole('estudiante')) {
            $campos_valores = $solicitud->camposConValores();
            $beneficiarios_icfes = self::findCampoByName($campos_valores, 'beneficiarios_icfes');

            if ($beneficiarios_icfes) {
                $beneficiarios_icfes = json_decode($beneficiarios_icfes, true);
                return in_array(auth()->user()->id, $beneficiarios_icfes);
            }

            return false;
        }

        return false;
    }

    public function tieneProyectosEnCurso($valor): bool
    {
        $type = null;
        $solicitudes = null;

        try {
            $type = self::getType('fase_0');
            $solicitudes = Solicitud::query()->where('tipo_solicitud_id', '=', $type->id)->get();

            foreach ($solicitudes as $solicitud) {
                if (!$solicitud->vencido && !$solicitud->deshabilitado) {
                    if (str_contains($solicitud->estado, 'Fase') || str_contains($solicitud->estado, 'Pendiente')) {
                        $campos = $solicitud->CamposConValores();
                        foreach ($campos as $item) {
                            if (str_contains($item['campo']['name'], 'id_integrante')) {
                                if ($valor == $item['valor']) {
                                    return true;
                                }
                            }
                        }
                    }
                }
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function deshabilitarProyecto(Request $request)
    {
        $proyecto = null;

        try {
            $validator = Validator::make($request->all(), [
                'proyecto_id' => 'required',
                'nro_acta_desactivar' => 'required|numeric',
                'fecha_acta_desactivar' => 'required|date',
                'descripcion_desactivar' => 'required',
            ], [
                'nro_acta_desactivar.numeric' => 'El número de acta debe ser un número.',
                'nro_acta_desactivar.required' => 'El número de acta es obligatorio.',
                'fecha_acta_desactivar.date' => 'La fecha del acta debe ser una fecha válida.',
                'fecha_acta_desactivar.required' => 'La fecha del acta es obligatoria.',
                'descripcion_desactivar.required' => 'La descripción es obligatoria.'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $proyecto = Solicitud::findOrFail($request->input('proyecto_id'));

            $proyecto->update([
                'deshabilitado' => true
            ]);

            $acta = Acta::create([
                'numero' => $request->nro_acta_desactivar,
                'fecha' => $request->fecha_acta_desactivar,
                'descripcion' => "Desactivación del proyecto",
                'proyecto_id' => $request->input('proyecto_id')
            ]);

            $acta->save();

            // Consultar los integrantes del proyecto
            $campos_proyecto = $proyecto->camposConValores();

            $id_integrante_1 = self::findCampoByName($campos_proyecto, 'id_integrante_1') ?? null;
            $id_integrante_2 = self::findCampoByName($campos_proyecto, 'id_integrante_2') ?? null;
            $id_integrante_3 = self::findCampoByName($campos_proyecto, 'id_integrante_3') ?? null;

            if (isset($id_integrante_1) || isset($id_integrante_2) || isset($id_integrante_3)) {
                // Se envía el correo a los estudiantes
                Mail::queue(new DeshabilitarProyectoMail($proyecto->id, $acta, $request->descripcion_desactivar, 'estudiantes'));
            }

            // Se envía el correo al director
            $director = User::query()->where('id', self::findCampoByName($proyecto->camposConValores(), 'director_id'))->first();
            if ($director) {
                Mail::queue(new DeshabilitarProyectoMail($proyecto->id, $acta, $request->descripcion_desactivar, 'director'));
            }

            // Se envía el correo al evaluador
            $evaluador = User::query()->where('id', self::findCampoByName($proyecto->camposConValores(), 'evaluador_id'))->first();
            if ($evaluador) {
                Mail::queue(new DeshabilitarProyectoMail($proyecto->id, $acta, $request->descripcion_desactivar, 'evaluador'));
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function habilitarProyecto(Request $request)
    {
        $proyecto = null;

        try {
            $validator = Validator::make($request->all(), [
                'proyecto_id_activar' => 'required',
                'nro_acta_activar' => 'required|numeric',
                'fecha_acta_activar' => 'required|date',
                'descripcion_activar' => 'required',
            ], [
                'nro_acta_activar.numeric' => 'El número de acta debe ser un número.',
                'nro_acta_activar.required' => 'El número de acta es obligatorio.',
                'fecha_acta_activar.date' => 'La fecha del acta debe ser una fecha válida.',
                'fecha_acta_activar.required' => 'La fecha del acta es obligatoria.',
                'descripcion_activar.required' => 'La descripción es obligatoria.'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $proyecto = Solicitud::findOrFail($request->input('proyecto_id_activar'));
            $campos_proyecto = $proyecto->camposConValores();

            $integrante_1 = User::query()->where('id', $this->findCampoByName($campos_proyecto, 'id_integrante_1'))->first();
            $integrante_2 = User::query()->where('id', $this->findCampoByName($campos_proyecto, 'id_integrante_2'))->first();
            $integrante_3 = User::query()->where('id', $this->findCampoByName($campos_proyecto, 'id_integrante_3'))->first();

            $proyecto_en_curso = [];

            if ($integrante_1) {
                $proyecto_en_curso[] = self::tieneProyectosEnCurso($integrante_1->id);
            }

            if ($integrante_2) {
                $proyecto_en_curso[] = self::tieneProyectosEnCurso($integrante_2->id);
            }

            if ($integrante_3) {
                $proyecto_en_curso[] = self::tieneProyectosEnCurso($integrante_3->id);
            }

            if (in_array(true, $proyecto_en_curso)) {
                return response()->json(['message' => 'Alguno de los integrantes de este proyecto ya tiene otro proyecto en curso, por lo tanto no se puede habilitar este proyecto.'], 422);
            }

            $proyecto->update([
                'deshabilitado' => false
            ]);

            $acta = Acta::create([
                'numero' => $request->nro_acta_activar,
                'fecha' => $request->fecha_acta_activar,
                'descripcion' => "Activación del proyecto",
                'proyecto_id' => $request->input('proyecto_id_activar')
            ]);

            $acta->save();

            // Se envía el correo a los estudiantes
            Mail::queue(new HabilitarProyectoMail($proyecto->id, $acta, $request->descripcion_activar, 'estudiantes'));

            // Se envía el correo al director
            $director = User::query()->where('id', self::findCampoByName($proyecto->camposConValores(), 'director_id'))->first();
            if ($director) {
                Mail::queue(new HabilitarProyectoMail($proyecto->id, $acta, $request->descripcion_activar, 'director'));
            }

            // Se envía el correo al evaluador
            $evaluador = User::query()->where('id', self::findCampoByName($proyecto->camposConValores(), 'evaluador_id'))->first();
            if ($evaluador) {
                Mail::queue(new HabilitarProyectoMail($proyecto->id, $acta, $request->descripcion_activar, 'evaluador'));
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function sendEmailFinalizado($request, $caso = "comite", $adjunto = null)
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

    public function store(Request $request)
    {
        $type = null;
        $campos = null;
        $rules = [];

        try {
            $type = self::getType('fase_0');
            $campos = Campo::query()->where('tipo_solicitud_id', '=', $type->id)->where('deleted_at', '=', NULL)->get();

            foreach ($campos as $campo) {
                $rules[$campo->name] = $campo->required ? ['required'] : ['nullable'];
            }

            if ($request->has('id_integrante_1')) {
                $rules['id_integrante_1'][] = new ProyectoEnCurso(auth()->user()->id);
            }

            if ($request->has('id_integrante_2')) {
                $rules['id_integrante_2'][] = new ProyectoEnCurso($request->input('id_integrante_2'));
            }

            if ($request->has('id_integrante_3')) {
                $rules['id_integrante_3'][] = new ProyectoEnCurso($request->input('id_integrante_3'));
            }

            if ($request->has('id_integrante_2') && $request->has('id_integrante_3')) {
                $rules['id_integrante_3'][] = function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('id_integrante_2') == $value) {
                        $fail('Los integrantes no pueden ser iguales.');
                    }
                };
            }

            $validator = Validator::make($request->all(), $rules);

            $validator->after(function ($validator) use ($request) {
                if ($request->has('id_integrante_3') && !$request->has('id_integrante_2')) {
                    $validator->errors()->add('id_integrante_2', 'Debe seleccionar primero este integrante.');
                }
            });

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $solicitud = Solicitud::create([
                'user_id' => auth()->user()->id,
                'tipo_solicitud_id' => $type->id,
                'descripcion' => 'Propuesta de estudiante para iniciar con proyecto de grado'
            ]);

            foreach ($campos as $campo) {
                if ($request->has($campo->name)) {
                    if ($campo->name == 'id_integrante_1') {
                        ValorCampo::create([
                            'solicitud_id' => $solicitud->id,
                            'campo_id' => $campo->id,
                            'valor' => auth()->user()->id
                        ]);
                    } else if ($campo->name == 'nivel') {
                        ValorCampo::create([
                            'solicitud_id' => $solicitud->id,
                            'campo_id' => $campo->id,
                            'valor' => auth()->user()->nivel_id
                        ]);
                    } else {
                        ValorCampo::create([
                            'solicitud_id' => $solicitud->id,
                            'campo_id' => $campo->id,
                            'valor' => $request->input($campo->name)
                        ]);
                    }
                }
            }

            // Enviar correo a estudiantes y administradores
            $campos_nuevo_proyecto = $solicitud->camposConValores();
            self::sendEmailProyectoNuevo($campos_nuevo_proyecto);

            return response()->json(['success' => 'Solicitud enviada exitosamente']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function sendEmailProyectoNuevo($campos)
    {
        try {
            $cuerpo_correo = [];
            $correos_destinatarios = [];
            $asunto_correo = 'PROYECTO DE GRADO - FASE 0';
            $tipo_correo = 'fase_0';
            $comentarios = null;
            $esRespuesta = false;

            foreach ($campos as $campo) {
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

            Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));
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
            $campos_proyecto = $solicitud->camposConValores();

            if ($request->post('estado') === 'Aprobada') {
                $solicitud->estado = 'Fase 1';
            } else {
                $solicitud->estado = $request->post('estado');
            }

            $solicitud->save();

            // Enviar correo de la respuesta al estudiante
            self::sendEmailProyectoRespuesta($campos_proyecto, $request->estado, $request->mensaje);

            return response()->json(['success' => 'Respuesta enviada exitosamente']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function sendEmailProyectoRespuesta($campos, $estado, $mensaje)
    {
        try {
            $cuerpo_correo = [];
            $correos_destinatarios = [];
            $asunto_correo = 'PROYECTO DE GRADO - FASE 0';
            $tipo_correo = 'respuesta_fase_0';
            $comentarios = $mensaje;
            $esRespuesta = true;

            foreach ($campos as $campo) {
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

            $cuerpo_correo['estado'] = $estado;

            Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function configEstudiante(Request $request)
    {
        $validator = null;

        try {

            if (isset($request->tipo_solicitud)) {
                if ($request->tipo_solicitud == 'prorroga') {
                    $validator = Validator::make($request->all(), [
                        'solicitud_id' => 'required',
                        'tipo_solicitud' => 'required',
                        'carta_prorroga' => 'required',
                        'carta_prorroga.*' => 'required',
                        'doc_prorroga' => 'required',
                        'doc_prorroga.*' => 'required',
                        'comentarios_config' => 'required|string',
                    ], [
                        'tipo_solicitud.required' => 'El campo tipo de solicitud es obligatorio',
                        'doc_prorroga.required' => 'El soporte de la prórroga es obligatorio',
                        'doc_prorroga.*.required' => 'El soporte de la prórroga es obligatorio',
                        'carta_prorroga.required' => 'La carta de prórroga es obligatoria',
                        'carta_prorroga.*.required' => 'La carta de prórroga es obligatoria',
                        'comentarios_config.required' => 'El campo comentarios es obligatorio',
                        'comentarios_config.string' => 'El campo comentarios debe ser una cadena de texto',
                    ]);
                } else if ($request->tipo_solicitud == 'retiro') {
                    $validator = Validator::make($request->all(), [
                        'solicitud_id' => 'required',
                        'tipo_solicitud' => 'required',
                        'doc_retiro' => 'required',
                        'doc_retiro.*' => 'required',
                        'comentarios_config' => 'required|string',
                    ], [
                        'tipo_solicitud.required' => 'El campo tipo de solicitud es obligatorio',
                        'doc_retiro.required' => 'La carta de retiro es obligatoria',
                        'doc_retiro.*.required' => 'La carta de retiro es obligatoria',
                        'comentarios_config.required' => 'El campo comentarios es obligatorio',
                        'comentarios_config.string' => 'El campo comentarios debe ser una cadena de texto',
                    ]);
                } else {
                    $validator = Validator::make($request->all(), [
                        'solicitud_id' => 'required',
                        'tipo_solicitud' => 'required',
                        'comentarios_config' => 'required|string',
                    ], [
                        'tipo_solicitud.required' => 'El campo tipo de solicitud es obligatorio',
                        'comentarios_config.required' => 'El campo comentarios es obligatorio',
                        'comentarios_config.string' => 'El campo comentarios debe ser una cadena de texto',
                    ]);
                }
            } else {
                $validator = Validator::make($request->all(), [
                    'solicitud_id' => 'required',
                    'tipo_solicitud' => 'required',
                    'comentarios_config' => 'required|string',
                ], [
                    'tipo_solicitud.required' => 'El campo tipo de solicitud es obligatorio',
                    'comentarios_config.required' => 'El campo comentarios es obligatorio',
                    'comentarios_config.string' => 'El campo comentarios debe ser una cadena de texto',
                ]);
            }

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            if ($request->tipo_solicitud == 'prorroga') {
                $nombreUnico = null;
                $adjuntos = [];

                if ($request->hasFile('doc_prorroga')) {
                    $nombresArchivos = [];
                    foreach ($request->file('doc_prorroga') as $archivo) {
                        $extension = $archivo->getClientOriginalExtension();
                        $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
                        $archivo->storeAs('documentos_proyectos/proyecto-00' . $request->solicitud_id, $nombreUnico, 'public');
                        $nombresArchivos[] = $nombreUnico;
                    }
                }

                $adjuntos[] = public_path("storage/documentos_proyectos/proyecto-00{$request->solicitud_id}/" . $nombreUnico);

                $nombreUnico = null;

                if ($request->hasFile('carta_prorroga')) {
                    $nombresArchivos = [];
                    foreach ($request->file('carta_prorroga') as $archivo) {
                        $extension = $archivo->getClientOriginalExtension();
                        $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
                        $archivo->storeAs('documentos_proyectos/proyecto-00' . $request->solicitud_id, $nombreUnico, 'public');
                        $nombresArchivos[] = $nombreUnico;
                    }
                }

                $adjuntos[] = public_path("storage/documentos_proyectos/proyecto-00{$request->solicitud_id}/" . $nombreUnico);

                self::sendEmail($request, 'prorroga', null, $adjuntos);
            } else if ($request->tipo_solicitud == 'retiro') {
                $nombreUnico = null;
                $adjuntos = [];

                if ($request->hasFile('doc_retiro')) {
                    $nombresArchivos = [];
                    foreach ($request->file('doc_retiro') as $archivo) {
                        $extension = $archivo->getClientOriginalExtension();
                        $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
                        $archivo->storeAs('documentos_proyectos/proyecto-00' . $request->solicitud_id, $nombreUnico, 'public');
                        $nombresArchivos[] = $nombreUnico;
                    }
                }

                $adjuntos[] = public_path("storage/documentos_proyectos/proyecto-00{$request->solicitud_id}/" . $nombreUnico);

                $data_old['estudiante'] = User::query()->where('id', auth()->id())->first();

                $proyecto = Solicitud::query()->where('id', '=', $request->solicitud_id)->first();
                $campos_proyecto = $proyecto->camposConValores();

                $submited_retiro = self::findCampoByName($campos_proyecto, 'submited_retiro');

                if (isset($submited_retiro)) {
                    $submited_retiro = json_decode($submited_retiro, true);
                    if (!in_array(auth()->id(), $submited_retiro)) {
                        $submited_retiro[] = auth()->id();
                    }
                } else {
                    $submited_retiro = [auth()->id()];
                }

                $campo_submited_retiro = Campo::where('name', '=', 'submited_retiro')->firstOrFail();

                ValorCampo::UpdateOrCreate(
                    [
                        'solicitud_id' => $request->solicitud_id,
                        'campo_id' => $campo_submited_retiro->id
                    ],
                    [
                        'valor' => json_encode($submited_retiro)
                    ]
                );

                self::sendEmail($request, 'retiro', $data_old, $adjuntos);
            } else {
                self::sendEmail($request, 'estudiante');
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function configAdmin(Request $request)
    {
        $validator = null;

        try {
            $solicitud = Solicitud::query()->where('id', '=', $request->solicitud_id)->first();

            $validator = Validator::make($request->all(), [
                'solicitud_id' => 'required',
                'retirar_estudiante' => 'nullable',
                'nro_acta_ajustes' => 'required|numeric',
                'fecha_acta_ajustes' => 'required|date',
                'comentarios_config_admin' => 'required|string',
            ], [
                'solicitud_id.required' => 'El campo solicitud es obligatorio',
                'nro_acta_ajustes.required' => 'El campo número de acta es obligatorio',
                'nro_acta_ajustes.numeric' => 'El campo número de acta debe ser un número',
                'fecha_acta_ajustes.required' => 'El campo fecha de acta es obligatorio',
                'fecha_acta_ajustes.date' => 'El campo fecha de acta debe ser una fecha válida',
                'comentarios_config_admin.required' => 'El campo comentarios es obligatorio',
                'comentarios_config_admin.string' => 'El campo comentarios debe ser una cadena de texto',
            ]);

            if (isset($solicitud)) {
                if ($solicitud->estado == 'Fase 2' || $solicitud->estado == 'Fase 3') {
                    $validator = Validator::make($request->all(), [
                        'solicitud_id' => 'required',
                        'director_id' => 'required',
                        'evaluador_id' => 'required',
                        'retirar_estudiante' => 'nullable',
                        'nro_acta_ajustes' => 'required|numeric',
                        'fecha_acta_ajustes' => 'required|date',
                        'comentarios_config_admin' => 'required|string',
                    ], [
                        'solicitud_id.required' => 'El campo solicitud es obligatorio',
                        'director_id.required' => 'El campo director es obligatorio',
                        'evaluador_id.required' => 'El campo evaluador es obligatorio',
                        'nro_acta_ajustes.required' => 'El campo número de acta es obligatorio',
                        'nro_acta_ajustes.numeric' => 'El campo número de acta debe ser un número',
                        'fecha_acta_ajustes.required' => 'El campo fecha de acta es obligatorio',
                        'fecha_acta_ajustes.date' => 'El campo fecha de acta debe ser una fecha válida',
                        'comentarios_config_admin.required' => 'El campo comentarios es obligatorio',
                        'comentarios_config_admin.string' => 'El campo comentarios debe ser una cadena de texto',
                    ]);
                }

                if ($solicitud->estado == 'Fase 4' || $solicitud->estado == 'Fase 5') {
                    $validator = Validator::make($request->all(), [
                        'solicitud_id' => 'required',
                        'director_id' => 'required',
                        'evaluador_id' => 'required',
                        'retirar_estudiante' => 'nullable',
                        'fecha_inicio_informe' => 'required',
                        'fecha_maxima_informe' => 'required',
                        'nro_acta_ajustes' => 'required|numeric',
                        'fecha_acta_ajustes' => 'required|date',
                        'comentarios_config_admin' => 'required|string',
                    ], [
                        'director_id.required' => 'El campo director es obligatorio',
                        'evaluador_id.required' => 'El campo evaluador es obligatorio',
                        'fecha_inicio_informe.required' => 'El campo fecha de inicio de informe es obligatorio',
                        'fecha_maxima_informe.required' => 'El campo fecha máxima de informe es obligatorio',
                        'nro_acta_ajustes.required' => 'El campo número de acta es obligatorio',
                        'nro_acta_ajustes.numeric' => 'El campo número de acta debe ser un número',
                        'fecha_acta_ajustes.required' => 'El campo fecha de acta es obligatorio',
                        'fecha_acta_ajustes.date' => 'El campo fecha de acta debe ser una fecha válida',
                        'comentarios_config_admin.required' => 'El campo comentarios es obligatorio',
                        'comentarios_config_admin.string' => 'El campo comentarios debe ser una cadena de texto',
                    ]);
                }

                if (
                    $solicitud->estado == 'Fase 2'
                    || $solicitud->estado == 'Fase 3'
                    || $solicitud->estado == 'Fase 4'
                    || $solicitud->estado == 'Fase 5'
                ) {
                    if ($request->input('director_id') == $request->input('evaluador_id')) {
                        return response()->json(['errors' => ['director_id' => ['El director y el evaluador no pueden ser la misma persona.']]], 422);
                    }
                }
            }

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            if ($request->input('retirar_estudiante')) {
                $campos_proyecto = $solicitud->camposConValores();

                if (isset($campos_proyecto)) {
                    $submited_retiro = self::findCampoByName($campos_proyecto, 'submited_retiro');

                    if (isset($submited_retiro)) {
                        $submited_retiro = json_decode($submited_retiro, true);

                        if (!in_array($request->input('retirar_estudiante'), $submited_retiro)) {
                            return response()->json(['errors' => ['retirar_estudiante' => ['El estudiante seleccionado no ha enviado la solicitud de retiro.']]], 422);
                        }
                    } else {
                        return response()->json(['errors' => ['retirar_estudiante' => ['El estudiante seleccionado aún no ha enviado la solicitud de retiro.']]], 422);
                    }
                }
            }

            DB::transaction(function () use ($request) {
                $data_old = [];

                $solicitud_id = $request->solicitud_id;
                $solicitud = Solicitud::query()->where('id', '=', $solicitud_id)->first();

                $estado_array = explode(' ', $solicitud->estado);
                $estado = $estado_array[1];

                $campos = $solicitud->camposConValores();

                if ($estado > 1) {
                    $fase_1 = self::getType('fase_1');
                    $campos_fase_1 = self::findCamposByTipoSolicitud($campos, $fase_1->id);

                    $director_id = self::findCampoByName($campos_fase_1, 'director_id');
                    $evaluador_id = self::findCampoByName($campos_fase_1, 'evaluador_id');

                    $data_old['director_id'] = $director_id;
                    $data_old['evaluador_id'] = $evaluador_id;

                    $campo_director = self::findCampo($campos_fase_1, 'director_id');
                    ValorCampo::where('solicitud_id', '=', $solicitud->id)
                        ->where('campo_id', '=', $campo_director['id'])
                        ->firstOrFail()
                        ->update(['valor' => $request->director_id]);

                    $campo_evaluador = self::findCampo($campos_fase_1, 'evaluador_id');
                    ValorCampo::where('solicitud_id', '=', $solicitud->id)
                        ->where('campo_id', '=', $campo_evaluador['id'])
                        ->firstOrFail()
                        ->update(['valor' => $request->evaluador_id]);

                    $director_nuevo = User::findOrFail($request->director_id);
                    $director_nuevo->assignRole('director');

                    $evaluador_nuevo = User::findOrFail($request->evaluador_id);
                    $evaluador_nuevo->assignRole('evaluador');

                    $campo_fecha_inicio_informe = self::findCampo($campos, 'fecha_inicio_informe');
                    $campo_fecha_maxima_informe = self::findCampo($campos, 'fecha_maxima_informe');

                    if (isset($campo_fecha_inicio_informe) && isset($campo_fecha_maxima_informe)) {
                        $fecha_inicio_informe = self::findCampoByName($campos, 'fecha_inicio_informe');
                        $data_old['fecha_inicio_informe'] = $fecha_inicio_informe;

                        ValorCampo::where('solicitud_id', '=', $solicitud->id)
                            ->where('campo_id', '=', $campo_fecha_inicio_informe['id'])
                            ->firstOrFail()
                            ->update(['valor' => $request->fecha_inicio_informe]);

                        $fecha_maxima_informe = self::findCampoByName($campos, 'fecha_maxima_informe');
                        $data_old['fecha_maxima_informe'] = $fecha_maxima_informe;

                        ValorCampo::where('solicitud_id', '=', $solicitud->id)
                            ->where('campo_id', '=', $campo_fecha_maxima_informe['id'])
                            ->firstOrFail()
                            ->update(['valor' => $request->fecha_maxima_informe]);
                    }

                    self::sendEmail($request, 'admin', $data_old);
                }

                if ($request->input('retirar_estudiante')) {
                    $estudiante = User::query()->where('id', '=', $request->input('retirar_estudiante'))->first();
                    $data_old['estudiante_retirado'] = $estudiante;

                    $id_integrante_1 = self::findCampoByName($campos, 'id_integrante_1') ?? null;
                    $id_integrante_2 = self::findCampoByName($campos, 'id_integrante_2') ?? null;
                    $id_integrante_3 = self::findCampoByName($campos, 'id_integrante_3') ?? null;

                    if ($id_integrante_1 == $request->input('retirar_estudiante')) {
                        $campo_integrante_1 = Campo::query()->where('name', '=', 'id_integrante_1')->first();
                        ValorCampo::query()
                            ->where('solicitud_id', '=', $solicitud->id)
                            ->where('campo_id', '=', $campo_integrante_1->id)
                            ->first()
                            ->delete();
                    }

                    if ($id_integrante_2 == $request->input('retirar_estudiante')) {
                        $campo_integrante_2 = Campo::query()->where('name', '=', 'id_integrante_2')->first();
                        ValorCampo::query()
                            ->where('solicitud_id', '=', $solicitud->id)
                            ->where('campo_id', '=', $campo_integrante_2->id)
                            ->first()
                            ->delete();
                    }

                    if ($id_integrante_3 == $request->input('retirar_estudiante')) {
                        $campo_integrante_3 = Campo::query()->where('name', '=', 'id_integrante_3')->first();
                        ValorCampo::query()
                            ->where('solicitud_id', '=', $solicitud->id)
                            ->where('campo_id', '=', $campo_integrante_3->id)
                            ->first()
                            ->delete();
                    }

                    $submited_retiro = self::findCampoByName($campos, 'submited_retiro');
                    $campo_submited_retiro = Campo::query()->where('name', '=', 'submited_retiro')->first();

                    if (isset($submited_retiro)) {
                        $submited_retiro = json_decode($submited_retiro, true);

                        if (($key = array_search($request->input('retirar_estudiante'), $submited_retiro)) !== false) {
                            unset($submited_retiro[$key]);

                            $submited_retiro = array_values($submited_retiro);

                            ValorCampo::UpdateOrCreate(
                                [
                                    'solicitud_id' => $solicitud->id,
                                    'campo_id' => $campo_submited_retiro->id
                                ],
                                [
                                    'valor' => json_encode($submited_retiro)
                                ]
                            );
                        }
                    }

                    // Enviar correo
                    self::sendEmail($request, 'admin', $data_old);

                    // Si no quedan integrantes activos, desactivar el proyecto
                    $id_integrante_1 = ValorCampo::query()
                        ->where('solicitud_id', '=', $solicitud->id)
                        ->where('campo_id', '=', Campo::query()->where('name', '=', 'id_integrante_1')->first()->id)
                        ->value('valor') ?? null;

                    $id_integrante_2 = ValorCampo::query()
                        ->where('solicitud_id', '=', $solicitud->id)
                        ->where('campo_id', '=', Campo::query()->where('name', '=', 'id_integrante_2')->first()->id)
                        ->value('valor') ?? null;

                    $id_integrante_3 = ValorCampo::query()
                        ->where('solicitud_id', '=', $solicitud->id)
                        ->where('campo_id', '=', Campo::query()->where('name', '=', 'id_integrante_3')->first()->id)
                        ->value('valor') ?? null;

                    if (is_null($id_integrante_1) && is_null($id_integrante_2) && is_null($id_integrante_3)) {
                        $request_desactivar = new Request();
                        $request_desactivar->merge([
                            'proyecto_id' => $solicitud->id,
                            'nro_acta_desactivar' => $request->nro_acta_ajustes,
                            'fecha_acta_desactivar' => $request->fecha_acta_ajustes,
                            'descripcion_desactivar' => 'Se ha desactivado el proyecto de grado porque no hay integrantes activos.',
                        ]);

                        self::deshabilitarProyecto($request_desactivar);
                    }
                }
            });
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function sendEmail($request, $flag, $data_old = null, $adjunto = null)
    {
        $cuerpo_correo = [];
        $correos_destinatarios = [];
        $asunto_correo = '';
        $tipo_correo = '';
        $comentarios = null;
        $esRespuesta = false;

        try {
            $solicitud_id = $request->solicitud_id;
            $solicitud = Solicitud::query()->where('id', '=', $solicitud_id)->first();
            $estado_array = explode(' ', $solicitud->estado);
            $estado = $estado_array[1];

            $campos = $solicitud->camposConValores();

            $fase_0 = self::getType('fase_0');
            $fase_1 = self::getType('fase_1');

            $campos_fase_0 = self::findCamposByTipoSolicitud($campos, $fase_0->id);
            $campos_fase_1 = self::findCamposByTipoSolicitud($campos, $fase_1->id);
            $check_idea_banco = self::findCampoByName($campos_fase_1, 'check_idea_banco');

            $cuerpo_correo['adjunto'] = $adjunto;

            if ($estado > 1) {
                if ($check_idea_banco === 'false') {
                    $cuerpo_correo['titulo'] = self::findCampoByName($campos, 'titulo');
                } else {
                    $idea_banco_id = self::findCampoByName($campos, 'idea_banco');
                    $idea_banco = Solicitud::query()->where('id', '=', $idea_banco_id)->first();
                    $tipo_solicitud_banco = self::getType('solicitud_banco');
                    $campos_idea_banco = self::findCamposByTipoSolicitud($idea_banco->camposConValores(), $tipo_solicitud_banco->id);

                    $cuerpo_correo['titulo'] = self::findCampoByName($campos_idea_banco, 'titulo');
                }

                $codigo_modalidad = self::findCampoByName($campos, 'codigo_modalidad');
                $modalidad = Modalidad::query()->where('id', '=', self::findCampoByName($campos, 'modalidad'))->first();
                $nivel = Nivel::query()->where('id', '=', self::findCampoByName($campos, 'nivel'))->first();
            }

            foreach ($campos_fase_0 as $item) {
                if (str_contains($item['campo']['name'], 'id_integrante') && isset($item['valor'])) {
                    $integrante = User::findOrFail($item['valor']);
                    $correos_destinatarios[] = $integrante->email;
                }
            }

            $integrante_1 = User::query()->where('id', '=', self::findCampoByName($campos, 'id_integrante_1'))->first();
            $integrante_2 = User::query()->where('id', '=', self::findCampoByName($campos, 'id_integrante_2'))->first();
            $integrante_3 = User::query()->where('id', '=', self::findCampoByName($campos, 'id_integrante_3'))->first();

            $cuerpo_correo['codigo_modalidad'] = $codigo_modalidad ?? 'No disponible';
            $cuerpo_correo['modalidad'] = $modalidad->nombre ?? 'No disponible';
            $cuerpo_correo['nivel'] = $nivel->nombre ?? 'No disponible';

            $cuerpo_correo['integrante_1'] = $integrante_1;
            $cuerpo_correo['integrante_2'] = $integrante_2;
            $cuerpo_correo['integrante_3'] = $integrante_3;

            $cuerpo_correo['nro_acta'] = $request->nro_acta_ajustes ?? 'No disponible';
            $cuerpo_correo['fecha_acta'] = $request->fecha_acta_ajustes ?? 'No disponible';

            $descripcion_acta = "";

            switch ($flag) {
                case 'estudiante':
                    $cuerpo_correo['tipo_solicitud'] = mb_strtoupper($request->tipo_solicitud);
                    $asunto_correo = 'PROYECTO DE GRADO - ' . mb_strtoupper($cuerpo_correo['tipo_solicitud']);
                    $tipo_correo = 'config_estudiante';
                    $cuerpo_correo['solicitud'] = $request->comentarios_config;
                    $esRespuesta = false;

                    Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));
                    break;
                case 'admin':
                    $tipo_correo = 'config_admin';
                    $cuerpo_correo['solicitud'] = $request->comentarios_config_admin;
                    $cuerpo_correo['destinatario'] = 'estudiante';
                    $esRespuesta = true;

                    $cuerpo_correo['solicitud_id'] = $solicitud_id;

                    // 1. SECCIÓN PARA CAMBIOS DE DIRECTOR, EVALUADOR Y PRÓRROGA
                    if ($estado > 1 && $estado < 6) {
                        $director_id_anterior = $data_old['director_id'];
                        $evaluador_id_anterior = $data_old['evaluador_id'];

                        $director_anterior = User::query()->where('id', '=', $director_id_anterior)->first();
                        $evaluador_anterior = User::query()->where('id', '=', $evaluador_id_anterior)->first();

                        $director_id_actual = self::findCampoByName($campos, 'director_id');
                        $evaluador_id_actual = self::findCampoByName($campos, 'evaluador_id');

                        $director_actual = User::query()->where('id', '=', $director_id_actual)->first();
                        $evaluador_actual = User::query()->where('id', '=', $evaluador_id_actual)->first();

                        // 1. CAMBIO DE DIRECTOR
                        if ($director_id_actual != $director_id_anterior) {
                            $asunto_correo = 'PROYECTO DE GRADO - CAMBIO DE DIRECTOR';
                            $cuerpo_correo['tipo_solicitud'] = 'CAMBIO DE DIRECTOR';

                            $cuerpo_correo['nuevo_director_nombre'] = $director_actual->name;
                            $cuerpo_correo['nuevo_director_correo'] = $director_actual->email;

                            $cuerpo_correo['destinatario'] = 'estudiante';

                            // Enviamos correo al los estudiantes
                            Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));

                            // Enviamos correo al evaluador actual
                            $cuerpo_correo['destinatario'] = 'evaluador';
                            $correo_evaluador_anterior = $evaluador_anterior->email;
                            Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correo_evaluador_anterior, $esRespuesta));

                            // Se envia correo a director antiguo
                            $cuerpo_correo['destinatario'] = 'director_antiguo';
                            $correo_director_anterior = $director_anterior->email;
                            Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correo_director_anterior, $esRespuesta));

                            // Enviamos correo a nuevo director
                            $cuerpo_correo['destinatario'] = 'director';
                            self::sendEmailNuevo($cuerpo_correo);

                            // Descripción del acta
                            $descripcion_acta = "Cambio de director";

                            // Creamos el acta de ajustes
                            $acta = Acta::create([
                                'numero' => $request->nro_acta_ajustes,
                                'fecha' => $request->fecha_acta_ajustes,
                                'descripcion' => $descripcion_acta,
                                'proyecto_id' => $solicitud_id
                            ]);

                            $acta->save();
                        }

                        // 2. CAMBIO DE EVALUADOR
                        if ($evaluador_id_actual != $evaluador_id_anterior) {
                            $asunto_correo = 'PROYECTO DE GRADO - CAMBIO DE EVALUADOR';
                            $cuerpo_correo['tipo_solicitud'] = 'CAMBIO DE EVALUADOR';

                            $cuerpo_correo['nuevo_evaluador_nombre'] = $evaluador_actual->name;
                            $cuerpo_correo['nuevo_evaluador_correo'] = $evaluador_actual->email;

                            $cuerpo_correo['destinatario'] = 'estudiante';

                            // Enviamos correo al los estudiantes
                            Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));

                            // Enviamos correo al director actual
                            $cuerpo_correo['destinatario'] = 'director';
                            $correo_director_anterior = $director_anterior->email;
                            Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correo_director_anterior, $esRespuesta));

                            // Enviamos correo a evaluador antiguo
                            $cuerpo_correo['destinatario'] = 'evaluador_antiguo';
                            $correo_evaluador_anterior = $evaluador_anterior->email;
                            Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correo_evaluador_anterior, $esRespuesta));

                            // Enviamos correo a nuevo evaluador
                            $cuerpo_correo['destinatario'] = 'evaluador';
                            self::sendEmailNuevo($cuerpo_correo);

                            // Descripción del acta
                            $descripcion_acta = "Cambio de evaluador";

                            // Creamos el acta de ajustes
                            $acta = Acta::create([
                                'numero' => $request->nro_acta_ajustes,
                                'fecha' => $request->fecha_acta_ajustes,
                                'descripcion' => $descripcion_acta,
                                'proyecto_id' => $solicitud_id
                            ]);

                            $acta->save();
                        }

                        // 3. CAMBIO DE FECHAS
                        if ($estado > 3 && $estado < 6) {
                            $fecha_inicio_informe_anterior = $data_old['fecha_inicio_informe'] ?? null;
                            $fecha_maxima_informe_anterior = $data_old['fecha_maxima_informe'] ?? null;

                            $fecha_inicio_informe = self::findCampoByName($campos, 'fecha_inicio_informe');
                            $fecha_maxima_informe = self::findCampoByName($campos, 'fecha_maxima_informe');

                            // 3. PRÓRROGA DE FECHAS
                            if ($fecha_inicio_informe != $fecha_inicio_informe_anterior || $fecha_maxima_informe != $fecha_maxima_informe_anterior) {
                                $asunto_correo = 'PROYECTO DE GRADO - PRÓRROGA';
                                $cuerpo_correo['tipo_solicitud'] = 'CAMBIO DE FECHAS (PRÓRROGA)';

                                // Agregar fecha al cuerpo del correo
                                $cuerpo_correo['fecha_inicio_informe'] = $fecha_inicio_informe;
                                $cuerpo_correo['fecha_maxima_informe'] = $fecha_maxima_informe;

                                $cuerpo_correo['destinatario'] = 'estudiante';

                                // Enviamos correo al los estudiantes
                                Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));

                                // Enviamos correo al director actual
                                $cuerpo_correo['destinatario'] = 'director';
                                $correo_director_actual = $director_actual->email;
                                Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correo_director_actual, $esRespuesta));

                                // Enviamos correo al evaluador actual
                                $cuerpo_correo['destinatario'] = 'evaluador';
                                $correo_evaluador_actual = $evaluador_actual->email;
                                Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correo_evaluador_actual, $esRespuesta));

                                // Descripción del acta
                                $descripcion_acta = "Prórroga";

                                // Creamos el acta de ajustes
                                $acta = Acta::create([
                                    'numero' => $request->nro_acta_ajustes,
                                    'fecha' => $request->fecha_acta_ajustes,
                                    'descripcion' => $descripcion_acta,
                                    'proyecto_id' => $solicitud_id
                                ]);

                                $acta->save();
                            }
                        }
                    }

                    // 2. SECCIÓN PARA RETIRO DE ESTUDIANTE
                    if ($request->input('retirar_estudiante')) {
                        $asunto_correo = 'PROYECTO DE GRADO - RETIRO DE ESTUDIANTE';
                        $cuerpo_correo['tipo_solicitud'] = 'RETIRO DE ESTUDIANTE';

                        $cuerpo_correo['estudiante_retirado'] = $data_old['estudiante_retirado'];
                        $cuerpo_correo['proyecto_id'] = 'GRA-00' . $solicitud_id;

                        $cuerpo_correo['destinatario'] = 'retiro';
                        $correos_destinatarios[] = $data_old['estudiante_retirado']->email;

                        // Se envía correo los integrantes
                        Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));

                        // Se verifica si existe un director y evaluador
                        $director = User::query()->where('id', '=', self::findCampoByName($campos, 'director_id'))->first() ?? null;
                        $evaluador = User::query()->where('id', '=', self::findCampoByName($campos, 'evaluador_id'))->first() ?? null;

                        $correos_destinatarios = [];

                        if ($director) {
                            $correos_destinatarios[] = $director->email;
                        }

                        if ($evaluador) {
                            $correos_destinatarios[] = $evaluador->email;
                        }

                        if (count($correos_destinatarios) > 0) {
                            // Se envía correo al director y evaluador
                            $cuerpo_correo['destinatario'] = 'retiro';
                            $cuerpo_correo['correos_destinatarios'] = $correos_destinatarios;
                            Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));
                        }

                        // Descripción del acta
                        $descripcion_acta = "Retiro de estudiante";

                        // Se genera el acta de ajustes
                        $acta = Acta::create([
                            'numero' => $request->nro_acta_ajustes,
                            'fecha' => $request->fecha_acta_ajustes,
                            'descripcion' => $descripcion_acta,
                            'proyecto_id' => $solicitud_id
                        ]);

                        $acta->save();
                    }
                    break;
                case 'prorroga':
                    $cuerpo_correo['tipo_solicitud'] = mb_strtoupper($request->tipo_solicitud);
                    $asunto_correo = 'PROYECTO DE GRADO - SOLICITUD DE PRÓRROGA';
                    $tipo_correo = 'config_estudiante';
                    $cuerpo_correo['solicitud'] = $request->comentarios_config;
                    $esRespuesta = false;

                    Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));
                    break;
                case 'retiro':
                    $asunto_correo = 'PROYECTO DE GRADO - SOLICITUD DE RETIRO';
                    $tipo_correo = 'config_estudiante';

                    $cuerpo_correo['proyecto_id'] = 'GRA-00' . $request->solicitud_id;
                    $cuerpo_correo['tipo_solicitud'] = mb_strtoupper($request->tipo_solicitud) . ' DE PROYECTO DE GRADO';
                    $cuerpo_correo['solicitud'] = $request->comentarios_config;
                    $cuerpo_correo['estudiante'] = $data_old['estudiante'];
                    $cuerpo_correo['tipo_accion'] = 'retiro';

                    $correos_destinatarios = [];
                    $correos_destinatarios[] = $data_old['estudiante']->email;

                    $esRespuesta = false;

                    Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta));
                    break;
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function sendEmailNuevo($data)
    {
        try {
            $solicitud_id = $data['solicitud_id'];
            $cuerpo_correo = [];
            $correos_directores = [];
            $correos_evaluadores = [];
            $asunto_correo = '';
            $tipo_correo = 'respuesta_fase_1';
            $comentarios = $data['solicitud'];
            $esRespuesta = true;

            $solicitud = Solicitud::where('id', '=', $solicitud_id)->first();
            $campos_solicitud = $solicitud->camposConValores();

            $fase_0 = self::getType('fase_0');
            $fase_1 = self::getType('fase_1');

            $campos_fase_0 = self::findCamposByTipoSolicitud($campos_solicitud, $fase_0->id);

            $periodo_proyecto = self::findCampoByName($campos_fase_0, 'periodo');
            $fechas_proyecto = self::getFechasByPeriodo($periodo_proyecto);

            $cuerpo_correo['estado'] = 'Aprobado';
            $cuerpo_correo['fecha_aprobacion'] = $fechas_proyecto['fecha_aprobacion_propuesta'];

            $cuerpo_correo['director_nombre'] = null;
            $cuerpo_correo['director_correo'] = null;

            $cuerpo_correo['evaluador_nombre'] = null;
            $cuerpo_correo['evaluador_correo'] = null;

            $codigo_modalidad = self::findCampoByName($campos_solicitud, 'codigo_modalidad');
            $cuerpo_correo['codigo_modalidad'] = $codigo_modalidad;

            $cuerpo_correo['nro_acta'] = $data['nro_acta'];
            $cuerpo_correo['fecha_acta'] = $data['fecha_acta'];

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

            $campos = self::findCamposByTipoSolicitud($campos_solicitud, $fase_1->id);
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

            $director_id = self::findCampoByName($campos, 'director_id');
            $evaluador_id = self::findCampoByName($campos, 'evaluador_id');

            $director = User::findOrFail($director_id);
            $evaluador = User::findOrFail($evaluador_id);

            $correos_directores[] = $director->email;
            $correos_evaluadores[] = $evaluador->email;

            $cuerpo_correo['director_nombre'] = $director->name;
            $cuerpo_correo['director_correo'] = $director->email;

            $cuerpo_correo['evaluador_nombre'] = $evaluador->name;
            $cuerpo_correo['evaluador_correo'] = $evaluador->email;

            if ($data['destinatario'] === 'director') {
                $asunto_correo = 'DOCENTE DIRECTOR DE PROYECTO';
                $cuerpo_correo['destinatario'] = 'director';
                Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_directores, $esRespuesta));
            } else if ($data['destinatario'] === 'evaluador') {
                $asunto_correo = 'DOCENTE EVALUADOR DE PROYECTO';
                $cuerpo_correo['destinatario'] = 'evaluador';
                Mail::queue(new ProyectosGradoMail($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_evaluadores, $esRespuesta));
            }
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

    public function getLineaInvestigacion($proyecto)
    {
        $campos = $proyecto->camposConValores();
        $check_idea_banco = $this->findCampoByName($campos, 'check_idea_banco');
        $linea_investigacion = null;

        if ($check_idea_banco == 'true') {
            $idea_banco = Solicitud::query()->where('id', $this->findCampoByName($campos, 'idea_banco'))->first();
            $campos_idea = $idea_banco->camposConValores();
            $linea_investigacion = LineaInvestigacion::findOrFail($this->findCampoByName($campos_idea, 'linea_investigacion'))->nombre;
        } else {
            $linea_investigacion = LineaInvestigacion::findOrFail($this->findCampoByName($campos, 'linea_investigacion'))->nombre;
        }

        return $linea_investigacion;
    }

    public function getTipoIdea($proyecto)
    {
        $campos = $proyecto->camposConValores();
        $check_idea_banco = $this->findCampoByName($campos, 'check_idea_banco');
        $tipo_idea = null;

        if ($check_idea_banco == 'true') {
            $tipo_idea = 'Banco de ideas';
        } else {
            $tipo_idea = 'Idea propia';
        }

        return $tipo_idea;
    }

    function esBeneficiarioIcfesReporte($integrante_id, $beneficiarios_icfes)
    {
        return in_array($integrante_id, $beneficiarios_icfes);
    }

    public function generarReporte(Request $request)
    {
        $type = null;
        $periodo = null;
        $formato_reporte = null;
        $proyectos = null;
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

            $type = self::getType('fase_0');
            $periodo = $request->periodo_reporte;
            $formato_reporte = public_path('formatos/informe_proyectos.xlsx');

            // Cargar el archivo de Excel existente
            $spreadsheet = IOFactory::load($formato_reporte);
            $sheet = $spreadsheet->getActiveSheet();
            $fila = 3;

            $proyectos = Solicitud::query()
                ->where('tipo_solicitud_id', $type->id)
                ->whereIn('estado', ['Fase 2', 'Fase 3', 'Fase 4', 'Fase 5', 'Finalizado'])
                ->whereHas('valoresCampos', function ($q) use ($periodo) {
                    $q->whereHas('campo', function ($q) use ($periodo) {
                        $q->where(function ($q) use ($periodo) {
                            $q->where('name', 'periodo')
                                ->where('valores_campos.valor', $periodo);
                        });
                    });
                })
                ->get();

            foreach ($proyectos as $proyecto) {
                $campos = $proyecto->camposConValores();

                $id = 'GRA-00' . $proyecto->id;
                $codigo_modalidad = self::findCampoByName($campos, 'codigo_modalidad') ?? 'No disponible';
                $titulo = mb_strtoupper(self::getTituloProyecto($proyecto)) ?? 'No disponible';

                $modalidad = Modalidad::findOrFail(self::findCampoByName($campos, 'modalidad'))->nombre ?? 'No disponible';
                $nivel_academico = Nivel::findOrFail(self::findCampoByName($campos, 'nivel'))->nombre ?? 'No disponible';

                $linea_investigacion = self::getLineaInvestigacion($proyecto) ?? 'No disponible';
                $tipo_idea = self::getTipoIdea($proyecto) ?? 'No disponible';

                $estado = $proyecto->estado ?? 'No disponible';

                if ($proyecto->vencido) $estado .= ' (Vencido)';
                if ($proyecto->deshabilitado) $estado .= ' (Deshabilitado)';

                $integrante_1_id = self::findCampoByName($campos, 'id_integrante_1');
                $integrante_2_id = self::findCampoByName($campos, 'id_integrante_2');
                $integrante_3_id = self::findCampoByName($campos, 'id_integrante_3');

                // Obtener el array de beneficiarios ICFES
                $beneficiarios_icfes = self::findCampoByName($campos, 'beneficiarios_icfes');
                $beneficiarios_icfes = $beneficiarios_icfes ? json_decode($beneficiarios_icfes, true) : [];

                $integrantes = '';
                $documentos = '';
                $emails = '';
                $nros_celulares = '';

                if ($integrante_1_id) {
                    $integrante_1 = User::query()->where('id', $integrante_1_id)->first();
                    $tipo_documento_integrante_1 = TipoDocumento::query()->where('id', $integrante_1->tipo_documento_id)->first();
                    $nro_documento_integrante_1 = $integrante_1->nro_documento;
                    $documento_integrante_1 = $tipo_documento_integrante_1->tag . " " . $nro_documento_integrante_1;
                    $email_integrante_1 = $integrante_1->email;
                    $nro_celular_integrante_1 = $integrante_1->nro_celular;

                    $integrantes = mb_strtoupper($integrante_1->name);
                    $documentos = $documento_integrante_1;
                    $emails = $email_integrante_1;
                    $nros_celulares = $nro_celular_integrante_1;

                    if ($beneficiarios_icfes && self::esBeneficiarioIcfesReporte($integrante_1->id, $beneficiarios_icfes)) {
                        $integrantes .= ' - BENEFICIARIO ICFES';
                    }
                }

                if ($integrante_2_id) {
                    $integrante_2 = User::query()->where('id', $integrante_2_id)->first();
                    $tipo_documento_integrante_2 = TipoDocumento::query()->where('id', $integrante_2->tipo_documento_id)->first();
                    $nro_documento_integrante_2 = $integrante_2->nro_documento;
                    $documento_integrante_2 = $tipo_documento_integrante_2->tag . " " . $nro_documento_integrante_2;
                    $email_integrante_2 = $integrante_2->email;
                    $nro_celular_integrante_2 = $integrante_2->nro_celular;

                    $integrantes .= "\n" . mb_strtoupper($integrante_2->name);

                    // verificar si el segundo integrante es beneficiario ICFES
                    if ($beneficiarios_icfes && self::esBeneficiarioIcfesReporte($integrante_2->id, $beneficiarios_icfes)) {
                        $integrantes .= ' - BENEFICIARIO ICFES';
                    }

                    $documentos .= "\n" . $documento_integrante_2;
                    $emails .= "\n" . $email_integrante_2;
                    $nros_celulares .= "\n" . $nro_celular_integrante_2;
                }

                if ($integrante_3_id) {
                    $integrante_3 = User::query()->where('id', $integrante_3_id)->first();
                    $tipo_documento_integrante_3 = TipoDocumento::query()->where('id', $integrante_3->tipo_documento_id)->first();
                    $nro_documento_integrante_3 = $integrante_3->nro_documento;
                    $documento_integrante_3 = $tipo_documento_integrante_3->tag . " " . $nro_documento_integrante_3;
                    $email_integrante_3 = $integrante_3->email;
                    $nro_celular_integrante_3 = $integrante_3->nro_celular;

                    $integrantes .= "\n" . mb_strtoupper($integrante_3->name);

                    // verificar si el tercer integrante es beneficiario ICFES
                    if ($beneficiarios_icfes && self::esBeneficiarioIcfesReporte($integrante_3->id, $beneficiarios_icfes)) {
                        $integrantes .= ' - BENEFICIARIO ICFES';
                    }

                    $documentos .= "\n" . $documento_integrante_3;
                    $emails .= "\n" . $email_integrante_3;
                    $nros_celulares .= "\n" . $nro_celular_integrante_3;
                }

                $director = mb_strtoupper(User::query()->where('id', self::findCampoByName($campos, 'director_id'))->first()->name) ?? 'No disponible';
                $evaluador = mb_strtoupper(User::query()->where('id', self::findCampoByName($campos, 'evaluador_id'))->first()->name) ?? 'No disponible';

                // Obtener actas de registro por proyecto separadas por saltos de línea
                $actas = Acta::query()
                    ->where('proyecto_id', $proyecto->id)
                    ->orderBy('numero', 'asc')
                    ->get();

                $actas_registro = $actas->map(function ($acta) {
                    $fecha = Carbon::parse($acta->fecha);
                    $acta->fecha = $fecha;
                    return "Nro. {$acta->numero} - Fecha: {$acta->fecha->format('d-m-Y')} - {$acta->descripcion}";
                })->implode("\n");

                $inicio_tg = $actas->firstWhere('descripcion', 'Aprobación del pago de la modalidad');
                $inicio_tg = $inicio_tg ? $inicio_tg->fecha->format('d-m-Y') : 'No disponible';
                $aprobacion_propuesta_tg = $actas->firstWhere('descripcion', 'Aprobación de la propuesta');
                $aprobacion_propuesta_tg = $aprobacion_propuesta_tg ? $aprobacion_propuesta_tg->fecha->format('d-m-Y') : 'No disponible';
                $fin_tg = $actas->firstWhere('descripcion', 'Aprobación del informe final');
                $fin_tg = $fin_tg ? $fin_tg->fecha->format('d-m-Y') : 'No disponible';

                // Insertar valores
                $sheet->setCellValue("A{$fila}", $id);
                $sheet->setCellValue("B{$fila}", $codigo_modalidad);
                $sheet->setCellValue("C{$fila}", $titulo);
                $sheet->setCellValue("D{$fila}", $modalidad);
                $sheet->setCellValue("E{$fila}", $nivel_academico);
                $sheet->setCellValue("F{$fila}", $linea_investigacion);
                $sheet->setCellValue("G{$fila}", $tipo_idea);
                $sheet->setCellValue("H{$fila}", $estado);
                $sheet->setCellValue("I{$fila}", $integrantes);
                $sheet->setCellValue("J{$fila}", $documentos);
                $sheet->setCellValue("K{$fila}", $emails);
                $sheet->setCellValue("L{$fila}", $nros_celulares);
                $sheet->setCellValue("M{$fila}", $director);
                $sheet->setCellValue("N{$fila}", $evaluador);
                $sheet->setCellValue("O{$fila}", $actas_registro);
                $sheet->setCellValue("P{$fila}", $inicio_tg);
                $sheet->setCellValue("Q{$fila}", $aprobacion_propuesta_tg);
                $sheet->setCellValue("R{$fila}", $fin_tg);

                // Aplicar bordes a cada celda de la fila
                foreach (range('A', 'R') as $col) {
                    $sheet->getStyle("{$col}{$fila}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle("{$col}{$fila}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                }

                $fila++;
            }

            // Definir el nombre del archivo a descargar
            $fileName = "Informe - Trabajos de grado ({$periodo}).xlsx";

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

    public function estimuloIcfesEstudiante(Request $request)
    {
        $validator = null;
        $proyecto = null;
        $integrante = null;

        try {
            $validator = Validator::make($request->all(), [
                'proyecto_id' => 'required',
                'doc_icfes' => 'required',
                'doc_icfes.*' => 'required',
                'submited_icfes' => 'required',
            ], [
                'proyecto_id.required' => 'El campo periodo es obligatorio',
                'doc_icfes.required' => 'El soporte del ICFES es obligatorio',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // 1. Consultar proyecto al que está vinculado el estudiante
            $proyecto = Solicitud::query()->where('id', '=', $request->input('proyecto_id'))->first();
            $campos = $proyecto->camposConValores();

            // 2. Consultar datos del estudiante
            $integrante = User::query()->where('id', '=', auth()->user()->id)->first();

            // 3. Guardar soporte del ICFES
            $nombreUnico = null;

            if ($request->hasFile('doc_icfes')) {
                $nombresArchivos = [];
                foreach ($request->file('doc_icfes') as $archivo) {
                    $extension = $archivo->getClientOriginalExtension();
                    $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
                    $archivo->storeAs('documentos_proyectos/proyecto-00' . $proyecto->id, $nombreUnico, 'public');
                    $nombresArchivos[] = $nombreUnico;
                }
            }

            $adjunto = public_path('storage/documentos_proyectos/proyecto-00' . $proyecto->id . '/' . $nombreUnico);

            // 4. Guardar el nombre del archivo en la base de datos
            $campo_doc_icfes = Campo::where('name', '=', 'doc_icfes')->firstOrFail();
            $valor_doc_ifces = self::findCampoByName($campos, 'doc_icfes');

            // se verifica si el doc_icfes ya tiene un valor
            if ($valor_doc_ifces) {
                // si ya existe, se toma el valor original, el valor original tiene este formarto ["67bbb22c29a24_1740354092.pdf"], entonces se debe agregar el nuevo valor al array
                $valor_doc_ifces = json_decode($valor_doc_ifces);
                $valor_doc_ifces[] = $nombreUnico;
                $valor_doc_ifces = json_encode($valor_doc_ifces);

                ValorCampo::where('solicitud_id', '=', $proyecto->id)
                    ->where('campo_id', '=', $campo_doc_icfes->id)
                    ->firstOrFail()
                    ->update(['valor' => $valor_doc_ifces]);
            } else {
                // si no existe, se crea un nuevo valor
                $valor_doc_icfes = json_encode([$nombreUnico]);

                ValorCampo::create([
                    'solicitud_id' => $proyecto->id,
                    'campo_id' => $campo_doc_icfes->id,
                    'valor' => $valor_doc_icfes,
                ]);
            }

            // 5. Guardar el submited_icfes en la base de datos
            $campo_submited_icfes = Campo::where('name', '=', 'submited_icfes')->firstOrFail();
            $valor_submited_icfes = self::findCampoByName($campos, 'submited_icfes');

            // se verifica si el submited_icfes ya tiene un valor
            if ($valor_submited_icfes) {
                // si ya existe, se toma el valor original, el valor original tiene este formarto ["67bbb22c29a24_1740354092.pdf"], entonces se debe agregar el nuevo valor al array
                $valor_submited_icfes = json_decode($valor_submited_icfes);

                // el valor de $request->submited_icfes tiene este formato: 1_true, y solo se necesita el 1
                $request_submited_icfes = explode('_', $request->submited_icfes);
                $valor_submited_icfes[] = $request_submited_icfes[0];
                $valor_submited_icfes = json_encode($valor_submited_icfes);

                ValorCampo::where('solicitud_id', '=', $proyecto->id)
                    ->where('campo_id', '=', $campo_submited_icfes->id)
                    ->firstOrFail()
                    ->update(['valor' => $valor_submited_icfes]);
            } else {
                // si no existe, se crea un nuevo valor

                // el valor de $request->submited_icfes tiene este formato: 1_true, y solo se necesita el 1
                $request_submited_icfes = explode('_', $request->submited_icfes);
                $valor_submited_icfes[] = $request_submited_icfes[0];
                $valor_submited_icfes = json_encode($valor_submited_icfes);

                ValorCampo::create([
                    'solicitud_id' => $proyecto->id,
                    'campo_id' => $campo_submited_icfes->id,
                    'valor' => $valor_submited_icfes,
                ]);
            }

            // 6. Enviar correo al estudiante y comité con archivo adjunto
            Mail::queue(new SolicitudEstimuloIcfesMail($proyecto->id, $integrante->id, $adjunto, 'solicitud_estudiante', null, null, null));
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function estimuloIcfesComite(Request $request)
    {
        $validator = null;
        $proyecto = null;
        $integrantes = null;
        $integrante_beneficiado = null;

        try {
            $validator = Validator::make($request->all(), [
                'proyecto_id' => 'required',
                'estado_icfes' => 'required',
                'estudiante_id' => 'required',
                'nro_acta_icfes' => 'required|numeric',
                'fecha_acta_icfes' => 'required|date',
                'respuesta_icfes' => 'required',
            ], [
                'proyecto_id.required' => 'El campo periodo es obligatorio',
                'estado_icfes.required' => 'El campo estado es obligatorio',
                'estudiante_id.required' => 'El campo estudiante es obligatorio',
                'nro_acta_icfes.required' => 'El campo número de acta es obligatorio',
                'nro_acta_icfes.numeric' => 'El campo número de acta debe ser un número',
                'fecha_acta_icfes.required' => 'El campo fecha de acta es obligatorio',
                'fecha_acta_icfes.date' => 'El campo fecha de acta debe ser una fecha válida',
                'respuesta_icfes.required' => 'El campo respuesta es obligatorio',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // 1. Consultar proyecto al que está vinculado el estudiante
            $proyecto = Solicitud::query()->where('id', '=', $request->proyecto_id)->first();
            $campos = $proyecto->camposConValores();

            if ($request->estado_icfes === 'Rechazado') {
                // 1. Obtener el valor del submited_icfes
                $submited_icfes = json_decode(self::findCampoByName($campos, 'submited_icfes')) ?? [];

                // 2. verificar si el id del estudiante está dentro del array submited_icfes
                $existe = in_array($request->estudiante_id, $submited_icfes);

                // 3. Si el id del estudiante no está dentro del array submited_icfes, se debe hacer una excepción
                if (!$existe) {
                    return response()->json(['message' => 'El estudiante seleccionado no ha cargado el soporte del ICFES.'], 422);
                }

                // 4. Obtener el indice del id del estudiante en el array submited_icfes
                $indice = array_search($request->estudiante_id, $submited_icfes);

                // 5. Eliminar el id del estudiante del array submited_icfes
                unset($submited_icfes[$indice]);
                $submited_icfes = json_encode(array_values($submited_icfes));

                // 6. Obtener el valor del doc_icfes
                $doc_icfes = json_decode(self::findCampoByName($campos, 'doc_icfes'));

                // 7. Eliminar el soporte del ICFES del estudiante
                unset($doc_icfes[$indice]);
                $doc_icfes = json_encode(array_values($doc_icfes));

                // 8. Actualizar el valor del submited_icfes
                ValorCampo::where('solicitud_id', '=', $proyecto->id)
                    ->where('campo_id', '=', self::findCampo($campos, 'submited_icfes')->id)
                    ->firstOrFail()
                    ->update(['valor' => $submited_icfes]);

                // 9. Actualizar el valor del doc_icfes
                ValorCampo::where('solicitud_id', '=', $proyecto->id)
                    ->where('campo_id', '=', self::findCampo($campos, 'doc_icfes')->id)
                    ->firstOrFail()
                    ->update(['valor' => $doc_icfes]);


                // 10. Generar acta de rechazo
                $acta = Acta::create([
                    'numero' => $request->nro_acta_icfes,
                    'fecha' => $request->fecha_acta_icfes,
                    'descripcion' => 'Rechazo del beneficio ICFES Saber TyT/Pro del estudiante',
                    'proyecto_id' => $proyecto->id
                ]);

                $acta->save();

                // 11. Enviar correo al estudiante indicando la respuesta
                Mail::queue(new SolicitudEstimuloIcfesMail($request->proyecto_id, $request->estudiante_id, null, 'respuesta_estudiante', $request->estado_icfes, $request->respuesta_icfes, $acta));
            } else {
                // 1. Obtener todos los integrantes del proyecto:
                $integrante_1 = User::query()->where('id', $this->findCampoByName($campos, 'id_integrante_1'))->first();
                $integrante_2 = User::query()->where('id', $this->findCampoByName($campos, 'id_integrante_2'))->first();
                $integrante_3 = User::query()->where('id', $this->findCampoByName($campos, 'id_integrante_3'))->first();

                $integrantes = [];
                if (isset($integrante_1)) $integrantes[] = $integrante_1;
                if (isset($integrante_2)) $integrantes[] = $integrante_2;
                if (isset($integrante_3)) $integrantes[] = $integrante_3;

                // 2. Consultar en los integrantes y obtener el estudiante beneficiado:
                foreach ($integrantes as $integrante) {
                    if ($integrante->id == $request->estudiante_id) {
                        $integrante_beneficiado = $integrante;
                        break;
                    }
                }

                // 3. Obtener la cantidad de integrantes que han cargado el soporte del ICFES:
                $submited_icfes = json_decode(self::findCampoByName($campos, 'submited_icfes')) ?? [];

                // 4. verificar si el id del estudiante beneficiado está dentro del array submited_icfes
                $existe = in_array($integrante_beneficiado->id, $submited_icfes);

                // 5. Si el id del estudiante beneficiado no está dentro del array submited_icfes, se debe hacer una excepción
                if (!$existe) {
                    return response()->json(['message' => 'El estudiante seleccionado no ha cargado el soporte del ICFES.'], 422);
                }

                // 6. Verificar si todos los id de los integrantes estan dentro del array submited_icfes
                $todos_submited = true;
                foreach ($integrantes as $integrante) {
                    if (!in_array($integrante->id, $submited_icfes)) {
                        $todos_submited = false;
                        break;
                    }
                }

                // 7. Se debe agregar el id del estudiante beneficiado al array beneficiarios_icfes, pero para eso se debe verificar si ya existe el ValorCampo beneficiarios_icfes
                $campo_beneficiarios_icfes = Campo::where('name', '=', 'beneficiarios_icfes')->firstOrFail();
                $valor_beneficiarios_icfes = self::findCampoByName($campos, 'beneficiarios_icfes');
                if ($valor_beneficiarios_icfes) {
                    // si ya existe, se toma el valor original, el valor original tiene este formarto ["11"], entonces se debe agregar el nuevo valor al array
                    $valor_beneficiarios_icfes = json_decode($valor_beneficiarios_icfes);
                    $valor_beneficiarios_icfes[] = (string) $integrante_beneficiado->id;
                    $valor_beneficiarios_icfes = json_encode($valor_beneficiarios_icfes);

                    ValorCampo::where('solicitud_id', '=', $proyecto->id)
                        ->where('campo_id', '=', $campo_beneficiarios_icfes->id)
                        ->firstOrFail()
                        ->update(['valor' => $valor_beneficiarios_icfes]);
                } else {
                    // si no existe, se crea un nuevo valor
                    $valor_beneficiarios_icfes = [];
                    $valor_beneficiarios_icfes[] = (string) $integrante_beneficiado->id;
                    $valor_beneficiarios_icfes = json_encode($valor_beneficiarios_icfes);

                    ValorCampo::create([
                        'solicitud_id' => $proyecto->id,
                        'campo_id' => $campo_beneficiarios_icfes->id,
                        'valor' => $valor_beneficiarios_icfes,
                    ]);
                }

                $acta = Acta::create([
                    'numero' => $request->nro_acta_icfes,
                    'fecha' => $request->fecha_acta_icfes,
                    'descripcion' => 'Aprobación del beneficio ICFES Saber TyT/Pro del estudiante',
                    'proyecto_id' => $proyecto->id
                ]);

                $acta->save();

                // 8. Enviar correo al estudiante indicando la respuesta
                Mail::queue(new SolicitudEstimuloIcfesMail($proyecto->id, $integrante_beneficiado->id, null, 'respuesta_estudiante', $request->estado_icfes, $request->respuesta_icfes, $acta));
                Mail::queue(new SolicitudEstimuloIcfesMail($proyecto->id, $integrante_beneficiado->id, null, 'respuesta_docentes', $request->estado_icfes, $request->respuesta_icfes, $acta));

                // 9. En caso de que todos los integrantes hayan cargado el soporte del ICFES, se debe finalizar todo el proyecto
                if ($todos_submited) {
                    // Cambiar el estado del proyecto a Finalizado
                    $proyecto->update([
                        'estado' => 'Finalizado'
                    ]);

                    // Enviar correo a estudiantes, director, evaluador y comité con mensaje de finalización
                    Mail::queue(new SolicitudEstimuloIcfesMail($proyecto->id, $integrante_beneficiado->id, null, 'finalizacion_estudiantes', $request->estado_icfes, $request->respuesta_icfes, $acta));
                    Mail::queue(new SolicitudEstimuloIcfesMail($proyecto->id, $integrante_beneficiado->id, null, 'finalizacion_docentes', $request->estado_icfes, $request->respuesta_icfes, $acta));
                }
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }
}
