<?php

namespace Database\Seeders;

use App\Models\Campo;
use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use App\Models\ValorCampo;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class IdeasBancoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cargar el archivo Excel
        $filePath = public_path('formatos/ideas_banco.csv');

        // Leer el archivo
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);

        // Campos para crear idea en el banco
        $solicitud_banco = TipoSolicitud::where('nombre', 'solicitud_banco')->first();

        $campo_titulo = Campo::query()->where('name', 'titulo')->where('tipo_solicitud_id', $solicitud_banco->id)->first();
        $campo_modalidad = Campo::query()->where('name', 'modalidad')->where('tipo_solicitud_id', $solicitud_banco->id)->first();
        $campo_objetivo = Campo::query()->where('name', 'objetivo')->where('tipo_solicitud_id', $solicitud_banco->id)->first();
        $campo_linea_investigacion = Campo::query()->where('name', 'linea_investigacion')->where('tipo_solicitud_id', $solicitud_banco->id)->first();
        $campo_nivel = Campo::query()->where('name', 'nivel')->where('tipo_solicitud_id', $solicitud_banco->id)->first();
        $campo_periodo = Campo::query()->where('name', 'periodo')->where('tipo_solicitud_id', $solicitud_banco->id)->first();
        $campo_disponible = Campo::query()->where('name', 'disponible')->where('tipo_solicitud_id', $solicitud_banco->id)->first();

        $campos = [
            $campo_titulo,
            $campo_modalidad,
            $campo_objetivo,
            $campo_linea_investigacion,
            $campo_nivel,
            $campo_periodo,
            $campo_disponible
        ];

        // Obtener todas las filas como un array asociativo
        $records = $csv->getRecords();

        foreach ($records as $row) {
            // 1. Extraer la info del csv
            $titulo = $row['titulo'] ?? null;
            $modalidad = $row['modalidad'] ?? null;
            $objetivo = $row['objetivo'] ?? null;
            $linea_investigacion = $row['linea_investigacion'] ?? null;
            $nivel = $row['nivel'] ?? null;
            $periodo = $row['periodo'] ?? null;
            $disponible = "true";
            $docente = $row['docente'] ?? null;

            $valores = [
                $titulo,
                $modalidad,
                $objetivo,
                $linea_investigacion,
                $nivel,
                $periodo,
                $disponible
            ];

            // 2. Crear la solicitud
            $solicitud = Solicitud::create([
                'user_id' => $docente,
                'estado' => 'Aprobada',
                'tipo_solicitud_id' => $solicitud_banco->id,
                'descripcion' => 'Propuesta de docente para publicar en el banco de ideas institucional'
            ]);

            // Por cada campo se debe crear un ValorCampo
            foreach ($campos as $index => $campo) {
                $valor = $valores[$index] ?? null;

                ValorCampo::create([
                    'solicitud_id' => $solicitud->id,
                    'campo_id' => $campo->id,
                    'valor' => $valor
                ]);
            }
        }
    }
}
