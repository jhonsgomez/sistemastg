<?php

namespace App\Console\Commands;

use App\Models\Fecha;
use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use Carbon\Carbon;
use Illuminate\Console\Command;

class VencimientoPropuesta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:vencimiento-propuesta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el estado de los proyectos vencidos';

    public function getType($name)
    {
        $type = TipoSolicitud::query()->where('nombre', '=', $name)->where('deleted_at', '=', NULL)->first();
        return $type;
    }

    public function findCampoByName($campos, $name)
    {
        foreach ($campos as $item) {
            if (isset($item['campo']['name']) && $item['campo']['name'] === $name) {
                return $item['valor'] ? $item['valor'] : null;
            }
        }
    }

    public function getFechasByPeriodo($periodo)
    {
        $fechas = Fecha::where('periodo', '=', $periodo)->first();
        return $fechas ? $fechas->fechas : null;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = self::getType('fase_0');

        $proyectos = Solicitud::query()
            ->where('vencido', false)
            ->where('deshabilitado', false)
            ->where('tipo_solicitud_id', $type->id)
            ->whereIn('estado', ['Pendiente', 'Fase 1', 'Fase 2', 'Fase 3'])
            ->get();

        foreach ($proyectos as $proyecto) {
            $campos = $proyecto->camposConValores();
            $periodo = self::findCampoByName($campos, 'periodo');
            $fechas = self::getFechasByPeriodo($periodo);
            $fecha_aprobacion = Carbon::parse($fechas['fecha_aprobacion_propuesta'])->format('Y-m-d');
            $fecha_actual = Carbon::now()->format('Y-m-d');

            if ($fecha_actual > $fecha_aprobacion) {
                $proyecto_vencido = Solicitud::query()->where('id', $proyecto->id)->first();
                $proyecto_vencido->update([
                    'vencido' => true
                ]);
            }
        }
    }
}
