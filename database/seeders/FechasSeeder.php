<?php

namespace Database\Seeders;

use App\Models\Fecha;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FechasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function getPeriodoActual()
    {
        $anio_actual = date('Y');
        $mes_actual = date('n');

        $periodo_actual = ($mes_actual <= 6) ? "$anio_actual-1" : "$anio_actual-2";

        return $periodo_actual;
    }

    public function run(): void
    {
        $periodo_actual = self::getPeriodoActual();
        $fecha_actual = Carbon::now()->format('Y-m-d');
        $fecha_futura = Carbon::now()->addMonths(1)->format('Y-m-d');

        $fechas = [
            'fecha_inicio_banco' => $fecha_actual,
            'fecha_fin_banco' => $fecha_futura,
            'fecha_inicio_proyectos' => $fecha_actual,
            'fecha_fin_proyectos' => $fecha_futura,
            'fecha_aprobacion_propuesta' => $fecha_futura,
        ];

        Fecha::updateOrCreate(
            ['periodo' => $periodo_actual],
            ['fechas' => $fechas]
        );
    }
}
