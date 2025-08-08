<?php

namespace App\Console\Commands;

use App\Mail\RecordatorioPropuesta;
use App\Models\Fecha;
use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarRecordatorioPropuesta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:recordatorio-propuesta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía recordatorios a estudiantes cuyo periodo de aprobación está próximo a vencer';

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
            ->whereIn('estado', ['Fase 1', 'Fase 2', 'Fase 3'])
            ->get();

        foreach ($proyectos as $proyecto) {
            $campos = $proyecto->camposConValores();
            $director = self::findCampoByName($campos, 'director_id');
            $evaluador = self::findCampoByName($campos, 'evaluador_id');
            
            $periodo = self::findCampoByName($campos, 'periodo');
            $fechas = self::getFechasByPeriodo($periodo);
            
            $fecha_aprobacion = Carbon::parse($fechas['fecha_aprobacion_propuesta'])->subDays(15)->format('Y-m-d');
            $fecha_aprobacion_2 = Carbon::parse($fechas['fecha_aprobacion_propuesta'])->subDays(8)->format('Y-m-d');
            
            $fecha_actual = Carbon::now()->format('Y-m-d');

            if ($fecha_actual === $fecha_aprobacion || $fecha_actual === $fecha_aprobacion_2) {
                Mail::queue(new RecordatorioPropuesta('RECORDATORIO DE APROBACIÓN DE PROPUESTA', $proyecto->id, true));
                if (isset($director) && isset($evaluador)) {
                    Mail::queue(new RecordatorioPropuesta('RECORDATORIO DE APROBACIÓN DE PROPUESTA', $proyecto->id, false));
                }
            }
        }
    }
}
