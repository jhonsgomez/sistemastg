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
use App\Models\Fecha;
use App\Services\PracticaMailService;
use App\Services\PracticaService;
use App\Http\Requests\StorePracticaRequest;


class PracticaController extends Controller
{

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

    public function index(Request $request)
    {
        // Obtener el periodo actual
        $periodoActual = session('periodo_academico', '2026-1');

        // Buscar las fechas para el periodo actual
        $fechasData = Fecha::where('periodo', $periodoActual)->first();

        $fechas = [];
        
        if ($fechasData) {
            // Como el modelo tiene cast, $fechasData->fechas ya es un array
            $fechasArray = $fechasData->fechas;
            
            $fechas = [
                'fecha_inicio_banco' => $fechasArray['fecha_inicio_banco'] ?? 'No definida',
                'fecha_fin_banco' => $fechasArray['fecha_fin_banco'] ?? 'No definida',
                'fecha_inicio_proyectos' => $fechasArray['fecha_inicio_proyectos'] ?? 'No definida',
                'fecha_fin_proyectos' => $fechasArray['fecha_fin_proyectos'] ?? 'No definida',
                'fecha_aprobacion_propuesta' => $fechasArray['fecha_aprobacion_propuesta'] ?? 'No definida',
            ];
        } else {
            // Fechas por defecto
            $fechas = [
                'fecha_inicio_banco' => '2026-01-30',
                'fecha_fin_banco' => '2026-09-30',
                'fecha_inicio_proyectos' => '2026-02-09',
                'fecha_fin_proyectos' => '2026-09-30',
                'fecha_aprobacion_propuesta' => '2026-09-30',
            ];
        }
        
        // Obtener las prácticas del usuario logueado
        $practicas = Practica::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Obtener campos para el formulario de Fase 0
        $tipo = TipoSolicitud::where('nombre', 'practicas_fase_0')->first();
        $campos = Campo::where('tipo_solicitud_id', $tipo->id)->get();

        return view('practicas.index', compact('campos', 'practicas', 'fechas'));
    }


    
    public function getData(Request $request)
{

  
    // CORREGIDO: Mostrar TODAS las prácticas, no solo tipo_solicitud_id = 9
    $practicas = Practica::with('user')
        ->where('tipo_solicitud_id', '>=', 9);  // Todas las fases de prácticas (9, 10, 11, 12, 13, 14)

        \Log::info('ESTADO PRACTICAS', 
    $practicas->pluck('estado')->toArray()
);
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
    }

    // DIRECTOR
    if (auth()->user()->hasRole('director_practica')) {


        $practicas->whereHas('valoresCampos', function ($vc) {

            $vc->whereHas('campo', function ($c) {
                $c->where('name', 'director_id');
            })->where('valor', auth()->id());

        });

    }

    // EVALUADOR
    if (auth()->user()->hasRole('evaluador_practica')) {

        

        $practicas->whereHas('valoresCampos', function ($vc) {

            $vc->whereHas('campo', function ($c) {
                $c->where('name', 'evaluador_id');
            })->where('valor', auth()->id());

        });

    }

    // FILTRO PARA CODIRECTOR (si lo necesitas)
    if (auth()->user()->hasRole('codirector_practica')) {
        $practicas->whereHas('valoresCampos', function ($vc) {
            $vc->whereHas('campo', function ($c) {
                $c->where('name', 'codirector_id');
            })->where('valor', auth()->id());
        });
    }

