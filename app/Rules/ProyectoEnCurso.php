<?php

namespace App\Rules;

use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;

class ProyectoEnCurso implements ValidationRule
{
    protected $estudiante_id;

    public function __construct($estudiante_id = null)
    {
        $this->estudiante_id = $estudiante_id;
    }

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

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $valor = $this->estudiante_id ?? $value;

        if (self::tieneProyectosEnCurso($valor)) {
            $fail("El integrante ya tiene un proyecto en curso.");
        }
    }

    public function tieneProyectosEnCurso($valor): bool
    {
        $type = null;
        $solicitudes = null;

        try {
            $type = self::getType('fase_0');
            $solicitudes = Solicitud::query()->where('tipo_solicitud_id', '=', $type->id)->get();

            foreach ($solicitudes as $solicitud) {
                if (!$solicitud->vencido && !$solicitud->deshabilitado) {
                    if (str_contains($solicitud->estado, 'Fase') || str_contains($solicitud->estado, 'Pendiente')) {
                        $campos = $solicitud->CamposConValores();
                        foreach ($campos as $item) {
                            if (str_contains($item['campo']['name'], 'id_integrante')) {
                                if ($valor == $item['valor']) {
                                    return true;
                                }
                            }
                        }
                    }
                }
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
