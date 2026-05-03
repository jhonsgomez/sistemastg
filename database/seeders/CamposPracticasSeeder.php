<?php
namespace Database\Seeders;

use App\Models\Campo;
use App\Models\TipoSolicitud;
use Illuminate\Database\Seeder;

class CamposPracticasSeeder extends Seeder
{
    public function run(): void
    {
        // ==================== FASE 0 ====================
        $practicas_fase_0 = TipoSolicitud::where('nombre', 'practicas_fase_0')->first();
        if ($practicas_fase_0) {
            // Restaurar campos eliminados lógicamente
            Campo::withTrashed()
                ->where('tipo_solicitud_id', $practicas_fase_0->id)
                ->restore();

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

            // 7. Hoja de vida (PDF)
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
                ['label' => 'Segundo integrante', 'type' => 'select', 'required' => false, 'instructions' => 'Seleccione el compañero con quien realizará la práctica empresarial.']
            );

            // 10. Periodo académico (oculto)
            Campo::updateOrCreate(
                ['tipo_solicitud_id' => $practicas_fase_0->id, 'name' => 'periodo'],
                ['label' => 'Periodo académico', 'type' => 'hidden', 'required' => true, 'instructions' => null]
            );

            // 11. Respuesta del comité (Fase 0)
            Campo::updateOrCreate(
                ['tipo_solicitud_id' => $practicas_fase_0->id, 'name' => 'respuesta_comite'],
                ['label' => 'Respuesta del comité', 'type' => 'textarea', 'required' => true, 'instructions' => 'Respuesta del comité a la solicitud de práctica']
            );
        }

        // ==================== FASE 1 ====================
        $practicas_fase_1 = TipoSolicitud::where('nombre', 'practicas_fase_1')->first();
        if ($practicas_fase_1) {
            Campo::withTrashed()
                ->where('tipo_solicitud_id', $practicas_fase_1->id)
                ->restore();

            // 1. Documento F-DC-126
            Campo::updateOrCreate(
                ['tipo_solicitud_id' => $practicas_fase_1->id, 'name' => 'doc_fdc126'],
                ['label' => 'Formato F-DC-126', 'type' => 'file', 'required' => true, 'instructions' => 'Suba el formato F-DC-126 diligenciado (Word, máx 5MB).']
            );

            // 2. ¿Es práctica institucional? (checkbox)
            Campo::updateOrCreate(
                ['tipo_solicitud_id' => $practicas_fase_1->id, 'name' => 'es_institucional'],
                ['label' => '¿Es práctica institucional?', 'type' => 'checkbox', 'required' => false, 'instructions' => 'Marque si la práctica se realizará en la UTS con previa autorización del coordinador.']
            );

            // 3. Nombre de la empresa (aparece si no es institucional)
            Campo::updateOrCreate(
                ['tipo_solicitud_id' => $practicas_fase_1->id, 'name' => 'nombre_empresa'],
                ['label' => 'Nombre de la empresa', 'type' => 'text', 'required' => false, 'instructions' => 'Ingrese el nombre de la empresa donde realizará la práctica.']
            );

            // 4. Bandera para validar el envío de Fase 1
            Campo::updateOrCreate(
                ['tipo_solicitud_id' => $practicas_fase_1->id, 'name' => 'submited_fase1'],
                ['label' => 'Fase 1 enviada', 'type' => 'hidden', 'required' => false, 'instructions' => null]
            );

            // 5. Respuesta del comité (Fase 1)
            Campo::updateOrCreate(
                ['tipo_solicitud_id' => $practicas_fase_1->id, 'name' => 'respuesta_comite_fase1'],
                ['label' => 'Respuesta del comité', 'type' => 'textarea', 'required' => true, 'instructions' => 'Respuesta del comité a la solicitud de práctica']
            );
        }
    }
}