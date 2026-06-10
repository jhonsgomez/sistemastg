<?php

namespace App\Console\Commands;

use App\Models\Practica;
use Carbon\Carbon;
use Illuminate\Console\Command;

class VencimientoPractica extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:vencimiento-practica';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el estado de las prácticas vencidas';

    public function findCampoByName($campos, $name)
    {
        foreach ($campos as $item) {
            if (isset($item['campo']) && $item['campo'] === $name) {
                return $item['valor'] ?: null;
            }
        }

        return null;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $practicas = Practica::with('valoresCampos.campo')
            ->where('vencido', false)
            ->where('deshabilitado', false)
            ->get();

        foreach ($practicas as $practica) {
            $campos = $practica->camposConValores();

            $fechaLimite = $this->findCampoByName($campos, 'fecha_limite_practica');

            if (!$fechaLimite) {
                continue;
            }

            $fechaActual = Carbon::now()->format('Y-m-d');

            $fechaLimite = Carbon::parse($fechaLimite)->format('Y-m-d');

            if ($fechaActual > $fechaLimite) {
                $practica->update([
                    'vencido' => true
                ]);

                $this->info('Práctica vencida: ' . $practica->id);
            }
        }
    }
}