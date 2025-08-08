<?php

namespace App\Console\Commands;

use App\Mail\RevisionProyectoMail;
use App\Models\Fecha;
use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class RecordatorioRevision extends Command
{
    protected $signature = 'app:recordatorio-revision';

    protected $description = 'Envía recordatorios a docentes para la revisión de proyectos';

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

    public function handle()
    {
        $type = self::getType('fase_0');

        $proyectos = Solicitud::query()
            ->where('vencido', false)
            ->where('deshabilitado', false)
            ->where('tipo_solicitud_id', $type->id)
            ->whereIn('estado', ['Fase 2', 'Fase 3', 'Fase 4', 'Fase 5'])
            ->get();

        foreach ($proyectos as $proyecto) {
            $campos = $proyecto->camposConValores();

            $recordatorio_fase2 = $this->findCampoByName($campos, 'recordatorio_fase2') ?? null;
            $recordatorio_fase3 = $this->findCampoByName($campos, 'recordatorio_fase3') ?? null;
            $recordatorio_fase4 = $this->findCampoByName($campos, 'recordatorio_fase4') ?? null;
            $recordatorio_fase5 = $this->findCampoByName($campos, 'recordatorio_fase5') ?? null;

            $estado = $proyecto->estado;
            $estado_array = explode(' ', $estado);
            $fase = $estado_array[1];
            $fase = is_numeric($fase) ? (int) $fase : null;

            $fecha_actual = now()->format('Y-m-d');

            switch ($fase) {
                case 2:
                    if (isset($recordatorio_fase2)) {
                        $fecha_recordatorio = Carbon::parse($recordatorio_fase2)->format('Y-m-d');
                        if ($fecha_actual === $fecha_recordatorio) {
                            $this->sendRecordatorioFase2y3($proyecto, $campos);
                        }
                    }
                    break;
                case 3:
                    if (isset($recordatorio_fase3)) {
                        $fecha_recordatorio = Carbon::parse($recordatorio_fase3)->format('Y-m-d');
                        if ($fecha_actual === $fecha_recordatorio) {
                            $this->sendRecordatorioFase2y3($proyecto, $campos);
                        }
                    }
                    break;
                case 4:
                    if (isset($recordatorio_fase4)) {
                        $fecha_recordatorio = Carbon::parse($recordatorio_fase4)->format('Y-m-d');
                        if ($fecha_actual === $fecha_recordatorio) {
                            $this->sendRecordatorioFase4y5($proyecto, $campos);
                        }
                    }
                    break;
                case 5:
                    if (isset($recordatorio_fase5)) {
                        $fecha_recordatorio = Carbon::parse($recordatorio_fase5)->format('Y-m-d');
                        if ($fecha_actual === $fecha_recordatorio) {
                            $this->sendRecordatorioFase4y5($proyecto, $campos);
                        }
                    }
                    break;
            }
        }
    }

    public function sendRecordatorioFase2y3($proyecto, $campos)
    {
        $submited_fase2 = $this->findCampoByName($campos, 'submited_fase2') ?? null;
        $submited_fase3_director = $this->findCampoByName($campos, 'submited_fase3_director') ?? null;
        $submited_fase3_evaluador = $this->findCampoByName($campos, 'submited_fase3_evaluador') ?? null;

        if (isset($submited_fase2) && $submited_fase2 === 'true') {
            if (is_null($submited_fase3_director) || $submited_fase3_director === 'false') {
                Mail::queue(new RevisionProyectoMail($proyecto->id, 'director'));
            } else {
                if (is_null($submited_fase3_evaluador) || $submited_fase3_evaluador === 'false') {
                    Mail::queue(new RevisionProyectoMail($proyecto->id, 'evaluador'));
                }
            }
        }
    }

    public function sendRecordatorioFase4y5($proyecto, $campos)
    {
        $submited_fase4 = $this->findCampoByName($campos, 'submited_fase4') ?? null;
        $submited_fase5_director = $this->findCampoByName($campos, 'submited_fase5_director') ?? null;
        $submited_fase5_evaluador = $this->findCampoByName($campos, 'submited_fase5_evaluador') ?? null;

        if (isset($submited_fase4) && $submited_fase4 === 'true') {
            if (is_null($submited_fase5_director) || $submited_fase5_director === 'false') {
                Mail::queue(new RevisionProyectoMail($proyecto->id, 'director'));
            } else {
                if (is_null($submited_fase5_evaluador) || $submited_fase5_evaluador === 'false') {
                    Mail::queue(new RevisionProyectoMail($proyecto->id, 'evaluador'));
                }
            }
        }
    }
}
