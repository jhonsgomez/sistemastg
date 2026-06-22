<?php
namespace App\Http\Controllers;

use App\Mail\PracticasMail;
use App\Models\ActaPractica;
use App\Models\Campo;
use App\Models\User;
use App\Models\TipoDocumento;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Exception;
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
use Illuminate\Support\Facades\Storage;



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

    public function uploadQuillImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $path = $request->file('image')->store('quill/practicas', 'public');

        return response()->json([
            'url' => asset('storage/' . $path)
        ]);
    }

    public function index(Request $request)
    {

        $user = auth()->user();
    $tienePracticaActiva = false;
    
    if ($user->hasRole('estudiante')) {
        $tienePracticaActiva = $this->tienePracticaActiva($user->id);
    }
    
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

        return view('practicas.index', compact('campos', 'practicas', 'fechas', 'tienePracticaActiva'));
    }


    
    public function getData(Request $request)
    {
        $user = auth()->user();

        $rol_especifico = $request->input('rol_especifico');

        if ($request->routeIs('director.practicas.data')) {
            $rol_especifico = 'director_practica';
        }

        if ($request->routeIs('evaluador.practicas.data')) {
            $rol_especifico = 'evaluador_practica';
        }

        $query = Practica::with(['user', 'user.nivel', 'valoresCampos.campo']);

        // ================= DIRECTOR PRÁCTICAS =================
        if ($rol_especifico === 'director_practica') {
            $query->whereHas('valoresCampos', function ($vc) use ($user) {
                $vc->where('valor', (string) $user->id)
                    ->whereHas('campo', function ($c) {
                        $c->where('name', 'director_id');
                    });
            })
            ->whereIn('estado', ['Fase 3', 'Fase 4', 'Fase 5', 'Fase 6', 'Finalizado']);
        }

        // ================= EVALUADOR PRÁCTICAS =================
        elseif ($rol_especifico === 'evaluador_practica') {
            $query->whereHas('valoresCampos', function ($vc) use ($user) {
                $vc->where('valor', (string) $user->id)
                    ->whereHas('campo', function ($c) {
                        $c->where('name', 'evaluador_id');
                    });
            })
            ->whereIn('estado', ['Fase 4', 'Fase 5', 'Fase 6', 'Finalizado']);
        }

        // ================= ESTUDIANTE =================
        elseif ($user->hasRole('estudiante')) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('valoresCampos', function ($vc) use ($user) {
                        $vc->where('valor', (string) $user->id)
                            ->whereHas('campo', function ($c) {
                                $c->where('name', 'id_integrante_2');
                            });
                    });
            });
        }

        // ================= CODIRECTOR PRÁCTICAS =================
        elseif ($rol_especifico === 'codirector_practica') {
            $query->whereHas('valoresCampos', function ($vc) use ($user) {
                $vc->where('valor', (string) $user->id)
                    ->whereHas('campo', function ($c) {
                        $c->where('name', 'codirector_id');
                    });
            });
        }

        // ================= COMITÉ / ADMIN / COORDINADOR =================
    elseif ($user->hasRole(['super_admin', 'admin', 'coordinador', 'comité'])) {
        $filter = $request->input('filter');

        switch ($filter) {
            // ========== PENDIENTES COMITÉ ==========
            case 'pendientes_comite':
                $query->where(function ($q) {
                    // Pendiente (Fase 0 - Comité)
                    $q->orWhere('estado', 'Pendiente');
                    
                    // Fase 1 Comité (submited_fase1 = true)
                    $q->orWhere(function ($sub) {
                        $sub->where('estado', 'Fase 1')
                            ->whereHas('valoresCampos', function ($vc) {
                                $vc->whereHas('campo', function ($c) {
                                    $c->where('name', 'submited_fase1');
                                })->where('valor', 'true');
                            });
                    });
                    
                    // Fase 2 Comité (submited_fase2 = true)
                    $q->orWhere(function ($sub) {
                        $sub->where('estado', 'Fase 2')
                            ->whereHas('valoresCampos', function ($vc) {
                                $vc->whereHas('campo', function ($c) {
                                    $c->where('name', 'submited_fase2');
                                })->where('valor', 'true');
                            });
                    });
                    
                    // Fase 4 Comité (estado_evaluador_fase4 tiene valor)
                    $q->orWhere(function ($sub) {
                        $sub->where('estado', 'Fase 4')
                            ->whereHas('valoresCampos', function ($vc) {
                                $vc->whereHas('campo', function ($c) {
                                    $c->where('name', 'estado_evaluador_fase4');
                                })->whereNotNull('valor')
                                  ->where('valor', '!=', '');
                            });
                    });
                });
                break;

            // ========== PENDIENTES DIRECTOR ==========
            case 'pendientes_director':
                $query->where(function ($q) {
                    // Fase 3 Director (submited_fase3 = true)
                    $q->orWhere(function ($sub) {
                        $sub->where('estado', 'Fase 3')
                            ->whereHas('valoresCampos', function ($vc) {
                                $vc->whereHas('campo', function ($c) {
                                    $c->where('name', 'submited_fase3');
                                })->where('valor', 'true');
                            });
                    });
                    
                    // Fase 5 Director (submited_fase5 = true)
                    $q->orWhere(function ($sub) {
                        $sub->where('estado', 'Fase 5')
                            ->whereHas('valoresCampos', function ($vc) {
                                $vc->whereHas('campo', function ($c) {
                                    $c->where('name', 'submited_fase5');
                                })->where('valor', 'true');
                            });
                    });
                });
                break;

            // ========== PENDIENTES EVALUADOR ==========
            case 'pendientes_evaluador':
                $query->where(function ($q) {
                    // Fase 4 Evaluador (estado_evaluador_fase4 está vacío o no existe)
                    $q->orWhere(function ($sub) {
                        $sub->where('estado', 'Fase 4')
                            ->where(function ($where) {
                                $where->whereDoesntHave('valoresCampos', function ($vc) {
                                    $vc->whereHas('campo', function ($c) {
                                        $c->where('name', 'estado_evaluador_fase4');
                                    });
                                })
                                ->orWhereHas('valoresCampos', function ($vc) {
                                    $vc->whereHas('campo', function ($c) {
                                        $c->where('name', 'estado_evaluador_fase4');
                                    })->where(function ($w) {
                                        $w->whereNull('valor')
                                          ->orWhere('valor', '');
                                    });
                                });
                            });
                    });
                    
                    // Fase 6 Evaluador (estado_evaluador_fase6 está vacío o no existe)
                    $q->orWhere(function ($sub) {
                        $sub->where('estado', 'Fase 6')
                            ->where(function ($where) {
                                $where->whereDoesntHave('valoresCampos', function ($vc) {
                                    $vc->whereHas('campo', function ($c) {
                                        $c->where('name', 'estado_evaluador_fase6');
                                    });
                                })
                                ->orWhereHas('valoresCampos', function ($vc) {
                                    $vc->whereHas('campo', function ($c) {
                                        $c->where('name', 'estado_evaluador_fase6');
                                    })->where(function ($w) {
                                        $w->whereNull('valor')
                                          ->orWhere('valor', '');
                                    });
                                });
                            });
                    });
                });
                break;

            // ========== PROPUESTAS SIN APROBAR ==========
            case 'propuestas_pendientes':
                $query->where(function ($q) {
                    // Fase 1 Estudiante (submited_fase1 = false o no existe)
                    $q->orWhere(function ($sub) {
                        $sub->where('estado', 'Fase 1')
                            ->where(function ($where) {
                                $where->whereDoesntHave('valoresCampos', function ($vc) {
                                    $vc->whereHas('campo', function ($c) {
                                        $c->where('name', 'submited_fase1');
                                    });
                                })
                                ->orWhereHas('valoresCampos', function ($vc) {
                                    $vc->whereHas('campo', function ($c) {
                                        $c->where('name', 'submited_fase1');
                                    })->where('valor', 'false');
                                });
                            });
                    });
                    
                    // Fase 1 Comité (submited_fase1 = true)
                    $q->orWhere(function ($sub) {
                        $sub->where('estado', 'Fase 1')
                            ->whereHas('valoresCampos', function ($vc) {
                                $vc->whereHas('campo', function ($c) {
                                    $c->where('name', 'submited_fase1');
                                })->where('valor', 'true');
                            });
                    });
                });
                break;

            // ========== INFORMES FINALES SIN APROBAR ==========
            case 'informes_pendientes':
                $query->where(function ($q) {
                    // Fase 5 Estudiante (submited_fase5 = false o no existe)
                    $q->where('estado', 'Fase 5')
                        ->where(function ($where) {
                            $where->whereDoesntHave('valoresCampos', function ($vc) {
                                $vc->whereHas('campo', function ($c) {
                                    $c->where('name', 'submited_fase5');
                                });
                            })
                            ->orWhereHas('valoresCampos', function ($vc) {
                                $vc->whereHas('campo', function ($c) {
                                    $c->where('name', 'submited_fase5');
                                })->where('valor', 'false');
                            });
                        });
                });
                break;
        }
    }

        // ================= BÚSQUEDA AVANZADA =================
