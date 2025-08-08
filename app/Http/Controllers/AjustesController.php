<?php

namespace App\Http\Controllers;

use App\Models\Fecha;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AjustesController extends Controller
{
    public function index()
    {
        try {
            return view('ajustes.index');
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function fechas(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'periodo_academico' => 'required',
                'fecha_inicio_banco' => 'required|date',
                'fecha_fin_banco' => 'required|date',
                'fecha_inicio_proyectos' => 'required|date',
                'fecha_fin_proyectos' => 'required|date',
                'fecha_aprobacion_propuesta' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $fechas = [
                'fecha_inicio_banco' => $request->fecha_inicio_banco,
                'fecha_fin_banco' => $request->fecha_fin_banco,
                'fecha_inicio_proyectos' => $request->fecha_inicio_proyectos,
                'fecha_fin_proyectos' => $request->fecha_fin_proyectos,
                'fecha_aprobacion_propuesta' => $request->fecha_aprobacion_propuesta,
            ];

            Fecha::updateOrCreate(
                ['periodo' => $request->periodo_academico],
                ['fechas' => $fechas]
            );
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }

    public function backups(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'backup_type' => 'required',
            ], [
                'backup_type.required' => 'El tipo de respaldo es obligatorio.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            if ($request->backup_type === 'db') {
                Artisan::call('backup:run', ['--only-db' => true, '--disable-notifications' => true]);

                $backupDisk = Storage::disk(config('backup.backup.destination.disks')[0]);
                $backups = $backupDisk->files(config('backup.backup.name'));

                if (empty($backups)) {
                    return response()->json(['message' => 'No se pudo generar la copia de seguridad.'], 500);
                }

                $latestBackup = collect($backups)->sortByDesc(function ($file) use ($backupDisk) {
                    return $backupDisk->lastModified($file);
                })->first();

                $fileName = basename($latestBackup);
                $fileContent = $backupDisk->get($latestBackup);

                return response($fileContent)
                    ->header('Content-Type', 'application/zip')
                    ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                    ->header('Content-Length', strlen($fileContent));
            } else {
                return response()->json(['message' => 'Tipo de copia de seguridad no válido.'], 422);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }
}
