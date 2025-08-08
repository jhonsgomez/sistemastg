<?php

namespace App\Console\Commands;

use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use Carbon\Carbon;
use Illuminate\Console\Command;

class VencimientoInforme extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:vencimiento-informe';

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
            ->whereIn('estado', ['Pendiente', 'Fase 4', 'Fase 5',])
            ->get();

        foreach ($proyectos as $proyecto) {
            $campos = $proyecto->camposConValores();
            $fecha_maxima_informe = Carbon::parse(self::findCampoByName($campos, 'fecha_maxima_informe'))->format('Y-m-d');
            $fecha_actual = Carbon::now()->format('Y-m-d');

            if ($fecha_actual > $fecha_maxima_informe) {
                $proyecto_vencido = Solicitud::query()->where('id', $proyecto->id)->first();
                $proyecto_vencido->update([
                    'vencido' => true
                ]);
            }
        }
    }
}