    if (auth()->user()->hasRole(['super_admin', 'admin', 'coordinador'])) {
        $filter = $request->input('filter');
        
        switch ($filter) {
            case 'pendientes_comite':
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

    // BÚSQUEDA AVANZADA
    if ($request->has('search') && $search = $request->input('search.value')) {
        $practicas->where(function ($q) use ($search) {
            if (preg_match('/GRA-00(\d+)/i', $search, $matches)) {
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

    // DATATABLE - AQUI LE SALE LAS PRACTICAS A CADA USUARIO
 // DATATABLE - AQUI LE SALE LAS PRACTICAS A CADA USUARIO

        if (auth()->user()->hasRole('estudiante')) {

            $practicas = Practica::with('valoresCampos.campo')
                ->where('user_id', auth()->id())
                ->orderBy('id', 'desc')
                ->get();

        } elseif (auth()->user()->hasRole('director_practica')) {

            $practicas = Practica::with('valoresCampos.campo')
                ->whereHas('valoresCampos', function ($vc) {

                    $vc->whereHas('campo', function ($c) {
                        $c->where('name', 'director_id');
                    })

                    ->where('valor', auth()->id());

                })
                ->orderBy('id', 'desc')
                ->get();

        } elseif (auth()->user()->hasRole('evaluador_practica')) {

            $practicas = Practica::with('valoresCampos.campo')
                ->whereHas('valoresCampos', function ($vc) {

                    $vc->whereHas('campo', function ($c) {
                        $c->where('name', 'evaluador_id');
                    })

                    ->where('valor', auth()->id());

                })
                ->orderBy('id', 'desc')
                ->get();

        } else {

            $practicas = Practica::with('valoresCampos.campo')
                ->orderBy('id', 'desc')
                ->get();
        }

    return DataTables::of($practicas)
        ->addColumn('formatted_id', function ($p) {
            return 'GRA-00' . $p->id;
        })
        ->addColumn('descripcion', function ($p) {
            return 'Solicitud de prácticas empresariales';
        })
        ->addColumn('estado', function ($p) {
            $return_html = '<div class="flex gap-2 flex-wrap items-center justify-center">';

            // ========== BADGE BENEFICIARIO ICFES ==========
            $acceso = $this->esBeneficiarioIcfesListaPractica($p);
            
            // Badge para beneficiario ICFES (solo estudiantes en Fase 5 o 6)
            $badge_beneficiario_icfes = '<span class="shadow bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded border border-blue-300">Beneficiario ICFES</span>';
            
            if ($p->estado === 'Rechazada') {
                $badge = "<span class='shadow bg-red-100 text-red-800 text-sm font-medium px-2.5 py-0.5 rounded border border-red-300'>Rechazada</span>";
                return $return_html . $badge . "</div>";
            }

            $htmlEstado = '';

            if ($p->estado === 'Pendiente') {
                $htmlEstado = "<span class='shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300'>Pendiente</span>
                               <span class='shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300'>Comité</span>";
            } 
            elseif ($p->estado === 'Fase 1') {
                $submited = $p->valoresCampos->where('campo.name', 'submited_fase1')->first();
                $yaEnvio = $submited && $submited->valor === 'true';
                
                if ($yaEnvio) {
                    $htmlEstado = "<span class='shadow bg-uts-300 text-sm font-medium px-2.5 py-0.5 rounded border border-uts-500'>Fase 1</span>
                                   <span class='shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300'>Comité</span>";
                } else {
                    $htmlEstado = "<span class='shadow bg-uts-300 text-sm font-medium px-2.5 py-0.5 rounded border border-uts-500'>Fase 1</span>
                                   <span class='shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300'>Estudiante</span>";
                }
            } 
            elseif ($p->estado === 'Fase 2') {
                $submited = $p->valoresCampos->where('campo.name', 'submited_fase2')->first();
                $yaEnvio = $submited && $submited->valor === 'true';
                
                if ($yaEnvio) {
                    $htmlEstado = "<span class='shadow bg-uts-300 text-sm font-medium px-2.5 py-0.5 rounded border border-uts-500'>Fase 2</span>
                                <span class='shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300'>Comité</span>";
                } else {
                    $htmlEstado = "<span class='shadow bg-uts-300 text-sm font-medium px-2.5 py-0.5 rounded border border-uts-500'>Fase 2</span>
                                <span class='shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300'>Estudiante</span>";
                }
            }
            elseif ($p->estado === 'Fase 3') {
                $submited = $p->valoresCampos->where('campo.name', 'submited_fase3')->first();
                $yaEnvio = $submited && $submited->valor === 'true';

                if ($yaEnvio) {
                    $htmlEstado = "<span class='shadow bg-uts-300 text-sm font-medium px-2.5 py-0.5 rounded border border-uts-500'>Fase 3</span>
                                <span class='shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300'>Director</span>";
                } else {
                    $htmlEstado = "<span class='shadow bg-uts-300 text-sm font-medium px-2.5 py-0.5 rounded border border-uts-500'>Fase 3</span>
                                <span class='shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300'>Estudiante</span>";
                }
            }
         
            elseif ($p->estado === 'Fase 4') {
                $estadoEvaluador = $p->valoresCampos
                    ->where('campo.name', 'estado_evaluador_fase4')
                    ->first();

                $respondioEvaluador = $estadoEvaluador && !empty($estadoEvaluador->valor);

                if ($respondioEvaluador) {
                    $htmlEstado = "
                        <span class='shadow bg-uts-300 text-sm font-medium px-2.5 py-0.5 rounded border border-uts-500'>Fase 4</span>
                        <span class='shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300'>Comité</span>";
                } else {
                    $htmlEstado = "
                        <span class='shadow bg-uts-300 text-sm font-medium px-2.5 py-0.5 rounded border border-uts-500'>Fase 4</span>
                        <span class='shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300'>Evaluador</span>";
                }
            }

            elseif ($p->estado === 'Fase 5') {

                

                $submited = $p->valoresCampos
                    ->where('campo.name', 'submited_fase5')
                    ->first();
                $yaEnvio = $submited && $submited->valor === 'true';

                // Verificar si es beneficiario ICFES
    $esBeneficiario = $this->esBeneficiarioIcfesListaPractica($p);
    $badgeBeneficiario = $esBeneficiario ? '<span class="shadow bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded border border-blue-300">Beneficiario ICFES</span>' : '';
                
                if ($yaEnvio) {
                    $htmlEstado = "
                        <span class='shadow bg-uts-300 text-sm font-medium px-2.5 py-0.5 rounded border border-uts-500'>Fase 5</span>
                        <span class='shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300'> Director</span>
                    ";
                } else {
                    $htmlEstado = "
                        <span class='shadow bg-uts-300 text-sm font-medium px-2.5 py-0.5 rounded border border-uts-500'>Fase 5</span>
                        <span class='shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300'>Estudiante</span>
                    ";
                }
            }
             elseif ($p->estado === 'Fase 6') {
                $estadoEvaluador = $p->valoresCampos
                    ->where('campo.name', 'estado_evaluador_fase5')
                    ->first();

                $respondioEvaluador = $estadoEvaluador && !empty($estadoEvaluador->valor);

                // Verificar si es beneficiario ICFES
    $esBeneficiario = $this->esBeneficiarioIcfesListaPractica($p);
    $badgeBeneficiario = $esBeneficiario ? '<span class="shadow bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded border border-blue-300">Beneficiario ICFES</span>' : '';
                
                if ($respondioEvaluador) {
                    $htmlEstado = "
                        <span class='shadow bg-uts-300 text-sm font-medium px-2.5 py-0.5 rounded border border-uts-500'>Fase 6</span>
                        <span class='shadow bg-purple-100 text-purple-800 text-sm font-medium px-2.5 py-0.5 rounded border border-purple-300'>Comité</span>";
                } else {
                    $htmlEstado = "
                        <span class='shadow bg-uts-300 text-sm font-medium px-2.5 py-0.5 rounded border border-uts-500'>Fase 6</span>
                        <span class='shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300'>Evaluador</span>";
                }
            }

            elseif ($p->estado === 'Finalizado') {
                $htmlEstado = "<span class='px-2 py-1 shadow rounded-md text-sm font-semibold bg-green-100 text-green-800 border border-green-300'>Finalizado</span>";
            }

            if ($p->deshabilitado && $p->estado !== 'Rechazada') {
                $deshabilitadoBadge = "<span class='shadow bg-red-100 text-red-800 text-sm font-medium px-2.5 py-0.5 rounded border border-red-300'>Deshabilitado</span>";
                return $return_html . $htmlEstado . ' ' . $deshabilitadoBadge . "</div>";
            }

            return $return_html . $htmlEstado . "</div>";
        })
        ->addColumn('acciones', function ($p) {
            $user = auth()->user();
            $buttons = '<div class="flex items-center justify-center gap-2">';

            // Botón Ver (siempre visible)
            $buttons .= '<button onclick="openDetailsModal(this, ' . $p->id . ')" 
                class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white w-10 h-10 rounded-lg relative inline-flex items-center justify-center">
                <i class="fa-regular fa-eye"></i>
                <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                </svg>
            </button>';

            // Botón Responder VERDE (para Comité en Fase 0 Pendiente)
            $esComite = $user->hasRole(['super_admin', 'admin', 'coordinador']);
            if ($esComite && $p->estado === 'Pendiente') {
                $submited = $p->valoresCampos->where('campo.name', 'submited_fase0')->first();
                if ($submited && $submited->valor === 'true') {
                    $buttons .= '<button onclick="openResponderSolicitudModal(' . $p->id . ')"
                        class="btn-action shadow bg-uts-500 hover:bg-uts-800 text-white px-3 py-1 rounded-lg">
                        <i class="fa-solid fa-share"></i>
                    </button>';
                }
            }

            // Botón Roadmap AZUL
            $esFaseActiva = in_array($p->estado, ['Fase 1', 'Fase 2', 'Fase 3', 'Fase 4', 'Fase 5', 'Fase 6', 'Finalizado']);
            $puedeVerRoadmap = !$p->deshabilitado && $esFaseActiva;

            if ($puedeVerRoadmap) {
                $buttons .= '
                    <form action="' . route('practicas.roadmap') . '" method="POST" class="inline-block m-0" onsubmit="return showRoadmapSpinner(this)">
                        ' . csrf_field() . '
                        <input type="hidden" name="practica_id" value="' . $p->id . '">
                        <button type="submit" class="btn-action shadow bg-indigo-500 hover:bg-indigo-800 text-white rounded-lg inline-flex items-center justify-center">
                            <i class="fa-solid fa-map-location-dot"></i>
                            <svg class="loading-spinner hidden text-white animate-spin" viewBox="0 0 64 64" fill="none">
                                <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                            </svg>
                        </button>
                    </form>';
            }

            // Botón Deshabilitar/Habilitar (SOLO para Comité)
            if ($esComite && $esFaseActiva && $p->estado !== 'Rechazada') {
                if (!$p->deshabilitado) {
                    $buttons .= '<button onclick="deshabilitarPracticaConActa(' . $p->id . ')"
                        class="btn-action shadow bg-red-500 hover:bg-red-700 text-white rounded-lg inline-flex items-center justify-center">
                        <i class="fa-regular fa-circle-xmark"></i>
                    </button>';
                } else {
                    $buttons .= '<button onclick="habilitarPracticaConActa(' . $p->id . ')"
                        class="btn-action shadow bg-teal-500 hover:bg-teal-800 text-white px-3 py-1 rounded-lg relative">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </button>';
                }
            }

            $buttons .= '</div>';
            return $buttons;
        })
        ->rawColumns(['estado', 'acciones', 'descripcion'])
        ->make(true);
}

    // Dentro del método getData(), después de las funciones existentes
// Agrega esta función auxiliar

private function esBeneficiarioIcfesListaPractica($practica)
{
    if (!auth()->user()->hasRole('estudiante')) {
        return false;
    }

    $campoBeneficiario = Campo::where('name', 'beneficiarios_icfes_practicas')
        ->where('tipo_solicitud_id', $practica->tipo_solicitud_id)
        ->first();

    if (!$campoBeneficiario) {
        return false;
    }

    $valorBeneficiario = $practica->valoresCampos
        ->where('campo_id', $campoBeneficiario->id)
        ->first();

    if (!$valorBeneficiario || !$valorBeneficiario->valor) {
        return false;
    }

    $beneficiarios = json_decode($valorBeneficiario->valor, true) ?? [];
    return in_array(auth()->user()->id, $beneficiarios);
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
    
    
    
    
    
    
   public function store(StorePracticaRequest $request)
    {
        $practica = $this->practicaService
            ->crearPractica($request);
        

       // $this->practicaMailService ->sendSolicitud($practica);

        return response()->json([
            'message' => 'Práctica enviada correctamente'
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
         // Enviar correo al estudiante - DESCOMENTAR PARA ENVIAR CORREO
        // $this->practicaMailService->sendRespuesta($practica,$nuevoEstado,$request->mensaje,$request->estado);
            
        } else {
            // Si es rechazada, el nuevo estado es 'Rechazada'
            $nuevoEstado = 'Rechazada';
             // Enviar correo al estudiante -DESCOMENTAR PARA ENVIAR CORREO
           // $this->practicaMailService->sendRespuesta($practica,$nuevoEstado,$request->mensaje,$request->estado);
           
        }

        // Asignar el nuevo estado al objeto
        $practica->estado = $nuevoEstado;
        $practica->save();

        

        return response()->json(['success' => 'Respuesta enviada exitosamente', 'estado' => $practica->estado]);
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
        $integrantesHtml .= '<p class="font-semibold text-gray-700 mb-2 sm:mb-0 w-1/3 min-w-[100px]">Integrante:</p>';
        $integrantesHtml .= '<div class="text-gray-800 w-full sm:flex-1 sm:ml-2">';
        $integrantesHtml .= e($practica->user->name) . '<br>';
        $integrantesHtml .= 'C.C ' . e($practica->user->nro_documento ?? 'N/A') . '<br>';
        $integrantesHtml .= '<a href="mailto:' . e($practica->user->email) . '" class="text-blue-600 underline">'. e($practica->user->email) .'</a><br>';
        $integrantesHtml .= e($practica->user->nro_celular ?? 'N/A');
        $integrantesHtml .= '</div></div>';
        
        // Integrante 2 (si existe)
        if ($integrante2) {
            $integrantesHtml .= '<div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">';
            $integrantesHtml .= '<p class="font-semibold text-gray-700 mb-2 sm:mb-0 w-1/3 min-w-[100px]">Integrante:</p>';
            $integrantesHtml .= '<div class="text-gray-800 w-full sm:flex-1 sm:ml-2">';
            $integrantesHtml .= e($integrante2->name) . '<br>';
            $integrantesHtml .= 'C.C ' . e($integrante2->nro_documento ?? 'N/A') . '<br>';
            $integrantesHtml .= '<a href="mailto:' . e($integrante2->email) . '" class="text-blue-600 underline">'. e($integrante2->email) .'</a><br>';
            $integrantesHtml .= e($integrante2->nro_celular ?? 'N/A');
            $integrantesHtml .= '</div></div>';
        }
        
        // Obtener nombres de los docentes en lugar de IDs
        $directorNombre = 'No asignado';
        $evaluadorNombre = 'No asignado';
        $codirectorNombre = 'No asignado';
        
        if (isset($data['director_id']) && !empty($data['director_id'])) {
            $director = \App\Models\User::find($data['director_id']);
            $directorNombre = $director ? $director->name : 'No asignado';
        }
        
        if (isset($data['evaluador_id']) && !empty($data['evaluador_id'])) {
            $evaluador = \App\Models\User::find($data['evaluador_id']);
            $evaluadorNombre = $evaluador ? $evaluador->name : 'No asignado';
        }
        
        if (isset($data['codirector_id']) && !empty($data['codirector_id'])) {
            $codirector = \App\Models\User::find($data['codirector_id']);
            $codirectorNombre = $codirector ? $codirector->name : 'No asignado';
        }
        
        // Si es estudiante, ocultar evaluador
        if ($esEstudiante) {
            $docentesHtml = '<div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">';
            $docentesHtml .= '<p class="font-semibold text-gray-700 mb-2 sm:mb-0 w-1/3 min-w-[100px]">Docentes:</p>';
            $docentesHtml .= '<div class="text-gray-800 w-full sm:flex-1 sm:ml-2">';
            $docentesHtml .= '<span><b>Director:</b> ' . e($directorNombre) . '</span><br>';
            $docentesHtml .= '<span><b>Codirector:</b> ' . e($codirectorNombre) . '</span>';
            $docentesHtml .= '</div></div>';
        } else {
            $docentesHtml = '<div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">';
            $docentesHtml .= '<p class="font-semibold text-gray-700 mb-2 sm:mb-0 w-1/3 min-w-[100px]">Docentes:</p>';
            $docentesHtml .= '<div class="text-gray-800 w-full sm:flex-1 sm:ml-2">';
            $docentesHtml .= '<span><b>Director:</b> ' . e($directorNombre) . '</span><br>';
            $docentesHtml .= '<span><b>Evaluador:</b> ' . e($evaluadorNombre) . '</span><br>';
            $docentesHtml .= '<span><b>Codirector:</b> ' . e($codirectorNombre) . '</span>';
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
        $hojaVida2 = $data['hoja_vida_2'] ?? null;
        
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
            'hoja_vida_2' => $hojaVida2,
            'es_estudiante' => $esEstudiante
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error en getDetalle: ' . $e->getMessage());
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
