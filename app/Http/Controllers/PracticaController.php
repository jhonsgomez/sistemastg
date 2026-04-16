<?php
namespace App\Http\Controllers;

use App\Models\Campo;
use App\Models\Practica;
use App\Models\TipoSolicitud;
use Illuminate\Http\Request;
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
        // Base query: solo prácticas de tipo fase 0
        $practicas = Practica::with('user')
            ->where('tipo_solicitud_id', 9);

        // Filtro por rol
        if (auth()->user()->hasRole('estudiante')) {
            $practicas->where('user_id', auth()->id());
        }
        // Para admin, coordinador, super_admin no se filtra (muestra todas)

        return DataTables::of($practicas)
            ->addColumn('descripcion', function ($practica) {
                // Construir una descripción resumida: nombre completo y documento
                // Normalizar: si es string JSON, decodificarlo; si ya es array, usarlo directamente
                $data = $practica->data;
                if (is_string($data)) {
                    $data = json_decode($data, true);
                }
                if (! is_array($data)) {
                    $data = [];
                }
                $nombre    = $practica->user->name ?? 'N/A';
                $documento = $practica->user->nro_documento ?? 'N/A';

                return "{$nombre} (Doc: {$documento})";
            })
            ->addColumn('estado', function ($practica) {
                $badge = match ($practica->estado) {
                    'Pendiente' => 'bg-yellow-100 text-yellow-800',
                    'Aprobada'  => 'bg-green-100 text-green-800',
                    'Rechazada' => 'bg-red-100 text-red-800',
                    default     => 'bg-gray-100 text-gray-800',
                };
                $html = "<span class='px-2 py-1 rounded-full text-xs font-semibold {$badge}'>{$practica->estado}</span>";
                if ($practica->estado === 'Pendiente') {
                    $html .= ' <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Comité</span>';
                }
                return $html;
            })
            ->addColumn('acciones', function ($practica) {
                $user    = auth()->user();
                $buttons = '<div class="flex items-center gap-1">';

                // 1. Botón Ver (todos los roles con permiso)
                if ($user->can('view_practicas')) {
                    $buttons .= '<button onclick="openDetailsModal(' . $practica->id . ')" class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg"><i class="fa-regular fa-eye"></i></button>';
                }

                // 2. Si es estudiante y la práctica está deshabilitada, mostrar texto en lugar del ojo
                if ($user->hasRole('estudiante')) {
                    if ($practica->deshabilitado) {
                        // Reemplazar el botón Ver por un texto
                        $buttons = '<div class="flex items-center gap-1"><span class="text-red-600 font-semibold">Deshabilitado</span></div>';
                    }
                    // Estudiante no tiene más botones
                    return $buttons;
                }

                // 3. Para comité (admin, coordinador, super_admin)
                if ($user->hasRole(['super_admin', 'admin', 'coordinador'])) {
                    // Botón Responder (solo si estado Pendiente)
                    if ($practica->estado === 'Pendiente') {
                        $buttons .= '<button onclick="responderPractica(' . $practica->id . ')" class="btn-action shadow bg-green-500 hover:bg-green-700 text-white px-3 py-1 rounded-lg"><i class="fa-regular fa-paper-plane"></i></button>';
                    }
                }

                $buttons .= '</div>';
                return $buttons;
            })
            ->rawColumns(['estado', 'acciones'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $tipo   = TipoSolicitud::where('nombre', 'practicas_fase_0')->first();
        $campos = Campo::where('tipo_solicitud_id', $tipo->id)->get();

        $tieneEmpresa = $request->input('tiene_empresa');

        if ($tieneEmpresa === null) {
            $errors['tiene_empresa'][] = 'Debe seleccionar si tiene empresa o no.';
        }

        $tieneEmpresa = $request->input('tiene_empresa') === '1';

        //  VALIDACIÓN DINÁMICA

        $errors = [];

        $tieneEmpresaInput = $request->input('tiene_empresa');

        if ($tieneEmpresaInput === null) {
            $errors['tiene_empresa'][] = 'Debe seleccionar si tiene empresa o no.';
        }

        $tieneEmpresa = $tieneEmpresaInput === '1';

        // SOLO si NO tiene empresa
        if ($tieneEmpresa === false) {

            if (! $request->hasFile('hoja_vida')) {
                $errors['hoja_vida'][] = 'Debe subir la hoja de vida si NO cuenta con empresa.';
            }
        }

        if (! empty($errors)) {
            return response()->json([
                'errors' => $errors,
            ], 422);
        }

        //GUARDAR DATA
        $data = [];

        foreach ($campos as $campo) {

            // NO guardar estos campos
            if (in_array($campo->name, ['nombre_completo', 'correo', 'nivel', 'documento', 'celular'])) {
                continue;
            }

            if ($campo->type == 'checkbox') {
                $data[$campo->name] = $request->input($campo->name) === '1';
            } elseif ($campo->type == 'file') {
                if ($campo->name == 'hoja_vida' && $tieneEmpresa) {
                    continue;
                }

                if ($request->hasFile($campo->name)) {
                    $path               = $request->file($campo->name)->store('practicas', 'public');
                    $data[$campo->name] = $path;
                }
            } else {
                $data[$campo->name] = $request->input($campo->name);
            }
        }
        Practica::create([
            'user_id'           => auth()->id(),
            'tipo_solicitud_id' => $tipo->id,
            'data'              => $data,
            'estado'            => 'Pendiente',
            'vencido'           => false,
            'deshabilitado'     => false,
        ]);

        return response()->json([
            'message' => 'Práctica enviada correctamente',
        ]);
    }

    public function getDetalle($id)
    {
        $practica = Practica::with('user')->findOrFail($id);
        $data     = $practica->data;
        if (is_string($data)) {
            $data = json_decode($data, true);
        }
        if (! is_array($data)) {
            $data = [];
        }
        return response()->json([
            'id'              => $practica->id,
            'estado'          => $practica->estado,
            'vencido'         => $practica->vencido,
            'deshabilitado'   => $practica->deshabilitado,
            'fecha_solicitud' => $practica->created_at->format('d/m/Y H:i'),
            'user'            => [
                'nombre'    => $practica->user->name,
                'correo'    => $practica->user->email,
                'nivel'     => $practica->user->nivel->nombre ?? 'N/A',
                'documento' => $practica->user->nro_documento ?? 'N/A',
                'celular'   => $practica->user->nro_celular ?? 'N/A',
            ],
            'data'            => $data,
        ]);
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

}
