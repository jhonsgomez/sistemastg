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

            // 7.1 Hoja de vida segundo integrante
            Campo::updateOrCreate(
                ['tipo_solicitud_id' => $practicas_fase_0->id,'name' => 'hoja_vida_2'],
                ['label' => 'Hoja de vida segundo integrante (PDF)','type' => 'file','required' => false,'instructions' => 'Suba la hoja de vida del segundo integrante si NO cuenta con empresa.']
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
                ['label' => 'Nombre de la empresa', 'type' => 'text', 'required' => false, 'instructions' => 'Ingrese el nombre de la empresa donde realizará la práctica.', 'placeholder' => 'Escribe el nombre de la empresa']
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

            // 4. Código de modalidad (se genera automáticamente al aprobar)
        Campo::updateOrCreate(
            ['tipo_solicitud_id' => $practicas_fase_2->id, 'name' => 'codigo_modalidad'],
            [
                'label' => 'Código de modalidad',
                'type' => 'text',
                'required' => false,
                'instructions' => 'Código generado automáticamente para la práctica empresarial (ej: 65-2025-001)'
            ]
        );

        }


        // ################ FASE 3 #####################

        $practicas_fase_3 = TipoSolicitud::where('nombre', 'practicas_fase_3')->first();
        if ($practicas_fase_3) {
            // Restaurar campos eliminados lógicamente
            Campo::withTrashed()
                ->where('tipo_solicitud_id', $practicas_fase_3->id)
                ->restore();

            // 1. ARL
            Campo::updateOrCreate(
                ['tipo_solicitud_id' => $practicas_fase_3->id, 'name' => 'arl'],
                [
                    'label' => 'Administradora de Riesgos Laborales',
                    'type' => 'file',
                    'required' => true,
                    'instructions' => '<p>Suba el ARL en formato <strong>PDF</strong> (máx. 5 MB).</p>
                                    <ul class="list-disc mt-2 ml-4">
                                        <li class="ml-4">Debe contener la firma de la entidad</li>
                                        <li class="ml-4">Asegúrese de que los datos sean legibles</li>
                                    </ul>'
                ]
            );

            // 1. FDC-127 FORMATO DE PROPUESTA DE PRACTICAS
            Campo::updateOrCreate(
                ['tipo_solicitud_id' => $practicas_fase_3->id, 'name' => 'doc_fdc127'],
                [
                    'label' => 'Formato F-DC-127',
                    'type' => 'file',
                    'required' => true,
                    'instructions' => '<p>Suba el Formato propuesta de prácticas F-DC-127 en formato <strong>WORD</strong> (máx. 5 MB).</p>'
                ]
            );

            // 1. FDC-195 Acta de inicio de prácticas 
            Campo::updateOrCreate(
                ['tipo_solicitud_id' => $practicas_fase_3->id, 'name' => 'doc_fdc195'],
                [
                    'label' => 'Formato F-DC-195',
                    'type' => 'file',
                    'required' => true,
                    'instructions' => '<p>Suba el Formato de acta de inicio F-DC-195 en formato <strong>WORD</strong> (máx. 5 MB).</p>'
                ]
            );

            // 3. Bandera envío
            Campo::updateOrCreate(
                [
                    'tipo_solicitud_id' => $practicas_fase_3->id,
                    'name' => 'submited_fase3'
                ],
                [
                    'label' => 'Fase 3 enviada',
                    'type' => 'hidden',
                    'required' => false,
                    'instructions' => null
                ]
            );

            // 4. Estado director fase 3
            Campo::updateOrCreate(
                [
                    'tipo_solicitud_id' => $practicas_fase_3->id,
                    'name' => 'estado_director_fase3'
                ],
                [
                    'label' => 'Estado respuesta director fase 3',
                    'type' => 'select',
                    'required' => false,
                    'instructions' => null
                ]
            );

            // 5. Título propuesta director fase 3
            Campo::updateOrCreate(
                [
                    'tipo_solicitud_id' => $practicas_fase_3->id,
                    'name' => 'titulo_propuesta_director_fase3'
                ],
                [
                    'label' => 'Título propuesta director fase 3',
                    'type' => 'text',
                    'required' => false,
                    'instructions' => null
                ]
            );

            // 6. FDC127 firmado/comentado director
            Campo::updateOrCreate(
                [
                    'tipo_solicitud_id' => $practicas_fase_3->id,
                    'name' => 'fdc127_director_fase3'
                ],
                [
                    'label' => 'FDC127 director fase 3',
                    'type' => 'file',
                    'required' => false,
                    'instructions' => '<p>Suba el F-DC-127 firmado o con comentarios.</p>'
                ]
            );

            // 7. FDC195 firmado/comentado director
            Campo::updateOrCreate(
                [
                    'tipo_solicitud_id' => $practicas_fase_3->id,
                    'name' => 'fdc195_director_fase3'
                ],
                [
                    'label' => 'FDC195 director fase 3',
                    'type' => 'file',
                    'required' => false,
                    'instructions' => '<p>Suba el F-DC-195 firmado o con comentarios.</p>'
                ]
            );

            // 8. Turnitin director fase 3
            Campo::updateOrCreate(
                [
                    'tipo_solicitud_id' => $practicas_fase_3->id,
                    'name' => 'turnitin_director_fase3'
                ],
                [
                    'label' => 'Turnitin director fase 3',
                    'type' => 'file',
                    'required' => false,
                    'instructions' => '<p>Suba el informe Turnitin en PDF.</p>'
                ]
            );

            // 9. Respuesta director
            Campo::updateOrCreate(
                [
                    'tipo_solicitud_id' => $practicas_fase_3->id,
                    'name' => 'respuesta_director_fase3'
                ],
                [
                    'label' => 'Respuesta del director',
                    'type' => 'textarea',
                    'required' => false,
                    'instructions' => null
                ]
            );


    
        }

        // ################ FASE 4 #####################

        $practicas_fase_4 = TipoSolicitud::where('nombre', 'practicas_fase_4')->first();
        if ($practicas_fase_4) {

        //---------------------EVALUADOR ---------------------------------------
        // 1. Respuesta evaluador
        Campo::updateOrCreate(
            [
                'tipo_solicitud_id' => $practicas_fase_4->id,
                'name' => 'respuesta_evaluador_fase4'
            ],
            [
                'label' => 'Respuesta del evaluador',
                'type' => 'textarea',
                'required' => false,
                'instructions' => null
            ]
        );

        // 4. Estado evaluador fase 4
            Campo::updateOrCreate(
                [
                    'tipo_solicitud_id' => $practicas_fase_4->id,
                    'name' => 'estado_evaluador_fase4'
                ],
                [
                    'label' => 'Estado respuesta evaluador fase 4',
                    'type' => 'select',
                    'required' => false,
                    'instructions' => null
                ]
            );

        // ================= COMITÉ =================

            // Estado comité
            Campo::updateOrCreate(
                [
                    'tipo_solicitud_id' => $practicas_fase_4->id,
                    'name' => 'estado_comite_fase4'
                ],
                [
                    'label' => 'Estado respuesta comité fase 4',
                    'type' => 'select',
                    'required' => false,
                    'instructions' => null
                ]
            );

             // Título propuesta
            Campo::updateOrCreate(
                [
                    'tipo_solicitud_id' => $practicas_fase_4->id,
                    'name' => 'titulo_propuesta_fase4'
                ],
                [
                    'label' => 'Título de la propuesta',
                    'type' => 'text',
                    'required' => false,
                    'instructions' => null
                ]
            );


            // Respuesta comité
            Campo::updateOrCreate(
                [
                    'tipo_solicitud_id' => $practicas_fase_4->id,
                    'name' => 'respuesta_comite_fase4'
                ],
                [
                    'label' => 'Respuesta del comité',
                    'type' => 'textarea',
                    'required' => false,
                    'instructions' => null
                ]
            );


        
        }













    }
}