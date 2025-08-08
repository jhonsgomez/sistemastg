<?php

namespace Database\Seeders;

use App\Models\Campo;
use App\Models\TipoSolicitud;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CamposSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asumimos que el tipo de solicitud 'propuesta_banco' ya fue creado
        $solicitud_banco = TipoSolicitud::where('nombre', 'solicitud_banco')->first();

        Campo::create([
            'tipo_solicitud_id' => $solicitud_banco->id,
            'name' => 'titulo',
            'label' => 'Título de la idea',
            'type' => 'textarea',
            'placeholder' => 'Titúlo de la idea',
            'required' => true,
            'instructions' => 'Describa el título de la idea.',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $solicitud_banco->id,
            'name' => 'modalidad',
            'label' => 'Tipo de modalidad',
            'type' => 'select',
            'required' => true,
            'instructions' => 'Seleccione la modalidad de la idea.',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $solicitud_banco->id,
            'name' => 'objetivo',
            'label' => 'Objetivo general de la idea',
            'type' => 'textarea',
            'placeholder' => 'Objetivo general de la idea',
            'required' => true,
            'instructions' => 'Describa brevemente el objetivo de la idea.',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $solicitud_banco->id,
            'name' => 'linea_investigacion',
            'label' => 'Línea de investigación',
            'type' => 'select',
            'required' => true,
            'instructions' => 'Seleccione la línea de investigación asociada a la idea.',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $solicitud_banco->id,
            'name' => 'nivel',
            'label' => 'Tipo de nivel',
            'type' => 'select',
            'required' => true,
            'instructions' => 'Seleccione el nivel académico de la idea.',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $solicitud_banco->id,
            'name' => 'periodo',
            'label' => 'Periodo académico',
            'type' => 'hidden',
            'required' => true,
        ]);

        Campo::create([
            'tipo_solicitud_id' => $solicitud_banco->id,
            'name' => 'disponible',
            'label' => 'Disponible',
            'type' => 'hidden',
            'required' => true,
        ]);

        // Campos para fase 0
        $fase_0 = TipoSolicitud::where('nombre', 'fase_0')->first();

        Campo::create([
            'tipo_solicitud_id' => $fase_0->id,
            'name' => 'nivel',
            'label' => 'Tipo de nivel',
            'type' => 'text',
            'placeholder' => 'Nivel académico del proyecto.',
            'required' => true,
            'instructions' => '<p><strong class="uppercase">NOTA: </strong> Si este nivel es incorrecto debe cambiarlo desde su perfil.</p>',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_0->id,
            'name' => 'modalidad',
            'label' => 'Tipo de modalidad',
            'type' => 'select',
            'required' => true,
            'instructions' => 'Seleccione la modalidad del proyecto.',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_0->id,
            'name' => 'id_integrante_1',
            'label' => 'Primer integrante',
            'type' => 'text',
            'placeholder' => 'Primer integrante del proyecto',
            'required' => true,
            'instructions' => 'Usted es el primer integrante de este proyecto.',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_0->id,
            'name' => 'id_integrante_2',
            'label' => 'Segundo integrante',
            'type' => 'select',
            'placeholder' => 'Segundo integrante del proyecto',
            'required' => false,
            'instructions' => 'Seleccione el otro integrante de este proyecto.',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_0->id,
            'name' => 'id_integrante_3',
            'label' => 'Tercer integrante',
            'type' => 'select',
            'placeholder' => 'Tercer integrante del proyecto',
            'required' => false,
            'instructions' => 'Si selecciona este integrante, el comité se comunicará con usted para validar información.',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_0->id,
            'name' => 'periodo',
            'label' => 'Periodo académico',
            'type' => 'hidden',
            'required' => true,
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_0->id,
            'name' => 'submited_retiro',
            'label' => 'Bandera para validar el envío',
            'type' => 'hidden',
            'required' => false,
        ]);

        // Campos para fase 1
        $fase_1 = TipoSolicitud::where('nombre', 'fase_1')->first();

        Campo::create([
            'tipo_solicitud_id' => $fase_1->id,
            'name' => 'check_idea_banco',
            'label' => '¿Su proyecto está en el banco de ideas?',
            'type' => 'checkbox',
            'required' => false,
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_1->id,
            'name' => 'idea_banco',
            'label' => 'Proyecto del banco de ideas',
            'type' => 'select',
            'required' => false,
            'instructions' => 'Seleccione una idea del banco para su proyecto.',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_1->id,
            'name' => 'titulo',
            'label' => 'Título de la idea',
            'type' => 'textarea',
            'placeholder' => 'Titúlo de la idea que el estudiante desea desarrollar',
            'required' => false,
            'instructions' => 'Describe el título de la idea.',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_1->id,
            'name' => 'objetivo',
            'label' => 'Objetivo general de la idea',
            'type' => 'textarea',
            'placeholder' => 'Objetivo general de la idea que el estudiante desea desarrollar',
            'required' => false,
            'instructions' => 'Describe el objetivo de la idea.',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_1->id,
            'name' => 'linea_investigacion',
            'label' => 'Línea de investigación',
            'type' => 'select',
            'required' => false,
            'instructions' => 'Seleccione la línea de investigación de la idea.',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_1->id,
            'name' => 'descripcion',
            'label' => 'Descripción de la idea',
            'type' => 'textarea',
            'placeholder' => 'Descripción de la idea que el estudiante desea desarrollar',
            'required' => false,
            'instructions' => 'Describe la finalidad de la idea.',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_1->id,
            'name' => 'soporte_pago',
            'label' => 'Documentos (Liquidaciones y soportes)',
            'type' => 'file',
            'required' => true,
            'instructions' => '<p><strong>NOTA:</strong> Se debe subir <strong>UN ÚNICO</strong> archivo <strong>PDF</strong> que contenga los siguientes elementos por <strong>CADA</strong> estudiante:<ul class="list-disc mt-2"><li class="ml-4">Liquidación de la propuesta cancelada.</li><li class="ml-4">Soportes de pago de la liquidación.</li></ul></p>',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_1->id,
            'name' => 'codigo_modalidad',
            'label' => 'Código de modalidad',
            'type' => 'hidden',
            'required' => false,
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_1->id,
            'name' => 'director_id',
            'label' => 'Director',
            'type' => 'hidden',
            'required' => false,
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_1->id,
            'name' => 'evaluador_id',
            'label' => 'Evaluador',
            'type' => 'hidden',
            'required' => false,
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_1->id,
            'name' => 'codirector_id',
            'label' => 'Codirector',
            'type' => 'hidden',
            'required' => false,
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_1->id,
            'name' => 'submited',
            'label' => 'Bandera para validar el envío',
            'type' => 'hidden',
            'required' => true,
        ]);

        // Campos para fase 2
        $fase_2 = TipoSolicitud::where('nombre', 'fase_2')->first();

        Campo::create([
            'tipo_solicitud_id' => $fase_2->id,
            'name' => 'doc_propuesta',
            'label' => 'Formato de propuesta (F-DC-124)',
            'type' => 'file',
            'required' => true,
            'instructions' => 'Solo se debe subir un archivo en formato Word.',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_2->id,
            'name' => 'recordatorio_fase2',
            'label' => 'Recordatorio de fase 2',
            'type' => 'hidden',
            'required' => true,
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_2->id,
            'name' => 'submited_fase2',
            'label' => 'Bandera para validar el envío',
            'type' => 'hidden',
            'required' => true,
        ]);

        // Campos para fase 3
        $fase_3 = TipoSolicitud::where('nombre', 'fase_3')->first();

        Campo::create([
            'tipo_solicitud_id' => $fase_3->id,
            'name' => 'submited_fase3_director',
            'label' => 'Bandera para validar el envío',
            'type' => 'hidden',
            'required' => true,
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_3->id,
            'name' => 'recordatorio_fase3',
            'label' => 'Recordatorio de fase 3',
            'type' => 'hidden',
            'required' => true,
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_3->id,
            'name' => 'doc_turnitin',
            'label' => 'Informe de plagio',
            'type' => 'file',
            'required' => true,
            'instructions' => 'Solo se debe subir un archivo en formato pdf.',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_3->id,
            'name' => 'submited_fase3_evaluador',
            'label' => 'Bandera para validar el envío',
            'type' => 'hidden',
            'required' => true,
        ]);

        // Campos para fase 4
        $fase_4 = TipoSolicitud::where('nombre', 'fase_4')->first();

        Campo::create([
            'tipo_solicitud_id' => $fase_4->id,
            'name' => 'recordatorio_fase4',
            'label' => 'Recordatorio de fase 4',
            'type' => 'hidden',
            'required' => true,
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_4->id,
            'name' => 'fecha_inicio_informe',
            'label' => 'Fecha de inicio del informe',
            'type' => 'date',
            'required' => true,
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_4->id,
            'name' => 'fecha_maxima_informe',
            'label' => 'Fecha máxima del informe',
            'type' => 'date',
            'required' => true,
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_4->id,
            'name' => 'doc_informe',
            'label' => 'Informe final (F-DC-125)',
            'type' => 'file',
            'required' => true,
            'instructions' => 'Solo se debe subir un archivo en formato Word.',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_4->id,
            'name' => 'doc_rejilla',
            'label' => 'Rejilla de evaluación (F-DC-129)',
            'type' => 'file',
            'required' => true,
            'instructions' => 'Solo se debe subir un archivo en formato Word.',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_4->id,
            'name' => 'submited_fase4',
            'label' => 'Bandera para validar el envío',
            'type' => 'hidden',
            'required' => true,
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_4->id,
            'name' => 'doc_icfes',
            'label' => 'Evidencias de resultados pruebas TyT/Pro',
            'type' => 'file',
            'required' => true,
            'instructions' => 'Solo se debe subir un archivo en formato PDF.',
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_4->id,
            'name' => 'submited_icfes',
            'label' => 'Bandera para validar el envío',
            'type' => 'hidden',
            'required' => false,
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_4->id,
            'name' => 'beneficiarios_icfes',
            'label' => 'Bandera para validar beneficiario',
            'type' => 'hidden',
            'required' => false,
        ]);

        // Campos para fase 5
        $fase_5 = TipoSolicitud::where('nombre', 'fase_5')->first();

        Campo::create([
            'tipo_solicitud_id' => $fase_5->id,
            'name' => 'recordatorio_fase5',
            'label' => 'Recordatorio de fase 5',
            'type' => 'hidden',
            'required' => true,
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_5->id,
            'name' => 'submited_fase5_director',
            'label' => 'Bandera para validar el envío',
            'type' => 'hidden',
            'required' => true,
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_5->id,
            'name' => 'submited_fase5_evaluador',
            'label' => 'Bandera para validar el envío',
            'type' => 'hidden',
            'required' => true,
        ]);

        Campo::create([
            'tipo_solicitud_id' => $fase_5->id,
            'name' => 'doc_turnitin_informe',
            'label' => 'Informe de plagio',
            'type' => 'file',
            'required' => true,
            'instructions' => 'Solo se debe subir un archivo en formato pdf.',
        ]);
    }
}
