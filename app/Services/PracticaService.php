<?php

namespace App\Services;

use App\Models\Campo;
use App\Models\Practica;
use App\Models\PracticaValorCampo;
use App\Models\TipoSolicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PracticaService
{
    public function crearPractica(Request $request): Practica
    {
        return DB::transaction(function () use ($request) {

            $tipo = TipoSolicitud::where(
                'nombre',
                'practicas_fase_0'
            )->firstOrFail();

            $campos = Campo::where(
                'tipo_solicitud_id',
                $tipo->id
            )->get();

            $tieneEmpresa =
                $request->input('tiene_empresa') === '1';

            $practica = Practica::create([
                'user_id' => Auth::id(),

                'tipo_solicitud_id' => $tipo->id,

                'estado' => 'Pendiente',

                'vencido' => false,

                'deshabilitado' => false,
            ]);

            foreach ($campos as $campo) {

                if (
                    in_array(
                        $campo->name,
                        [
                            'nombre_completo',
                            'correo',
                            'nivel',
                            'documento',
                            'celular'
                        ]
                    )
                ) {
                    continue;
                }

                $valor = null;

                switch ($campo->type) {

                    case 'checkbox':

                        $valor =
                            $request->input($campo->name) === '1'
                            ? 'true'
                            : 'false';

                        break;

                    case 'file':

                        if (
                            $campo->name === 'hoja_vida' &&
                            $tieneEmpresa
                        ) {
                            continue 2;
                        }

                        if ($request->hasFile($campo->name)) {

                            $valor = $request
                                ->file($campo->name)
                                ->store('practicas', 'public');
                        }

                        break;

                    default:

                        $valor =
                            $request->input($campo->name);

                        break;
                }

                if (
                    $campo->name === 'id_integrante_2' &&
                    empty($valor)
                ) {
                    continue;
                }

                if ($valor !== null) {

                    PracticaValorCampo::create([
                        'practica_id' => $practica->id,

                        'campo_id' => $campo->id,

                        'valor' => $valor,
                    ]);
                }
            }

            $campoSubmited = Campo::where(
                'tipo_solicitud_id',
                $tipo->id
            )
                ->where('name', 'submited_fase0')
                ->first();

            if ($campoSubmited) {

                PracticaValorCampo::create([
                    'practica_id' => $practica->id,

                    'campo_id' => $campoSubmited->id,

                    'valor' => 'true',
                ]);
            }

            return $practica;
        });
    }
}