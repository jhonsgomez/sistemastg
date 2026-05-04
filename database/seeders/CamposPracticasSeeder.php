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

            // 12. Bandera para validar el envío de Fase 1
            Campo::updateOrCreate(
                ['tipo_solicitud_id' => $practicas_fase_0->id, 'name' => 'submited_fase0'],
                ['label' => 'Fase 0 enviada', 'type' => 'hidden', 'required' => false, 'instructions' => null]
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

        // ==================== FASE 2 ====================
        $practicas_fase_2 = TipoSolicitud::where('nombre', 'practicas_fase_2')->first();
        if ($practicas_fase_2) {
            // Restaurar campos eliminados lógicamente
            Campo::withTrashed()
                ->where('tipo_solicitud_id', $practicas_fase_2->id)
                ->restore();

            // 1. Liquidación de pago (PDF con marca de agua)
            Campo::updateOrCreate(
                ['tipo_solicitud_id' => $practicas_fase_2->id, 'name' => 'liquidacion_pago'],
                [
                    'label' => 'Liquidación de pago de modalidad',
                    'type' => 'file',
                    'required' => true,
                    'instructions' => '<p>Suba la liquidación generada del pago de modalidad en formato <strong>PDF</strong> (máx. 5 MB). El documento debe incluir la marca de agua correspondiente.</p>
                                    <ul class="list-disc mt-2 ml-4">
                                        <li class="ml-4">La liquidación debe estar debidamente diligenciada</li>
                                        <li class="ml-4">Debe contener la marca de agua del banco o entidad</li>
                                        <li class="ml-4">Asegúrese de que los datos sean legibles</li>
                                    </ul>'
                ]
            );

            // 2. Soporte de pago
            Campo::updateOrCreate(
                ['tipo_solicitud_id' => $practicas_fase_2->id, 'name' => 'soporte_pago'],
                [
                    'label' => 'Soporte de pago',
                    'type' => 'file',
                    'required' => true,
                    'instructions' => '<p>Suba el soporte de pago de la liquidación en formato <strong>PDF</strong> (máx. 5 MB).</p>
                                    <ul class="list-disc mt-2 ml-4">
                                        <li class="ml-4">El soporte debe corresponder a la liquidación adjunta</li>
                                        <li class="ml-4">Debe ser un comprobante de pago válido</li>
                                        <li class="ml-4">Asegúrese de que el monto sea el correcto</li>
                                    </ul>'
                ]
            );

            // 3. Bandera para validar el envío de Fase 2
            Campo::updateOrCreate(
                ['tipo_solicitud_id' => $practicas_fase_2->id, 'name' => 'submited_fase2'],
                [
                    'label' => 'Fase 2 enviada',
                    'type' => 'hidden',
                    'required' => false,
                    'instructions' => null
                ]
            );

            // 4. Respuesta del comité (Fase 2)
            Campo::updateOrCreate(
                ['tipo_solicitud_id' => $practicas_fase_2->id, 'name' => 'respuesta_comite_fase2'],
                [
                    'label' => 'Respuesta del comité',
                    'type' => 'textarea',
                    'required' => true,
                    'instructions' => 'Respuesta del comité a la solicitud de pago de modalidad'
                ]
            );

            // 5. Director de práctica (asignado por el comité)
            Campo::updateOrCreate(
                ['tipo_solicitud_id' => $practicas_fase_2->id, 'name' => 'director_id'],
                [
                    'label' => 'Director de práctica',
                    'type' => 'hidden',
                    'required' => false,
                    'instructions' => 'Docente asignado como director de la práctica empresarial'
                ]
            );

            // 6. Evaluador de práctica (asignado por el comité)
            Campo::updateOrCreate(
                ['tipo_solicitud_id' => $practicas_fase_2->id, 'name' => 'evaluador_id'],
                [
                    'label' => 'Evaluador de práctica',
                    'type' => 'hidden',
                    'required' => false,
                    'instructions' => 'Docente asignado como evaluador de la práctica empresarial'
                ]
            );

            // 7. Codirector de práctica (opcional - asignado por el comité)
            Campo::updateOrCreate(
                ['tipo_solicitud_id' => $practicas_fase_2->id, 'name' => 'codirector_id'],
                [
                    'label' => 'Codirector de práctica',
                    'type' => 'hidden',
                    'required' => false,
                    'instructions' => 'Docente asignado como codirector de la práctica empresarial (opcional)'
                ]
            );
        }

    }
}