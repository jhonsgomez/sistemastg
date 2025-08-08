<?php

namespace App\Http\Controllers;

use App\Imports\ProyectosImport;
use App\Models\Historico;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\DataTables\DataTables;

class HistoricoController extends Controller
{
    public function index()
    {
        try {
            return view('historico.index');
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function getData(Request $request)
    {
        try {
            $proyectos = Historico::query()->orderBy('inicio_tg', 'desc');

            return DataTables::of($proyectos)
                ->addColumn('periodo_academico', function ($proyecto) {
                    return $proyecto->periodo_academico;
                })
                ->addColumn('codigo_tg', function ($proyecto) {
                    return $proyecto->codigo_tg;
                })
                ->addColumn('nivel', function ($proyecto) {
                    return $proyecto->nivel;
                })
                ->addColumn('estudiante', function ($proyecto) {
                    return $proyecto->estudiante;
                })
                ->addColumn('correo', function ($proyecto) {
                    return $proyecto->correo;
                })
                ->addColumn('documento', function ($proyecto) {
                    return $proyecto->documento;
                })
                ->addColumn('celular', function ($proyecto) {
                    return $proyecto->celular;
                })
                ->addColumn('modalidad', function ($proyecto) {
                    return $proyecto->modalidad;
                })
                ->addColumn('titulo', function ($proyecto) {
                    return $proyecto->titulo;
                })
                ->addColumn('director', function ($proyecto) {
                    return $proyecto->director;
                })
                ->addColumn('evaluador', function ($proyecto) {
                    return $proyecto->evaluador;
                })
                ->addColumn('autores', function ($proyecto) {
                    return $proyecto->autores;
                })
                ->addColumn('inicio_tg', function ($proyecto) {
                    return $proyecto->inicio_tg;
                })
                ->addColumn('aprobacion_propuesta', function ($proyecto) {
                    return $proyecto->aprobacion_propuesta;
                })
                ->addColumn('final_tg', function ($proyecto) {
                    return $proyecto->final_tg;
                })
                ->addColumn('acciones', function ($proyecto) {
                    $proyectoJSON = htmlspecialchars(json_encode($proyecto));

                    return '
                    <div class="flex items-center justify-start container-actions">
                        <button
                            onclick="openEditProyectoModal(' . $proyectoJSON . ')" 
                            class="btn-action shadow bg-uts-500 hover:bg-uts-800 text-white px-3 py-1 rounded-lg mr-2">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteProyecto(' . $proyecto->id . ')" class="btn-action shadow bg-red-500 hover:bg-red-800 text-white px-3 py-1 rounded-lg mr-2 btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    ';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->input('search.value'))) {
                        $searchValue = $request->input('search.value');
                        $query->where(function ($q) use ($searchValue) {
                            $q->where('periodo_academico', 'like', "%{$searchValue}%")
                                ->orWhere('codigo_tg', 'like', "%{$searchValue}%")
                                ->orWhere('nivel', 'like', "%{$searchValue}%")
                                ->orWhere('estudiante', 'like', "%{$searchValue}%")
                                ->orWhere('correo', 'like', "%{$searchValue}%")
                                ->orWhere('documento', 'like', "%{$searchValue}%")
                                ->orWhere('celular', 'like', "%{$searchValue}%")
                                ->orWhere('modalidad', 'like', "%{$searchValue}%")
                                ->orWhere('titulo', 'like', "%{$searchValue}%")
                                ->orWhere('director', 'like', "%{$searchValue}%")
                                ->orWhere('evaluador', 'like', "%{$searchValue}%")
                                ->orWhere('autores', 'like', "%{$searchValue}%")
                                ->orWhere('inicio_tg', 'like', "%{$searchValue}%")
                                ->orWhere('aprobacion_propuesta', 'like', "%{$searchValue}%")
                                ->orWhere('final_tg', 'like', "%{$searchValue}%");
                        });
                    }
                })
                ->rawColumns(['acciones'])
                ->make(true);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'periodo_academico' => 'required',
                'codigo_tg' => 'required',
                'nivel' => 'required',
                'estudiante' => 'required',
                'correo' => 'required|email',
                'documento' => 'required',
                'celular' => 'nullable|numeric|digits_between:10,15',
                'modalidad' => 'required',
                'titulo' => 'required',
                'director' => 'required',
                'evaluador' => 'required',
                'autores' => 'required',
                'inicio_tg' => 'required|date',
                'aprobacion_propuesta' => 'required|date',
                'final_tg' => 'required|date'
            ], [
                'periodo_academico.required' => 'El periodo académico es obligatorio.',
                'codigo_tg.required' => 'El código de TG es obligatorio.',
                'nivel.required' => 'El nivel es obligatorio.',
                'estudiante.required' => 'El nombre del estudiante es obligatorio.',
                'correo.required' => 'El correo electrónico es obligatorio.',
                'documento.required' => 'El documento es obligatorio.',
                'celular.numeric' => 'El celular debe ser un número.',
                'celular.digits_between' => 'El celular debe tener entre 10 y 15 dígitos.',
                'modalidad.required' => 'La modalidad es obligatoria.',
                'titulo.required' => 'El título es obligatorio.',
                'director.required' => 'El director es obligatorio.',
                'evaluador.required' => 'El evaluador es obligatorio.',
                'autores.required' => 'Los autores son obligatorios.',
                'inicio_tg.required' => 'La fecha de inicio de TG es obligatoria.',
                'inicio_tg.date' => 'La fecha de inicio de TG debe ser una fecha válida.',
                'aprobacion_propuesta.required' => 'La fecha de aprobación de propuesta es obligatoria.',
                'aprobacion_propuesta.date' => 'La fecha de aprobación de propuesta debe ser una fecha válida.',
                'final_tg.required' => 'La fecha de finalización de TG es obligatoria.',
                'final_tg.date' => 'La fecha de finalización de TG debe ser una fecha válida.'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            Historico::create($request->all());
            return response()->json(['message' => 'Proyecto creado correctamente.']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function storeMasivo(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file_proyectos' => 'required|mimes:xlsx,xls'
            ], [
                'file_proyectos.required' => 'El archivo de proyectos es obligatorio.',
                'file_proyectos.mimes' => 'El archivo debe ser un archivo de tipo xlsx o xls.'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $file = $request->file('file_proyectos');

            $import = new ProyectosImport();
            $errors = $import->import($file);

            if ($errors) {
                return response()->json(['message' => $errors], 422);
            }

            return response()->json(['message' => 'Proyectos creados correctamente.']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function edit($id)
    {
        try {
            $proyecto = Historico::find($id);
            if (!$proyecto) {
                return response()->json(['message' => 'Proyecto no encontrado.'], 404);
            }
            return response()->json($proyecto);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $proyecto = Historico::find($id);

            if (!$proyecto) {
                return response()->json(['message' => 'Proyecto no encontrado.'], 404);
            }

            $validator = Validator::make($request->all(), [
                'periodo_academico' => 'required',
                'codigo_tg' => 'required',
                'nivel' => 'required',
                'estudiante' => 'required',
                'correo' => 'required|email',
                'documento' => 'required',
                'celular' => 'nullable|numeric|digits_between:10,15',
                'modalidad' => 'required',
                'titulo' => 'required',
                'director' => 'required',
                'evaluador' => 'required',
                'autores' => 'required',
                'inicio_tg' => 'required|date',
                'aprobacion_propuesta' => 'required|date',
                'final_tg' => 'required|date'
            ], [
                'periodo_academico.required' => 'El periodo académico es obligatorio.',
                'codigo_tg.required' => 'El código de TG es obligatorio.',
                'nivel.required' => 'El nivel es obligatorio.',
                'estudiante.required' => 'El nombre del estudiante es obligatorio.',
                'correo.required' => 'El correo electrónico es obligatorio.',
                'documento.required' => 'El documento es obligatorio.',
                'celular.numeric' => 'El celular debe ser un número.',
                'celular.digits_between' => 'El celular debe tener entre 10 y 15 dígitos.',
                'modalidad.required' => 'La modalidad es obligatoria.',
                'titulo.required' => 'El título es obligatorio.',
                'director.required' => 'El director es obligatorio.',
                'evaluador.required' => 'El evaluador es obligatorio.',
                'autores.required' => 'Los autores son obligatorios.',
                'inicio_tg.required' => 'La fecha de inicio de TG es obligatoria.',
                'inicio_tg.date' => 'La fecha de inicio de TG debe ser una fecha válida.',
                'aprobacion_propuesta.required' => 'La fecha de aprobación de propuesta es obligatoria.',
                'aprobacion_propuesta.date' => 'La fecha de aprobación de propuesta debe ser una fecha válida.',
                'final_tg.required' => 'La fecha de finalización de TG es obligatoria.',
                'final_tg.date' => 'La fecha de finalización de TG debe ser una fecha válida.'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $proyecto->update($request->all());
            return response()->json(['message' => 'Proyecto actualizado correctamente.']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $proyecto = Historico::find($id);
            if (!$proyecto) {
                return response()->json(['message' => 'Proyecto no encontrado.'], 404);
            }

            $proyecto->delete();
            return response()->json(['message' => 'Proyecto eliminado correctamente.']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function generarReporte(Request $request)
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
            $formato_reporte = public_path('formatos/informe_historico.xlsx');

            $spreadsheet = IOFactory::load($formato_reporte);
            $sheet = $spreadsheet->getActiveSheet();
            $fila = 3;

            $historicos = Historico::query()
                ->where('periodo_academico', $periodo)
                ->orderBy('inicio_tg', 'desc')
                ->get();

            foreach ($historicos as $historico) {
                $periodo_academico = $historico->periodo_academico;
                $codigo_tg = $historico->codigo_tg;
                $nivel = $historico->nivel;
                $estudiante = ucwords(strtolower($historico->estudiante));
                $correo = strtolower($historico->correo);
                $documento = $historico->documento;
                $celular = $historico->celular ? $historico->celular : 'No especificado';
                $modalidad = $historico->modalidad;
                $titulo = ucfirst(strtolower($historico->titulo));
                $director = ucwords(strtolower($historico->director));
                $evaluador = ucwords(strtolower($historico->evaluador));
                $autores = ucwords(strtolower($historico->autores));
                $inicio_tg = $historico->inicio_tg;
                $aprobacion_propuesta = $historico->aprobacion_propuesta;
                $final_tg = $historico->final_tg;

                $sheet->setCellValue('A' . $fila, $periodo_academico);
                $sheet->setCellValue('B' . $fila, $codigo_tg);
                $sheet->setCellValue('C' . $fila, $nivel);
                $sheet->setCellValue('D' . $fila, $estudiante);
                $sheet->setCellValue('E' . $fila, $correo);
                $sheet->setCellValue('F' . $fila, $documento);
                $sheet->setCellValue('G' . $fila, $celular);
                $sheet->setCellValue('H' . $fila, $modalidad);
                $sheet->setCellValue('I' . $fila, $titulo);
                $sheet->setCellValue('J' . $fila, $director);
                $sheet->setCellValue('K' . $fila, $evaluador);
                $sheet->setCellValue('L' . $fila, $autores);
                $sheet->setCellValue('M' . $fila, $inicio_tg);
                $sheet->setCellValue('N' . $fila, $aprobacion_propuesta);
                $sheet->setCellValue('O' . $fila, $final_tg);

                foreach (range('A', 'O') as $col) {
                    $sheet->getStyle("{$col}{$fila}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle("{$col}{$fila}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                }

                $fila++;
            }

            $fileName = "Informe - Histórico de proyectos ({$periodo}).xlsx";

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
