<?php

namespace App\Console\Commands;

use App\Mail\PracticasMail;
use App\Models\Practica;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarRecordatorioPractica extends Command
{
    protected $signature = 'app:recordatorio-practica';

    protected $description = 'Envía recordatorios de finalización de prácticas empresariales';

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
            ->whereNotIn('estado', ['Finalizada', 'Rechazada'])
            ->get();

        foreach ($practicas as $practica) {

            $campos = $practica->camposConValores();

            $fechaLimite = $this->findCampoByName(
                $campos,
                'fecha_limite_practica'
            );

            if (!$fechaLimite) {
                continue;
            }

            $fechaActual = Carbon::now()->format('Y-m-d');

            $recordatorio30 = Carbon::parse($fechaLimite)
                ->subDays(30)
                ->format('Y-m-d');

            $recordatorio15 = Carbon::parse($fechaLimite)
                ->subDays(15)
                ->format('Y-m-d');

            $recordatorio8 = Carbon::parse($fechaLimite)
                ->subDays(8)
                ->format('Y-m-d');

            if (
                $fechaActual === $recordatorio30 ||
                $fechaActual === $recordatorio15 ||
                $fechaActual === $recordatorio8
            ) {

                $integrante2Id = $this->findCampoByName(
                    $campos,
                    'id_integrante_2'
                );

                $integrante2 = null;

                if (!empty($integrante2Id)) {
                    $integrante2 = User::with('tipo_documento')
                        ->find($integrante2Id);
                }

                $data = [

                    'tipo_correo' => 'recordatorio_practica',

                    'cuerpo_correo' => [

                        'estado' => $practica->estado,

                        'estudiante' => $practica->user,

                        'correo' => $practica->user->email,

                        'celular' =>
                            $practica->user->nro_celular ?? '',

                        'fecha_limite_practica' =>
                            Carbon::parse($fechaLimite)
                                ->format('d/m/Y'),

                        'integrante_2' =>
                            $integrante2,

                        'integrante_2_correo' =>
                            $integrante2->email ?? null,

                        'integrante_2_documento' =>
                            $integrante2
                                ? (($integrante2->tipo_documento->tag ?? '') . ' ' . ($integrante2->nro_documento ?? ''))
                                : null,

                        'integrante_2_celular' =>
                            $integrante2->nro_celular ?? null,

                        'campos' => $campos,
                    ],
                ];

                $destinatarios = [
                    $practica->user->email,
                ];

                if (!empty($integrante2?->email)) {
                    $destinatarios[] = $integrante2->email;
                }

                $destinatarios = array_unique(
                    array_filter($destinatarios)
                );

                Mail::to($destinatarios)
                    ->queue(new PracticasMail($data));

                $this->info(
                    'Recordatorio enviado a: ' .
                    implode(', ', $destinatarios)
                );
            }
        }
    }
}