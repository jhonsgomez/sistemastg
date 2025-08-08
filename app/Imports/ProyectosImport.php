<?php

namespace App\Imports;

use App\Models\Historico;
use Carbon\Carbon;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\DB;

class ProyectosImport
{
    public function import($file)
    {
        DB::beginTransaction();

        try {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();

            $errors = [];

            $rows = $sheet->getRowIterator(3);

            foreach ($rows as $rowIndex => $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowData = [];

                foreach ($cellIterator as $cell) {
                    $value = $cell->getFormattedValue();
                    $rowData[] = $value === '' ? null : $value;
                }

                // $validationResult = $this->validateRow($rowData);

                // if ($validationResult !== true) {
                //     $errors[] = "Error en fila $rowIndex: $validationResult";
                //     DB::rollBack();
                //     return $errors;
                // }

                $dateFields = [12, 13, 14];

                foreach ($dateFields as $index) {
                    if (!is_null($rowData[$index])) {
                        $rowData[$index] = $this->convertDate($rowData[$index]);
                    }
                }

                Historico::create([
                    'periodo_academico' => $rowData[0],
                    'codigo_tg' => $rowData[1],
                    'nivel' => $rowData[2],
                    'estudiante' => $rowData[3],
                    'correo' => $rowData[4],
                    'documento' => $rowData[5],
                    'celular' => $rowData[6],
                    'modalidad' => $rowData[7],
                    'titulo' => $rowData[8],
                    'director' => $rowData[9],
                    'evaluador' => $rowData[10],
                    'autores' => $rowData[11],
                    'inicio_tg' => $rowData[12],
                    'aprobacion_propuesta' => $rowData[13],
                    'final_tg' => $rowData[14],
                ]);
            }

            DB::commit();
            return null;
        } catch (Exception $e) {
            DB::rollBack();
            return ["Error al procesar el archivo. Por favor, inténtelo de nuevo."];
        }
    }

    public function validateRow($rowData)
    {
        if (
            is_null($rowData[0]) || is_null($rowData[1]) || is_null($rowData[2]) || is_null($rowData[3])
            || is_null($rowData[4]) || is_null($rowData[5]) || is_null($rowData[7]) || is_null($rowData[8])
            || is_null($rowData[9]) || is_null($rowData[10]) || is_null($rowData[11])
        ) {
            return "Algunos campos obligatorios están vacíos.";
        }

        if (!filter_var($rowData[4], FILTER_VALIDATE_EMAIL)) {
            return "El correo ingresado no es válido.";
        }

        if (!is_numeric($rowData[6])) {
            return "El número de celular ingresado no es numérico.";
        }

        $dateFields = [$rowData[12], $rowData[13], $rowData[14]];
        
        foreach ($dateFields as $date) {
            if (!$this->isValidDate($date)) {
                return "Una de las fechas ingresadas no es válida.";
            }
        }

        return true;
    }

    private function convertDate($date)
    {
        if (is_numeric($date)) {
            return Carbon::createFromFormat('Y-m-d', Date::excelToDateTimeObject($date)->format('Y-m-d'));
        }

        $carbonDate = Carbon::createFromFormat('m/d/Y', $date);
        return $carbonDate->format('Y-m-d');
    }

    private function isValidDate($date)
    {
        return (bool) strtotime($date);
    }
}
