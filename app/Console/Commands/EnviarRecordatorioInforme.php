<?php

namespace App\Console\Commands;

use App\Mail\RecordatorioInforme;
use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarRecordatorioInforme extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:recordatorio-informe';

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
            ->whereIn('estado', ['Fase 4', 'Fase 5'])
            ->get();

        foreach ($proyectos as $proyecto) {
            $campos = $proyecto->camposConValores();
            $director = self::findCampoByName($campos, 'director_id');
            $evaluador = self::findCampoByName($campos, 'evaluador_id');

            $fecha_maxima_informe = Carbon::parse(self::findCampoByName($campos, 'fecha_maxima_informe'))->subDays(15)->format('Y-m-d');
            $fecha_maxima_informe_2 = Carbon::parse(self::findCampoByName($campos, 'fecha_maxima_informe'))->subDays(8)->format('Y-m-d');
            
            $fecha_actual = Carbon::now()->format('Y-m-d');

            if ($fecha_actual === $fecha_maxima_informe || $fecha_actual === $fecha_maxima_informe_2) {
                Mail::queue(new RecordatorioInforme('RECORDATORIO DE ENTREGA DE INFORME FINAL', $proyecto->id, true));
                if (isset($director) && isset($evaluador)) {
                    Mail::queue(new RecordatorioInforme('RECORDATORIO DE ENTREGA DE INFORME FINAL', $proyecto->id, false));
                }
            }
        }
    }
}