if ($request->has('search') && $search = $request->input('search.value')) {
    $query->where(function ($q) use ($search) {

        $searchLower = strtolower(trim($search));

        // ========== 1. BÚSQUEDA POR ID ==========
        if (preg_match('/GRA-00(\d+)/i', $search, $matches)) {
            $idNumero = intval($matches[1]);
            $q->orWhere('id', $idNumero);
        }

        if (is_numeric($search)) {
            $q->orWhere('id', $search);
        }

        // ========== 2. BÚSQUEDA POR ESTADO ==========
        if (preg_match('/^fase\s+([1-6])$/i', $searchLower, $matches)) {
            $q->orWhere('estado', 'Fase ' . $matches[1]);
        }

        if (in_array($searchLower, ['pendiente', 'pendientes'])) {
            $q->orWhere('estado', 'Pendiente');
        }

        if (in_array($searchLower, ['rechazada', 'rechazado'])) {
            $q->orWhere('estado', 'Rechazada');
        }
        if (in_array($searchLower, ['aplazada', 'aplazado'])) {
            $q->orWhere('estado', 'Aplazada');
        }

        if (in_array($searchLower, ['finalizado', 'finalizada'])) {
            $q->orWhere('estado', 'Finalizado');
        }

        // ========== 3. BÚSQUEDA POR RESPONSABLE ==========
        // Estudiante
        if (in_array($searchLower, ['estudiante', 'estudiantes'])) {
                $q->orWhere(function ($sub) {
                    $sub->where('estado', 'Fase 1')
                        ->where(function ($where) {
                            $where->whereDoesntHave('valoresCampos', function ($vc) {
                                $vc->whereHas('campo', function ($c) {
                                    $c->where('name', 'submited_fase1');
                                });
                            })
                            ->orWhereHas('valoresCampos', function ($vc) {
                                $vc->whereHas('campo', function ($c) {
                                    $c->where('name', 'submited_fase1');
                                })->where('valor', 'false');
                            });
                        });
                });
            }

        // Comité
        if (in_array($searchLower, ['comité', 'comite'])) {
            $q->orWhere(function ($sub) {
                $sub->whereHas('valoresCampos', function ($vcq) {
                    $vcq->whereHas('campo', function ($cq) {
                        $cq->whereIn('name', ['submited_fase1', 'submited_fase2']);
                    })->where('valor', 'true');
                });
            });
        }

        // Director
        if (in_array($searchLower, ['director'])) {
                $q->orWhere(function ($sub) {
                    $sub->where('estado', 'Fase 3')
                        ->whereHas('valoresCampos', function ($vc) {
                            $vc->whereHas('campo', function ($c) {
                                $c->where('name', 'submited_fase3');
                            })->where('valor', 'true');
                        });
                });
                $q->orWhere(function ($sub) {
                    $sub->where('estado', 'Fase 5')
                        ->whereHas('valoresCampos', function ($vc) {
                            $vc->whereHas('campo', function ($c) {
                                $c->where('name', 'submited_fase5');
                            })->where('valor', 'true');
                        });
                });
            }

        // Evaluador
        if (in_array($searchLower, ['evaluador'])) {
                $q->orWhere(function ($sub) {
                    $sub->where('estado', 'Fase 4')
                        ->whereHas('valoresCampos', function ($vc) {
                            $vc->whereHas('campo', function ($c) {
                                $c->where('name', 'estado_evaluador_fase4');
                            })->whereNotNull('valor')
                              ->where('valor', '!=', '');
                        });
                });
                $q->orWhere(function ($sub) {
                    $sub->where('estado', 'Fase 6')
                        ->whereHas('valoresCampos', function ($vc) {
                            $vc->whereHas('campo', function ($c) {
                                $c->where('name', 'estado_evaluador_fase6');
                            })->whereNotNull('valor')
                              ->where('valor', '!=', '');
                        });
                });
            }

        // ========== 4. BÚSQUEDA POR BENEFICIARIO ICFES ==========
        if (in_array($searchLower, ['beneficiario', 'beneficiarios', 'icfes', 'beneficiario icfes'])) {
            $q->orWhereHas('valoresCampos', function ($vcq) {
                $vcq->whereHas('campo', function ($cq) {
                    $cq->where('name', 'beneficiarios_icfes_practicas');
                })->whereNotNull('valor')
                  ->where('valor', '!=', '[]')
                  ->where('valor', '!=', '{}');
            });
        }

        // ========== 5. BÚSQUEDA POR RETIRADO ==========
        if (in_array($searchLower, ['retirado', 'retirados', 'retiro'])) {
            $q->orWhereHas('valoresCampos', function ($vcq) {
                $vcq->whereHas('campo', function ($cq) {
                    $cq->where('name', 'retirados_practica');
                })->whereNotNull('valor')
                  ->where('valor', '!=', '[]')
                  ->where('valor', '!=', '{}');
            });
        }

        // ========== 6. BÚSQUEDA POR VENCIDO ==========
        if (in_array($searchLower, ['vencido', 'vencidos', 'vencida'])) {
            $q->orWhere('vencido', 1);
        }

        // ========== 7. BÚSQUEDA POR DESHABILITADO ==========
        if (in_array($searchLower, ['deshabilitado', 'deshabilitados', 'deshabilitada', 'deshabilitadas'])) {
            $q->orWhere('deshabilitado', 1);
        }

        // ========== 7. BÚSQUEDA POR SEGUNDO INTEGRANTE ==========
        $q->orWhereHas('valoresCampos', function ($vcq) use ($search) {
            $vcq->whereHas('campo', function ($cq) {
                $cq->where('name', 'id_integrante_2');
            })->whereHas('practica', function ($pq) use ($search) {
                $pq->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('nro_documento', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                });
            });
        });

        // ========== 8. BÚSQUEDA POR USUARIO (primer integrante) ==========
        $q->orWhereHas('user', function ($uq) use ($search) {
            $uq->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('nro_documento', 'LIKE', "%{$search}%")
                ->orWhere('nro_celular', 'LIKE', "%{$search}%");
        });

        // ========== 9. BÚSQUEDA POR NIVEL ==========
        $q->orWhereHas('user.nivel', function ($nq) use ($search) {
            $nq->where('nombre', 'LIKE', "%{$search}%");
        });

        // ========== 10. BÚSQUEDA POR TÍTULO O EMPRESA ==========
        $q->orWhereHas('valoresCampos', function ($vcq) use ($search) {
            $vcq->whereHas('campo', function ($cq) {
                $cq->whereIn('name', ['titulo', 'nombre_empresa']);
            })->where('valor', 'LIKE', "%{$search}%");
        });

        // ========== 11. BÚSQUEDA POR DESCRIPCIÓN ==========
        // Buscar en la descripción de la práctica (columna descripcion en la tabla practicas)
        // o en la descripción de los valores de campos
        $q->orWhereHas('valoresCampos', function ($vcq) use ($search) {
            $vcq->whereHas('campo', function ($cq) {
                $cq->where('name', 'descripcion');
            })->where('valor', 'LIKE', "%{$search}%");
        });

        // Buscar también en el campo 'objetivo' o 'descripcion' si existen
        $q->orWhereHas('valoresCampos', function ($vcq) use ($search) {
            $vcq->whereHas('campo', function ($cq) {
                $cq->whereIn('name', ['descripcion', 'objetivo', 'observaciones', 'comentarios']);
            })->where('valor', 'LIKE', "%{$search}%");
        });
    });
}

        $query->orderBy('id', 'desc');

        return DataTables::of($query)
            ->addColumn('formatted_id', function ($p) {
                return 'GRA-00' . $p->id;
            })
            ->addColumn('descripcion', function ($p) {
                switch ($p->estado) {
                    case 'Pendiente':
                        return 'Solicitud de prácticas empresariales';

                    case 'Fase 1':
                        return 'Envío del formato F-DC-126';

                    case 'Fase 2':
                        return 'Pago y liquidación de la modalidad';

                    case 'Fase 3':
                        return 'Propuesta de grado I';

                    case 'Fase 4':
                        return 'Propuesta de grado II';

                    case 'Fase 5':
                        return 'Informe Final I';

                    case 'Fase 6':
                        return 'Informe Final II';

                    case 'Finalizado':
                        return 'Práctica empresarial finalizada';

                    case 'Rechazada':
                    return 'Solicitud de prácticas rechazada';

                    case 'Aplazada':
                        return 'Solicitud de prácticas aplazada';

                    default:
                        return 'Solicitud de prácticas empresariales';
                }
            })
            ->addColumn('estado', function ($p) {
                $return_html = '<div class="flex gap-2 flex-wrap items-center justify-center">';

                $esEstudiante = auth()->user()->hasRole('estudiante');
                $esDirector = auth()->user()->hasRole('director_practica');
                $esEvaluador = auth()->user()->hasRole('evaluador_practica');
                $esComite = auth()->user()->hasRole(['super_admin', 'admin', 'coordinador']);

                // ========== VERIFICAR FINALIZACIÓN Y BENEFICIARIO ==========
$finalizadaPorIcfes = false;
$beneficiarioActual = false;
$badgeFinalizado = '';
$badgeBeneficiario = '';

// Verificar si la práctica tiene segundo integrante
$tieneSegundoIntegrante = false;
$campoIntegrante2 = Campo::where('name', 'id_integrante_2')
    ->where('tipo_solicitud_id', $p->tipo_solicitud_id)
    ->first();

if ($campoIntegrante2) {
    $valorIntegrante2 = $p->valoresCampos
        ->where('campo_id', $campoIntegrante2->id)
        ->first();
    if ($valorIntegrante2 && !empty($valorIntegrante2->valor)) {
        $tieneSegundoIntegrante = true;
    }
}

if ($esEstudiante) {
    // Para estudiantes: verificar si es beneficiario
    $beneficiarioActual = $this->esBeneficiarioIcfesListaPractica($p);
    if ($beneficiarioActual) {
        $finalizadaPorIcfes = $this->practicaFinalizadaPorIcfes($p);
    }
} else {
    // Para otros roles:
    if (!$tieneSegundoIntegrante) {
        // Práctica de 1 integrante: verificar si es beneficiario
        $beneficiarioActual = $this->practicaFinalizadaPorIcfesGlobal($p);
        $finalizadaPorIcfes = $beneficiarioActual;
    } else {
        // Práctica de 2 integrantes: NUNCA mostrar beneficiario
        $beneficiarioActual = false;
        // Solo verificar si TODOS son beneficiarios para mostrar "Finalizado"
        $finalizadaPorIcfes = $this->practicaFinalizadaPorIcfesGlobal($p);
    }
}

$badgeFinalizado = $finalizadaPorIcfes ? '<span class="shadow bg-green-100 text-green-800 text-sm font-medium px-2.5 py-0.5 rounded border border-green-300">Finalizado</span>' : '';
$badgeBeneficiario = $beneficiarioActual ? '<span class="shadow bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded border border-blue-300">Beneficiario ICFES</span>' : '';
                
                
                // ========== SI LA PRÁCTICA ESTÁ FINALIZADA POR ICFES ==========
if ($finalizadaPorIcfes) {
    $htmlEstado = $badgeFinalizado;
    
    // Solo mostrar beneficiario si aplica
    if ($beneficiarioActual) {
        $htmlEstado .= ' ' . $badgeBeneficiario;
    }
    
    return $return_html . $htmlEstado . '</div>';
}
                
                // Dentro de addColumn('estado'), antes de los demás badges
            if (auth()->user()->hasRole('estudiante')) {
                $estaRetirado = $this->estudianteRetirado($p);
                if ($estaRetirado) {
                    $badge = "<span class='shadow bg-red-100 text-red-800 text-sm font-medium px-2.5 py-0.5 rounded border border-red-300'>Retirado</span>";
                    return $return_html . $badge . "</div>";
                }
            }

            // Verificar si está vencida
if ($p->vencido == 1) {
    // Primero construir el estado y actor normal
    $estadoActual = '';
    $actorActual = '';
    
    // Obtener el estado y actor según la fase
    if ($p->estado === 'Pendiente') {
        $estadoActual = 'Pendiente';
        $actorActual = 'Comité';
    } elseif ($p->estado === 'Fase 1') {
        $submited = $p->valoresCampos->where('campo.name', 'submited_fase1')->first();
        $yaEnvio = $submited && $submited->valor === 'true';
        $estadoActual = 'Fase 1';
        $actorActual = $yaEnvio ? 'Comité' : 'Estudiante';
    } elseif ($p->estado === 'Fase 2') {
        $submited = $p->valoresCampos->where('campo.name', 'submited_fase2')->first();
        $yaEnvio = $submited && $submited->valor === 'true';
        $estadoActual = 'Fase 2';
        $actorActual = $yaEnvio ? 'Comité' : 'Estudiante';
    } elseif ($p->estado === 'Fase 3') {
        $submited = $p->valoresCampos->where('campo.name', 'submited_fase3')->first();
        $yaEnvio = $submited && $submited->valor === 'true';
        $estadoActual = 'Fase 3';
        $actorActual = $yaEnvio ? 'Director' : 'Estudiante';
    } elseif ($p->estado === 'Fase 4') {
        $estadoEvaluador = $p->valoresCampos->where('campo.name', 'estado_evaluador_fase4')->first();
        $respondioEvaluador = $estadoEvaluador && !empty($estadoEvaluador->valor);
        $estadoActual = 'Fase 4';
        $actorActual = $respondioEvaluador ? 'Comité' : 'Evaluador';
    } elseif ($p->estado === 'Fase 5') {
        $submited = $p->valoresCampos->where('campo.name', 'submited_fase5')->first();
        $yaEnvio = $submited && $submited->valor === 'true';
        $estadoActual = 'Fase 5';
        $actorActual = $yaEnvio ? 'Director' : 'Estudiante';
    } elseif ($p->estado === 'Fase 6') {
        $estadoEvaluador = $p->valoresCampos->where('campo.name', 'estado_evaluador_fase6')->first();
        $respondioEvaluador = $estadoEvaluador && !empty($estadoEvaluador->valor);
        $estadoActual = 'Fase 6';
        $actorActual = $respondioEvaluador ? 'Comité' : 'Evaluador';
    } elseif ($p->estado === 'Finalizado') {
        $estadoActual = 'Finalizado';
        $actorActual = '';
    }
    
    $badgeEstado = "<span class='shadow bg-uts-300 text-sm font-medium px-2.5 py-0.5 rounded border border-uts-500'>$estadoActual</span>";
    $badgeActor = $actorActual ? "<span class='shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300'>$actorActual</span>" : '';
    $badgeVencida = "<span class='shadow bg-red-100 text-red-800 text-sm font-medium px-2.5 py-0.5 rounded border border-red-300'>Vencida</span>";
    
    return $return_html . $badgeEstado . ' ' . $badgeActor . ' ' . $badgeVencida . '</div>';
}

            if (
                auth()->user()->hasRole('estudiante')
                && $beneficiarioActual
                && !$practicaFinalizadaIcfes
            ) {

                return $return_html .
                    "<span class='shadow bg-green-100 text-green-800 text-sm font-medium px-2.5 py-0.5 rounded border border-green-300'>
                        Finalizado
                    </span>
                    <span class='shadow bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded border border-blue-300'>
                        Beneficiario ICFES
                    </span>
                    </div>";
            }
            
                // ========== BADGE BENEFICIARIO ICFES ==========
                //$acceso = $this->esBeneficiarioIcfesListaPractica($p);
                
                // Badge para beneficiario ICFES (solo estudiantes en Fase 5 o 6)
                //$badge_beneficiario_icfes = '<span class="shadow bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded border border-blue-300">Beneficiario ICFES</span>';
               
                if ($p->estado === 'Aplazada') {
                    $badge = "<span class='shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300'>Aplazada</span>";
                    return $return_html . $badge . "</div>";
                }

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

    if ($yaEnvio) {
        $htmlEstado = "
            <span class='shadow bg-uts-300 text-sm font-medium px-2.5 py-0.5 rounded border border-uts-500'>Fase 5</span>
            <span class='shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300'>Director</span>
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
        ->where('campo.name', 'estado_evaluador_fase6')
        ->first();

    $respondioEvaluador = $estadoEvaluador && !empty($estadoEvaluador->valor);

    if ($respondioEvaluador) {
        $htmlEstado = "
            <span class='shadow bg-uts-300 text-sm font-medium px-2.5 py-0.5 rounded border border-uts-500'>Fase 6</span>
            <span class='shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300'>Comité</span>
        ";
    } else {
        $htmlEstado = "
            <span class='shadow bg-uts-300 text-sm font-medium px-2.5 py-0.5 rounded border border-uts-500'>Fase 6</span>
            <span class='shadow bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded border border-yellow-300'>Evaluador</span>
        ";
    }
}
                
                elseif ($p->estado === 'Finalizado') {
                    $htmlEstado = "<span class='shadow bg-green-100 text-green-800 text-sm font-medium px-2.5 py-0.5 rounded border border-green-300'>Finalizado</span>";
                }

                if ($p->deshabilitado && $p->estado !== 'Rechazada') {
                    $deshabilitadoBadge = "<span class='shadow bg-red-100 text-red-800 text-sm font-medium px-2.5 py-0.5 rounded border border-red-300'>Deshabilitado</span>";
                    return $return_html . $htmlEstado . ' ' . $deshabilitadoBadge . "</div>";
                }

                return $return_html . $htmlEstado . "</div>";
            })
            ->addColumn('acciones', function ($p) use ($rol_especifico) {
                $user = auth()->user();
                $buttons = '<div class="flex items-center justify-center gap-2">';

                $finalizadaPorIcfes = $this->practicaFinalizadaPorIcfes($p);
                
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
                // Si está finalizada por ICFES, también debe poder ver el roadmap
                $finalizadaPorIcfes = $this->practicaFinalizadaPorIcfes($p);

                $puedeVerRoadmap = !$p->deshabilitado && $esFaseActiva && $p->vencido != 1;

                if ($finalizadaPorIcfes) {
                    $puedeVerRoadmap = true;
                }

                // Si es estudiante, verificar si está retirado
                if (auth()->user()->hasRole('estudiante')) {
                    $estaRetirado = $this->estudianteRetirado($p);
                    if ($estaRetirado) {
                        $puedeVerRoadmap = false;
                    }
                }

                if ($puedeVerRoadmap) {

                    $rutaRoadmap = route('practicas.roadmap');

                    if (($rol_especifico ?? null) === 'director_practica') {
                        $rutaRoadmap = route('director.roadmap');
                    }

                    if (($rol_especifico ?? null) === 'evaluador_practica') {
                        $rutaRoadmap = route('evaluador.roadmap');
                    }

                    $buttons .= '
                        <form action="' . $rutaRoadmap . '" method="POST" class="inline-block m-0" onsubmit="return showRoadmapSpinner(this)">
                            ' . csrf_field() . '
                            <input type="hidden" name="practica_id" value="' . $p->id . '">
                            <input type="hidden" name="rol_especifico" value="' . ($rol_especifico ?? '') . '">

                            <button type="submit" class="btn-action shadow bg-indigo-500 hover:bg-indigo-800 text-white rounded-lg inline-flex items-center justify-center">
                                <i class="fa-solid fa-map-location-dot"></i>
                                <svg class="loading-spinner hidden text-white animate-spin" viewBox="0 0 64 64" fill="none">
                                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 58.7925 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                                </svg>
                            </button>
                        </form>';
                }

                if ($finalizadaPorIcfes) {
                    $puedeVerRoadmap = true;
                }

                // Botón Deshabilitar/Habilitar (SOLO para Comité)
// Si está vencida, mostrar botón de HABILITAR
if ($esComite && $p->estado !== 'Finalizado' && $esFaseActiva && $p->estado !== 'Rechazada') {
    if ($p->vencido == 1) {
        // Si está vencida, mostrar botón de habilitar
        $buttons .= '<button onclick="habilitarPracticaConActa(' . $p->id . ')"
            class="btn-action shadow bg-teal-500 hover:bg-teal-800 text-white px-3 py-1 rounded-lg relative">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </button>';
    } elseif (!$p->deshabilitado) {
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

    $beneficiarios = json_decode($valorBeneficiario->valor, true);
    
    if (!is_array($beneficiarios)) {
        $beneficiarios = [];
    }
    
    $beneficiarios = array_map('intval', $beneficiarios);
    $userId = (int) auth()->id();
    
    return in_array($userId, $beneficiarios);
}

    private function yaEnvioSolicitudIcfes($practica)
    {
        $userId = (string) auth()->id();
        
        $campoSubmited = Campo::where('name', 'submited_icfes_practicas')
            ->where('tipo_solicitud_id', $practica->tipo_solicitud_id)
            ->first();
        
        if (!$campoSubmited) {
            return false;
        }
        
        $valor = $practica->valoresCampos
            ->where('campo_id', $campoSubmited->id)
            ->first();
        
        if (!$valor || !$valor->valor) {
            return false;
        }
        
        $submitedData = json_decode($valor->valor, true) ?: [];
        
        return isset($submitedData[$userId]) && $submitedData[$userId] === true;
    }

    private function practicaFinalizadaPorIcfes($practica)
{
    $campoBeneficiarios = Campo::where('name', 'beneficiarios_icfes_practicas')
        ->where('tipo_solicitud_id', $practica->tipo_solicitud_id)
        ->first();

    if (!$campoBeneficiarios) {
        return false;
    }

    $valorBeneficiarios = $practica->valoresCampos
        ->where('campo_id', $campoBeneficiarios->id)
        ->first();

    if (!$valorBeneficiarios || !$valorBeneficiarios->valor) {
        return false;
    }

    $beneficiarios = json_decode($valorBeneficiarios->valor, true);

    if (!is_array($beneficiarios)) {
        return false;
    }

    $beneficiarios = array_map('intval', $beneficiarios);
    $userId = (int) auth()->id();

    // Si el usuario actual NO está en la lista de beneficiarios, retorna false
    if (!in_array($userId, $beneficiarios)) {
        return false;
    }

    // Si es el único integrante, la práctica está finalizada por ICFES
    $campoIntegrante2 = Campo::where('name', 'id_integrante_2')
        ->where('tipo_solicitud_id', $practica->tipo_solicitud_id)
        ->first();

    if (!$campoIntegrante2) {
        return true; // Práctica individual
    }

    $valorIntegrante2 = $practica->valoresCampos
        ->where('campo_id', $campoIntegrante2->id)
        ->first();

    if (!$valorIntegrante2 || empty($valorIntegrante2->valor)) {
        return true; // No hay segundo integrante
    }

    $integrante2 = (int) $valorIntegrante2->valor;

    // Si el usuario actual es el primer integrante
    if ($userId === (int) $practica->user_id) {
        // El primer integrante SIEMPRE puede ver la práctica como finalizada si es beneficiario
        // (independientemente del segundo integrante)
        return true;
    }

    // Si el usuario actual es el segundo integrante
    if ($userId === $integrante2) {
        // Verificar si el primer integrante también es beneficiario
        $integrante1 = (int) $practica->user_id;
        return in_array($integrante1, $beneficiarios);
    }

    return false;
}

    private function practicaFinalizadaPorIcfesGlobal($practica)
{

 // DEPURACIÓN: Ver todos los campos de esta práctica
    $todosLosCampos = $practica->valoresCampos()->with('campo')->get();
    
    \Log::info('=== CAMPOS DE LA PRÁCTICA ID: ' . $practica->id . ' ===');
    foreach ($todosLosCampos as $valor) {
        \Log::info('Campo: ' . ($valor->campo->name ?? 'NULL') . ' | Valor: ' . $valor->valor);
    }
    \Log::info('=== FIN CAMPOS ===');
    $campoBeneficiarios = Campo::where('name', 'beneficiarios_icfes_practicas')
        ->where('tipo_solicitud_id', $practica->tipo_solicitud_id)
        ->first();

    if (!$campoBeneficiarios) {
        return false;
    }

    $valorBeneficiarios = $practica->valoresCampos
        ->where('campo_id', $campoBeneficiarios->id)
        ->first();

    if (!$valorBeneficiarios || !$valorBeneficiarios->valor) {
        return false;
    }

    $beneficiarios = json_decode($valorBeneficiarios->valor, true);

    if (!is_array($beneficiarios) || empty($beneficiarios)) {
        return false;
    }

    $beneficiarios = array_map('intval', $beneficiarios);
    
    // Obtener todos los integrantes de la práctica
    $integrantes = [(int) $practica->user_id];
    
    // Buscar el segundo integrante usando el campo id_integrante_2
    $campoIntegrante2 = Campo::where('name', 'id_integrante_2')
        ->where('tipo_solicitud_id', $practica->tipo_solicitud_id)
        ->first();
    
    if ($campoIntegrante2) {
        $valorIntegrante2 = $practica->valoresCampos
            ->where('campo_id', $campoIntegrante2->id)
            ->first();
        
        if ($valorIntegrante2 && !empty($valorIntegrante2->valor)) {
            $integrantes[] = (int) $valorIntegrante2->valor;
        }
    }

    // Si no se encontró segundo integrante, es una práctica individual
    if (count($integrantes) === 1) {
        // Solo verificar que el único integrante sea beneficiario
        return in_array($integrantes[0], $beneficiarios);
    }

    // Verificar si TODOS los integrantes están en la lista de beneficiarios
    foreach ($integrantes as $integranteId) {
        if (!in_array($integranteId, $beneficiarios)) {
            return false;
        }
    }

    return true;
}

    private function estudianteRetirado($practica)
    {
        if (!auth()->user()->hasRole('estudiante')) {
            return false;
        }
        
        $campoRetirados = Campo::where('name', 'retirados_practica')
            ->where('tipo_solicitud_id', $practica->tipo_solicitud_id)
            ->first();
        
        if (!$campoRetirados) {
            return false;
        }
        
        $valor = $practica->valoresCampos
            ->where('campo_id', $campoRetirados->id)
            ->first();
        
        if (!$valor || !$valor->valor) {
            return false;
        }
        
        $retirados = json_decode($valor->valor, true);
        
        if (!is_array($retirados)) {
            return false;
        }
        
        $retirados = array_map('intval', $retirados);
        $userId = (int) auth()->id();
        
        return in_array($userId, $retirados);
    }

    public function buscarEstudiantes(Request $request)
{
    try {
        $search = $request->get('search');

        if (strlen($search) < 5) {
            return response()->json([]);
        }

        $userId = auth()->id();
        $userNivelId = auth()->user()->nivel_id;

        // Estados que se consideran "activos" (no se puede agregar a estos estudiantes)
        $estadosActivos = ['Pendiente', 'Fase 1', 'Fase 2', 'Fase 3', 'Fase 4', 'Fase 5', 'Fase 6'];

        $estudiantes = \App\Models\User::where('id', '!=', $userId)
            ->where('nivel_id', $userNivelId)
            ->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('nro_documento', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            })
            // Excluir estudiantes que tienen prácticas activas (TODAS las fases)
            ->whereNotIn('id', function ($subquery) use ($estadosActivos) {
                $subquery->select('user_id')
                    ->from('practicas')
                    ->whereIn('estado', $estadosActivos)
                    ->where('tipo_solicitud_id', '>=', 9);
            })
            // Excluir estudiantes que son segundo integrante en prácticas activas
            ->whereNotIn('id', function ($subquery) use ($estadosActivos) {
                $subquery->select('valor')
                    ->from('practica_valores_campos')
                    ->whereIn('practica_id', function ($q) use ($estadosActivos) {
                        $q->select('id')
                            ->from('practicas')
                            ->whereIn('estado', $estadosActivos)
                            ->where('tipo_solicitud_id', '>=', 9);
                    })
                    ->where('campo_id', function ($cq) {
                        $cq->select('id')
                            ->from('campos')
                            ->where('name', 'id_integrante_2')
                            ->where('tipo_solicitud_id', '>=', 9);
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
        
        $user = auth()->user();
    
    // Verificar si el estudiante ya tiene una práctica activa
    if ($this->tienePracticaActiva($user->id)) {
    return response()->json([
        'errors' => [
            'general' => 'No puedes solicitar una nueva práctica porque ya tienes una práctica activa.'
        ]
    ], 422);
}
    
    $practica = $this->practicaService
            ->crearPractica($request);
        
        //Envia correo al comite
        $this->practicaMailService ->sendSolicitud($practica);

        return response()->json([
            'message' => 'Práctica enviada correctamente'
        ]);
    }
    
    public function responderSolicitud(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'solicitudPractica_id' => 'required',
            'estado'               => 'required|in:Aprobada,Rechazada,Aplazada',
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
                'Fase 5'    => 'Fase 6',
                'Fase 6'    => 'Finalizado',
                default     => $estadoActual
            };
             $this->practicaMailService->sendRespuesta($practica,$estadoActual,$nuevoEstado,$request->mensaje,$request->estado);
        } else {
            // Si es rechazada o aplazada, el nuevo estado es 'Rechazada' o 'Aplazada'
            $nuevoEstado = $request->estado;
            $this->practicaMailService->sendRespuesta($practica,$estadoActual,$nuevoEstado,$request->mensaje,$request->estado);
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

            // Obtener fechas de propuesta
    $fechasPropuesta = $this->getFechasPropuesta($practica);
    
    // Obtener fechas de informe
    $fechasInforme = $this->getFechasInforme($practica);

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
            
            // Título - Buscar en orden jerárquico
        $titulo = 'No disponible';

        if (isset($data['titulo_propuesta_fase4']) && !empty($data['titulo_propuesta_fase4'])) {
            $titulo = $data['titulo_propuesta_fase4'];
        } elseif (isset($data['titulo_propuesta_director_fase3']) && !empty($data['titulo_propuesta_director_fase3'])) {
            $titulo = $data['titulo_propuesta_director_fase3'];
        } elseif (isset($data['titulo']) && !empty($data['titulo'])) {
            $titulo = $data['titulo'];
        }
            
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
                'es_estudiante' => $esEstudiante,
                'fechas_propuesta' => $fechasPropuesta,
                'fechas_informe' => $fechasInforme
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

        $this->practicaMailService
            ->sendEstadoHabilitacion($practica, 'practica_deshabilitada');

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

    $practica = Practica::findOrFail($request->practica_id_activar);
    
    // ========== VALIDACIÓN: Verificar si los estudiantes tienen otra práctica activa ==========
    // Obtener el primer integrante
    $primerIntegrante = $practica->user_id;
    
    // Obtener el segundo integrante si existe
    $segundoIntegrante = null;
    $campos = $practica->CamposConValores();
    foreach ($campos as $item) {
        if (str_contains($item['campo']['name'], 'id_integrante_2')) {
            $segundoIntegrante = $item['valor'];
            break;
        }
    }
    
    // Verificar si el primer integrante tiene otra práctica activa
    if ($this->tienePracticaActivaExcluyendo($primerIntegrante, $practica->id)) {
        $usuario = User::find($primerIntegrante);
        return response()->json([
            'success' => false,
            'message' => 'No se puede habilitar la práctica. El estudiante ' . ($usuario ? $usuario->name : $primerIntegrante) . ' ya tiene otra práctica activa.'
        ], 422);
    }
    
    // Verificar si el segundo integrante tiene otra práctica activa
    if ($segundoIntegrante && $this->tienePracticaActivaExcluyendo($segundoIntegrante, $practica->id)) {
        $usuario = User::find($segundoIntegrante);
        return response()->json([
            'success' => false,
            'message' => 'No se puede habilitar la práctica. El estudiante ' . ($usuario ? $usuario->name : $segundoIntegrante) . ' ya tiene otra práctica activa.'
        ], 422);
    }
    // ========== FIN VALIDACIÓN ==========

    $practica->deshabilitado = false;
    $practica->vencido = 0; // También resetear vencido si estaba vencida
    $practica->save();

    ActaPractica::create([
        'practica_id' => $practica->id,
        'numero'      => $request->nro_acta_activar,
        'fecha'       => $request->fecha_acta_activar,
        'descripcion' => $request->descripcion_activar,
    ]);

    $this->practicaMailService
        ->sendEstadoHabilitacion($practica, 'practica_habilitada');

    return response()->json(['success' => 'Práctica habilitada correctamente']);
}

    private function getFechasPropuesta($practica)
    {
        $fechas = [
            'envio_estudiante' => 'No disponible',
            'revision_director' => 'No disponible', 
            'revision_evaluador' => 'No disponible'
        ];
        
        // 1. Envío de propuesta (estudiante) - submited_fase3
        $campoEnvio = $practica->valoresCampos->where('campo.name', 'submited_fase3')->first();
        if ($campoEnvio && $campoEnvio->valor === 'true' && $campoEnvio->updated_at) {
            $fechas['envio_estudiante'] = $campoEnvio->updated_at->format('d/m/Y, H:i');
        }
        
        // 2. Revisión director - estado_director_fase3 (solo si fue aprobado)
        $campoDirector = $practica->valoresCampos->where('campo.name', 'estado_director_fase3')->first();
        if ($campoDirector && $campoDirector->valor === 'Aprobada' && $campoDirector->updated_at) {
            $fechas['revision_director'] = $campoDirector->updated_at->format('d/m/Y, H:i');
        }
        
        // 3. Revisión evaluador - estado_evaluador_fase4 (solo si fue aprobado)
        $campoEvaluador = $practica->valoresCampos->where('campo.name', 'estado_evaluador_fase4')->first();
        if ($campoEvaluador && $campoEvaluador->valor === 'Aprobada' && $campoEvaluador->updated_at) {
            $fechas['revision_evaluador'] = $campoEvaluador->updated_at->format('d/m/Y, H:i');
        }
        
        return $fechas;
    }

    private function getFechasInforme($practica)
    {
        $fechas = [
            'envio_estudiante' => 'No disponible',
            'revision_director' => 'No disponible',
            'revision_evaluador' => 'No disponible'
        ];
        
        // 1. Envío de informe (estudiante) - submited_fase5
        $campoEnvio = $practica->valoresCampos->where('campo.name', 'submited_fase5')->first();
        if ($campoEnvio && $campoEnvio->valor === 'true' && $campoEnvio->updated_at) {
            $fechas['envio_estudiante'] = $campoEnvio->updated_at->format('d/m/Y, H:i');
        }
        
        // 2. Revisión director - estado_director_fase5 (solo si fue aprobado)
        $campoDirector = $practica->valoresCampos->where('campo.name', 'estado_director_fase5')->first();
        if ($campoDirector && $campoDirector->valor == 'Aprobada' && $campoDirector->updated_at) {
            $fechas['revision_director'] = $campoDirector->updated_at->format('d/m/Y, H:i');
        }
        
        // 3. Revisión evaluador - estado_evaluador_fase6 (solo si fue aprobado)
        $campoEvaluador = $practica->valoresCampos->where('campo.name', 'estado_evaluador_fase6')->first();
        if ($campoEvaluador && $campoEvaluador->valor == 'Aprobada' && $campoEvaluador->updated_at) {
            $fechas['revision_evaluador'] = $campoEvaluador->updated_at->format('d/m/Y, H:i');
        }
        
        return $fechas;
    }

    public function generarReportePracticas(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'periodo_reporte' => 'required',
            ], [
                'periodo_reporte.required' => 'El campo periodo es obligatorio',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $periodo = $request->periodo_reporte;
            
            // Crear directorio si no existe
            $formatosPath = public_path('formatos');
            if (!file_exists($formatosPath)) {
                mkdir($formatosPath, 0777, true);
            }
            
            $formato_reporte = $formatosPath . '/informe_practicas.xlsx';

            // Si no existe el formato, crear uno nuevo
            if (!file_exists($formato_reporte)) {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                
                // Título
                $sheet->mergeCells('A1:R1');
                $sheet->setCellValue('A1', 'Unidades Tecnológicas de Santander');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $sheet->mergeCells('A2:R2');
                $sheet->setCellValue('A2', 'Informe de prácticas empresariales');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $sheet->mergeCells('A3:R3');
                $sheet->setCellValue('A3', 'Ingeniería de sistemas');
                $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Encabezados
                $headers = [
                    'ID', 'Código modalidad', 'Título del proyecto', 'Modalidad', 
                    'Nivel académico', 'Línea de investigación', 'Tipo de idea', 
                    'Estado', 'Integrantes', 'Documento', 'Correo electrónico', 
                    'Celular', 'Director', 'Evaluador', 'Actas de registro', 
                    'Inicio Práctica', 'Aprobación Propuesta', 'Fin Práctica'
                ];
                
                $col = 'A';
                foreach ($headers as $header) {
                    $sheet->setCellValue($col . '5', $header);
                    $sheet->getStyle($col . '5')->getFont()->setBold(true);
                    $sheet->getStyle($col . '5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getColumnDimension($col)->setWidth(20);
                    $col++;
                }
                
                // Guardar el formato
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save($formato_reporte);
            }

            // Cargar el archivo de Excel existente
            $spreadsheet = IOFactory::load($formato_reporte);
            $sheet = $spreadsheet->getActiveSheet();
            $fila = 6;

            // ============ CONSULTA SIMPLIFICADA ============
            // Traer TODAS las prácticas con estados válidos
            $practicas = Practica::with(['user', 'user.nivel', 'valoresCampos.campo'])
                ->whereIn('estado', ['Fase 1','Fase 2', 'Fase 3', 'Fase 4', 'Fase 5', 'Fase 6', 'Pendiente', 'Finalizado', 'vencida', 'retirado'])
                ->get();

            foreach ($practicas as $practica) {
                $campos = $practica->valoresCampos->pluck('valor', 'campo.name')->toArray();

                // Obtener el periodo de la práctica
                $periodoPractica = $campos['periodo'] ?? 'No disponible';
                
                // Si la práctica no tiene el periodo seleccionado o no coincide con el filtro, la saltamos
                if ($periodoPractica !== $periodo) {
                    continue;
                }

                $id = 'GRA-00' . $practica->id;
                $codigo_modalidad = $campos['codigo_modalidad'] ?? 'No disponible';
                $titulo = mb_strtoupper($this->getTituloPractica($practica)) ?? 'No disponible';
                
                $modalidad = 'Prácticas empresariales';
                $nivel = $practica->user->nivel->nombre ?? 'No disponible';
                $linea_investigacion = $campos['linea_investigacion'] ?? 'No disponible';
                $tipo_idea = $campos['tipo_idea'] ?? 'No disponible';
                
                $estado = $practica->estado ?? 'No disponible';
                if ($practica->vencido) $estado .= ' (Vencido)';
                if ($practica->deshabilitado) $estado .= ' (Deshabilitado)';

                // Integrantes
                $integrante_1_id = $practica->user_id;
                $integrante_2_id = $campos['id_integrante_2'] ?? null;

                $beneficiarios_icfes = $campos['beneficiarios_icfes_practicas'] ?? '[]';
                $beneficiarios_icfes = json_decode($beneficiarios_icfes, true) ?? [];

                $integrantes = '';
                $documentos = '';
                $emails = '';
                $nros_celulares = '';

                // Integrante 1
                if ($integrante_1_id) {
                    $integrante_1 = User::find($integrante_1_id);
                    if ($integrante_1) {
                        $tipo_documento = TipoDocumento::find($integrante_1->tipo_documento_id);
                        $documento = ($tipo_documento ? $tipo_documento->tag : 'CC') . " " . ($integrante_1->nro_documento ?? 'N/A');
                        
                        $integrantes = mb_strtoupper($integrante_1->name);
                        $documentos = $documento;
                        $emails = $integrante_1->email ?? 'N/A';
                        $nros_celulares = $integrante_1->nro_celular ?? 'N/A';

                        if ($beneficiarios_icfes && in_array($integrante_1->id, $beneficiarios_icfes)) {
                            $integrantes .= ' - BENEFICIARIO ICFES';
                        }
                    }
                }

                // Integrante 2
                if ($integrante_2_id) {
                    $integrante_2 = User::find($integrante_2_id);
                    if ($integrante_2) {
                        $tipo_documento = TipoDocumento::find($integrante_2->tipo_documento_id);
                        $documento = ($tipo_documento ? $tipo_documento->tag : 'CC') . " " . ($integrante_2->nro_documento ?? 'N/A');
                        
                        $integrantes .= "\n" . mb_strtoupper($integrante_2->name);
                        $documentos .= "\n" . $documento;
                        $emails .= "\n" . ($integrante_2->email ?? 'N/A');
                        $nros_celulares .= "\n" . ($integrante_2->nro_celular ?? 'N/A');

                        if ($beneficiarios_icfes && in_array($integrante_2->id, $beneficiarios_icfes)) {
                            $integrantes .= ' - BENEFICIARIO ICFES';
                        }
                    }
                }

                // Director
                $director_id = $campos['director_id'] ?? null;
                $director = 'No disponible';
                if ($director_id) {
                    $director_user = User::find($director_id);
                    $director = $director_user ? mb_strtoupper($director_user->name) : 'No disponible';
                }

                // Evaluador
                $evaluador_id = $campos['evaluador_id'] ?? null;
                $evaluador = 'No disponible';
                if ($evaluador_id) {
                    $evaluador_user = User::find($evaluador_id);
                    $evaluador = $evaluador_user ? mb_strtoupper($evaluador_user->name) : 'No disponible';
                }

                // Actas
                $actas = ActaPractica::where('practica_id', $practica->id)
                    ->orderBy('numero', 'asc')
                    ->get();

                $actas_registro = $actas->map(function ($acta) {
                    $fecha = Carbon::parse($acta->fecha);
                    return "Nro. {$acta->numero} - Fecha: {$fecha->format('d-m-Y')} - {$acta->descripcion}";
                })->implode("\n");

                // Fechas
                $inicio_practica = $actas->firstWhere('descripcion', 'Aprobación del pago de la modalidad');
                $inicio_practica = $inicio_practica ? Carbon::parse($inicio_practica->fecha)->format('d-m-Y') : 'No disponible';
                
                $aprobacion_propuesta = $actas->firstWhere('descripcion', 'Aprobación de la propuesta');
                $aprobacion_propuesta = $aprobacion_propuesta ? Carbon::parse($aprobacion_propuesta->fecha)->format('d-m-Y') : 'No disponible';
                
                $fin_practica = $actas->firstWhere('descripcion', 'Aprobación del informe final');
                $fin_practica = $fin_practica ? Carbon::parse($fin_practica->fecha)->format('d-m-Y') : 'No disponible';

                // Insertar valores
                $sheet->setCellValue("A{$fila}", $id);
                $sheet->setCellValue("B{$fila}", $codigo_modalidad);
                $sheet->setCellValue("C{$fila}", $titulo);
                $sheet->setCellValue("D{$fila}", $modalidad);
                $sheet->setCellValue("E{$fila}", $nivel);
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
                $sheet->setCellValue("P{$fila}", $inicio_practica);
                $sheet->setCellValue("Q{$fila}", $aprobacion_propuesta);
                $sheet->setCellValue("R{$fila}", $fin_practica);

                // Aplicar estilos
                foreach (range('A', 'R') as $col) {
                    $sheet->getStyle("{$col}{$fila}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle("{$col}{$fila}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("{$col}{$fila}")->getAlignment()->setWrapText(true);
                }

                $fila++;
            }

            // Definir el nombre del archivo a descargar
            $fileName = "Informe - Prácticas empresariales ({$periodo}).xlsx";

            // Guardar en un stream para descargar
            $response = new StreamedResponse(function () use ($spreadsheet) {
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save('php://output');
            });

            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', "attachment; filename=\"{$fileName}\"");

            return $response;
            
        } catch (Exception $e) {
            Log::error('Error en generarReportePracticas: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['message' => 'Ha ocurrido un error: ' . $e->getMessage()], 500);
        }
    }

    private function getTituloPractica($practica)
    {
        $campos = $practica->valoresCampos->pluck('valor', 'campo.name')->toArray();
        
        if (isset($campos['titulo_propuesta_fase4']) && !empty($campos['titulo_propuesta_fase4'])) {
            return $campos['titulo_propuesta_fase4'];
        } elseif (isset($campos['titulo_propuesta_director_fase3']) && !empty($campos['titulo_propuesta_director_fase3'])) {
            return $campos['titulo_propuesta_director_fase3'];
        } elseif (isset($campos['titulo']) && !empty($campos['titulo'])) {
            return $campos['titulo'];
        }
        
        return 'No disponible';
    }

    public function tienePracticaActiva($userId): bool
{
    try {
        $type = self::getType('fase_0');
        $solicitudes = Solicitud::query()
            ->where('tipo_solicitud_id', '=', $type->id)
            ->where('vencido', '!=', 1)
            ->where('deshabilitado', '!=', 1)
            ->where('estado', '!=', 'Rechazada')
            ->where('estado', '!=', 'Finalizado')
            ->where('estado', '!=', 'Aplazada')
            ->get();

        foreach ($solicitudes as $solicitud) {
            // Verificar si el usuario es el primer integrante
            if ($solicitud->user_id == $userId) {
                return true;
            }

            // Verificar si el usuario es el segundo integrante
            $campos = $solicitud->CamposConValores();
            foreach ($campos as $item) {
                if (str_contains($item['campo']['name'], 'id_integrante_2')) {
                    if ($userId == $item['valor']) {
                        return true;
                    }
                }
            }
        }

        return false;
    } catch (Exception $e) {
        return false;
    }
}

    public function tienePracticaActivaExcluyendo($userId, $practicaIdExcluir): bool
{
    try {
        $type = self::getType('fase_0');
        $solicitudes = Solicitud::query()
            ->where('tipo_solicitud_id', '=', $type->id)
            ->where('id', '!=', $practicaIdExcluir)
            ->where('vencido', '!=', 1)
            ->where('deshabilitado', '!=', 1)
            ->where('estado', '!=', 'Rechazada')
            ->where('estado', '!=', 'Finalizado')
            ->where('estado', '!=', 'Aplazada')
            ->get();

        foreach ($solicitudes as $solicitud) {
            // Verificar si el usuario es el primer integrante
            if ($solicitud->user_id == $userId) {
                return true;
            }

            // Verificar si el usuario es el segundo integrante
            $campos = $solicitud->CamposConValores();
            foreach ($campos as $item) {
                if (str_contains($item['campo']['name'], 'id_integrante_2')) {
                    if ($userId == $item['valor']) {
                        return true;
                    }
                }
            }
        }

        return false;
    } catch (Exception $e) {
        return false;
    }
}
    
}

