<?php

namespace App\Console\Commands;

use App\Mail\PracticasMail;
use App\Models\Practica;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarRecordatorioProrrogaPractica extends Command
{
    protected $signature = 'app:enviar-recordatorio-prorroga-practica';

    protected $description = 'Envía recordatorios para solicitar prórroga de prácticas';

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
            ->whereNotIn('estado', ['Finalizado'])
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

            // Si ya solicitó prórroga, no enviar recordatorio
            $cartaProrroga = $this->findCampoByName(
                $campos,
                'carta_prorroga'
            );

            if (!empty($cartaProrroga)) {
                continue;
            }

            $fechaActual = Carbon::now()->format('Y-m-d');

            // 15 días antes de vencer la práctica
            $recordatorio = Carbon::parse($fechaLimite)
                ->subDays(15)
                ->format('Y-m-d');

            if ($fechaActual === $recordatorio) {

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
                    'tipo_correo' => 'recordatorio_prorroga',

                    'cuerpo_correo' => [
                        'estado' => $practica->estado,
                        'estudiante' => $practica->user,
                        'fecha_limite_practica' => Carbon::parse($fechaLimite)
                            ->format('d/m/Y'),
                        'integrante_2' => $integrante2,
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
                    'Recordatorio de prórroga enviado a: ' .
                    implode(', ', $destinatarios)
                );
            }
        }
    }
}