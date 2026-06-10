<?php

namespace App\Console\Commands;

use App\Mail\PracticasMail;
use App\Models\Practica;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarRecordatorioRevisionDirectorPractica extends Command
{
    protected $signature = 'app:enviar-recordatorio-revision-director-practica';

    protected $description = 'Envía recordatorios al director para revisar documentos finales';

    public function findCampoByName($campos, $name)
    {
        foreach ($campos as $item) {
            if (isset($item['campo']) && $item['campo'] === $name) {
                return $item['valor'] ?: null;
            }
        }

        return null;
    }

    public function handle()
    {
        $practicas = Practica::with([
            'user.tipo_documento',
            'valoresCampos.campo'
        ])
            ->where('vencido', false)
            ->where('deshabilitado', false)
            ->where('estado', 'Fase 5')
            ->get();

        foreach ($practicas as $practica) {
            $campos = $practica->camposConValores();

            $submitedFase5 = $this->findCampoByName($campos, 'submited_fase5');
            $estadoDirector = $this->findCampoByName($campos, 'estado_director_fase5');

            if ($submitedFase5 !== 'true') {
                continue;
            }

            if (!empty($estadoDirector)) {
                continue;
            }

            $fechaRecordatorio = Carbon::parse($practica->updated_at)
                ->addDays(8)
                ->format('Y-m-d');

            $fechaActual = Carbon::now()->format('Y-m-d');

            if ($fechaActual !== $fechaRecordatorio) {
                continue;
            }

            $directorId = $this->findCampoByName($campos, 'director_id');

            if (!$directorId) {
                continue;
            }

            $director = User::find($directorId);

            if (!$director || empty($director->email)) {
                continue;
            }

            $integrante2Id = $this->findCampoByName($campos, 'id_integrante_2');

            $integrante2 = null;

            if (!empty($integrante2Id)) {
                $integrante2 = User::with('tipo_documento')->find($integrante2Id);
            }

            $data = [
                'tipo_correo' => 'recordatorio_revision_director_fase5',

                'cuerpo_correo' => [
                    'estado' => $practica->estado,
                    'director' => $director,
                    'estudiante' => $practica->user,
                    'integrante_2' => $integrante2,
                    'campos' => $campos,
                ],
            ];

            Mail::to($director->email)
                ->queue(new PracticasMail($data));

            $this->info('Recordatorio enviado a director: ' . $director->email);
        }
    }

}