<?php
namespace Database\Seeders;

use App\Models\Campo;
use App\Models\TipoSolicitud;
use Illuminate\Database\Seeder;

class CamposPracticasSeeder extends Seeder
{
    public function run(): void
    {
        $practicas_fase_0 = TipoSolicitud::where('nombre', 'practicas_fase_0')->first();

        if (!$practicas_fase_0) {
            return;
        }

        // Primero, restaurar todos los campos que puedan estar eliminados lógicamente
        Campo::withTrashed()
            ->where('tipo_solicitud_id', $practicas_fase_0->id)
            ->restore();

        // Usar updateOrCreate para evitar duplicados (no usar delete())
        
        // 1. Nombre completo
        Campo::updateOrCreate(
            ['tipo_solicitud_id' => $practicas_fase_0->id, 'name' => 'nombre_completo'],
            ['label' => 'Nombre completo', 'type' => 'text', 'required' => true, 'instructions' => 'Nombre completo del estudiante.']
        );

        // 2. Correo institucional
        Campo::updateOrCreate(
            ['tipo_solicitud_id' => $practicas_fase_0->id, 'name' => 'correo'],
            ['label' => 'Correo institucional', 'type' => 'text', 'required' => true, 'instructions' => 'Correo institucional']
        );

        // 3. Nivel académico
        Campo::updateOrCreate(
            ['tipo_solicitud_id' => $practicas_fase_0->id, 'name' => 'nivel'],
            ['label' => 'Nivel académico', 'type' => 'text', 'required' => true, 'instructions' => 'Nivel académico']
        );

        // 4. Número de documento
        Campo::updateOrCreate(
            ['tipo_solicitud_id' => $practicas_fase_0->id, 'name' => 'documento'],
            ['label' => 'Número de documento', 'type' => 'number', 'required' => true, 'instructions' => 'Número de documento']
        );

        // 5. Número de celular
        Campo::updateOrCreate(
            ['tipo_solicitud_id' => $practicas_fase_0->id, 'name' => 'celular'],
            ['label' => 'Número de celular', 'type' => 'number', 'required' => true, 'instructions' => 'Número de celular']
        );

        // 6. ¿Cuenta con empresa?
        Campo::updateOrCreate(
            ['tipo_solicitud_id' => $practicas_fase_0->id, 'name' => 'tiene_empresa'],
            ['label' => '¿Cuenta con empresa?', 'type' => 'checkbox', 'required' => true, 'instructions' => 'Marque si ya cuenta con empresa.']
        );

        // 7. Hoja de vida
        Campo::updateOrCreate(
            ['tipo_solicitud_id' => $practicas_fase_0->id, 'name' => 'hoja_vida'],
            ['label' => 'Hoja de vida (PDF)', 'type' => 'file', 'required' => false, 'instructions' => 'Suba la hoja de vida si NO cuenta con empresa.']
        );

        // 8. Título de la práctica
        Campo::updateOrCreate(
            ['tipo_solicitud_id' => $practicas_fase_0->id, 'name' => 'titulo'],
            ['label' => 'Título de la práctica', 'type' => 'text', 'required' => true, 'instructions' => 'Ingrese un título descriptivo para su práctica empresarial.']
        );

        // 9. Segundo integrante
        Campo::updateOrCreate(
            ['tipo_solicitud_id' => $practicas_fase_0->id, 'name' => 'id_integrante_2'],
            ['label' => 'Segundo integrante', 'type' => 'select', 'required' => false, 'instructions' => 'Seleccione el compañero con quien realizará la práctica empresarial. El comité verificará la información y se pondrá en contacto si es pertinente.']
        );

        // 10. Periodo académico (oculto)
        Campo::updateOrCreate(
            ['tipo_solicitud_id' => $practicas_fase_0->id, 'name' => 'periodo'],
            ['label' => 'Periodo académico', 'type' => 'hidden', 'required' => true, 'instructions' => null]
        );

        // 11. Respuesta del comité (importante para responder solicitudes)
        Campo::updateOrCreate(
            ['tipo_solicitud_id' => $practicas_fase_0->id, 'name' => 'respuesta_comite'],
            ['label' => 'Respuesta del comité', 'type' => 'textarea', 'required' => true, 'instructions' => 'Respuesta del comité a la solicitud de práctica']
        );
    }
}