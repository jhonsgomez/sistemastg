<?php

return [

    'dias_minimo' => env('PRACTICA_DIAS_MINIMO', 90),
    'dias_maximo' => env('PRACTICA_DIAS_MAXIMO', 180),
    'dias_prorroga' => env('PRACTICA_PRORROGA_DIAS', 90),
    'dias_gracia_fase5' => env('PRACTICA_GRACIA_ENVIO_FASE5', 15),

    'peso_maximo_archivos' => env('PESO_MAXIMO_ARCHIVOS', 8),
    'peso_maximo_propuesta' => env('PESO_MAXIMO_PROPUESTA', 4),
    'peso_maximo_informe' => env('PESO_MAXIMO_ARCHIVO', 6),
    'peso_maximo_hojavida' => env('PESO_MAXIMO_HOJAVIDA', 9),
    
    'correos' => [

        'practicas_fase_0' => [

            'subject' =>
                'PRÁCTICAS EMPRESARIALES - FASE 0',

            'view' =>
                'emails.practicas.fase0',
        ],

        'respuesta_comite' => [

            'subject' =>
                'RESPUESTA SOLICITUD PRÁCTICAS',

            'view' =>
                'emails.practicas.respuestaF0',
        ],

        'practicas_fase_1' => [

            'subject' =>
                'PRÁCTICAS EMPRESARIALES - FASE 1 F-DC-126',

            'view' =>
                'emails.practicas.fase1',
        ],

        'respuesta_fase_1' => [

            'subject' =>
                'RESPUESTA PRÁCTICAS - FASE 1',

            'view' =>
                'emails.practicas.respuestaF1',
        ],

        'practicas_fase_2' => [

            'subject' =>
                'PRÁCTICAS EMPRESARIALES - FASE 2: PAGO',

            'view' =>
                'emails.practicas.fase2',
        ],

        'respuesta_fase_2' => [

            'subject' =>
                'RESPUESTA PRÁCTICAS - FASE 2',

            'view' =>
                'emails.practicas.respuestaF2',
        ],

           'practicas_fase_3' => [

            'subject' =>
                'PRÁCTICAS EMPRESARIALES - FASE 3: DOCUMENTOS F-DC-127',

            'view' =>
                'emails.practicas.fase3',
        ],

         'respuesta_fase_3' => [

            'subject' =>
                'RESPUESTA PRÁCTICAS - FASE 3',

            'view' =>
                'emails.practicas.respuestaF3',
        ],

        
        'respuesta_fase_4' => [

            'subject' =>
                'RESPUESTA PRÁCTICAS - FASE 4',

            'view' =>
                'emails.practicas.respuestaF4',
        ],

        
        'practicas_fase_5' => [

            'subject' =>
                'PRÁCTICAS EMPRESARIALES - FASE 5: INFORME I',

            'view' =>
                'emails.practicas.fase5',
        ],

         'respuesta_fase_5' => [

            'subject' =>
                'RESPUESTA PRÁCTICAS - FASE 5 INFORME I',

            'view' =>
                'emails.practicas.respuestaF5',
        ],

         'respuesta_fase_6' => [

            'subject' =>
                'RESPUESTA PRÁCTICAS - FASE 6',

            'view' =>
                'emails.practicas.respuestaF6',
        ],

          'recordatorio_practica' => [

            'subject' =>
                'RECORDATORIO PRÁCTICAS',

            'view' =>
                'emails.practicas.recordatorio',
        ],

        'recordatorio_prorroga' => [

            'subject' =>
                'RECORDATORIO SOLICITUD DE PRÓRROGA - PRÁCTICAS',

            'view' =>
                'emails.practicas.recordatorioProrroga',
        ],

        'recordatorio_revision_director_fase5' => [

            'subject' =>
                'RECORDATORIO REVISIÓN DOCUMENTOS FINALES - PRÁCTICAS',

            'view' =>
                'emails.practicas.recordatorioRevisionDirectorFase5',
        ],

        'solicitud_icfes_practicas' => [

            'subject' =>
                'SOLICITUD BENEFICIO ICFES - PRÁCTICAS EMPRESARIALES',

            'view' =>
                'emails.practicas.solicitudIcfes',
        ],

        'respuesta_icfes_practicas' => [

            'subject' =>
                'RESPUESTA SOLICITUD BENEFICIO ICFES - PRÁCTICAS',

            'view' =>
                'emails.practicas.respuestaIcfes',
        ],

        'practica_habilitada' => [
            'subject' => 'PRÁCTICA HABILITADA',
            'view' => 'emails.practicas.practicaHabilitada',
        ],

        'practica_deshabilitada' => [
            'subject' => 'PRÁCTICA DESHABILITADA',
            'view' => 'emails.practicas.practicaDeshabilitada',
        ],

        'solicitud_ajuste_practica' => [

            'subject' =>
                'SOLICITUD DE AJUSTE DE PRÁCTICA EMPRESARIAL',

            'view' =>
                'emails.practicas.solicitudAjustePractica',
        ],

        'respuesta_ajuste_practica' => [

            'subject' =>
                'RESPUESTA SOLICITUD DE AJUSTE - PRÁCTICAS',

            'view' =>
                'emails.practicas.respuestaAjustePractica',
        ],







    ],
];