<?php

namespace App\Http\Controllers;
use App\Models\TipoSolicitud;
use Illuminate\Http\Request;
use App\Models\Campo;
use App\Models\Practica;
class PracticaController extends Controller
{
    public function index()
    {
    

        $tipo = TipoSolicitud::where('nombre', 'practicas_fase_0')->first();
        
        $campos = Campo::where('tipo_solicitud_id', $tipo->id)->get();

        return view('practicas.index', compact('campos'));
    }



    public function store(Request $request)
    {
        $tipo = TipoSolicitud::where('nombre', 'practicas_fase_0')->first();
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

            if (!$request->hasFile('hoja_vida')) {
                $errors['hoja_vida'][] = 'Debe subir la hoja de vida si NO cuenta con empresa.';
            }
        }
        
        if (!empty($errors)) {
            return response()->json([
                'errors' => $errors
            ], 422);
        }
      

        //GUARDAR DATA
        $data = [];

        foreach ($campos as $campo) {

            if ($campo->type == 'checkbox') {
                $data[$campo->name] = $request->input($campo->name) === '1';
            }

            elseif ($campo->type == 'file') {

                // 🔥 SI TIENE EMPRESA, NO GUARDAR HOJA DE VIDA
                if ($campo->name == 'hoja_vida' && $tieneEmpresa) {
                    continue;
                }

                if ($request->hasFile($campo->name)) {
                    $file = $request->file($campo->name);
                    $path = $file->store('practicas', 'public');
                    $data[$campo->name] = $path;
                }
            }

            else {
                $data[$campo->name] = $request->input($campo->name);
            }
        }
       

        Practica::create([
            'user_id' => auth()->id(),
            'tipo_solicitud_id' => $tipo->id,
            'data' => json_encode($data)
        ]);

        return response()->json([
            'message' => 'Práctica enviada correctamente'
        ]);
    }
}