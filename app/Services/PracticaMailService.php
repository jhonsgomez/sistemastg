<?php

namespace App\Services;

use App\Mail\PracticasMail;
use App\Models\Nivel;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PracticaMailService
{
    public function sendSolicitud($practica): void
    {
        $this->send(
            'practicas_fase_0',
            $practica
        );
    }

    // ================= ENVIO Y RESPUESTA FASE 0 - SOLICITUD DE PRACTICAS =================
    private function send(string $tipoCorreo,$practica, array $extraData = []): void 
    {
            try {

                $user = $practica->user;

                $campos = $practica->camposConValores();

                $data = [

                    'tipo_correo' => $tipoCorreo,

                    'comentarios' =>
                        $extraData['comentarios'] ?? null,

                    'es_respuesta' =>
                        $extraData['es_respuesta'] ?? false,

                    'adjuntos' => [],

                    'adjuntar_archivos' => $extraData['adjuntar_archivos'] ?? false,

                    'cuerpo_correo' => [

                        'estudiante' => $user,

                        'correo' => $user->email ?? '',

                        'estado' =>
                            $extraData['estado']
                            ?? $practica->estado,

                        'nuevo_estado' =>
                            $extraData['nuevo_estado']
                            ?? null,

                        'mensaje' =>
                            $extraData['mensaje']
                            ?? null,

                        'celular' =>
                            $user->nro_celular ?? '',

                        'nivel' =>
                            $user->nivel->nombre ?? '',

                        'periodo' =>
                            $practica->periodo ?? '2026-1',

                        'integrante_2' => null,

                        'integrante_2_documento' => null,

                        'integrante_2_correo' => null,

                        'integrante_2_celular' => null,

                        'campos' => $campos,
                    ],
                ];

                foreach ($campos as $campo) {

                    $nombreCampo = $campo['campo'];

                    $valorCampo = $campo['valor'];

                    switch ($nombreCampo) {

                        case 'nivel':

                            $data['cuerpo_correo']['nivel'] =
                                Nivel::find($valorCampo)->nombre
                                ?? $valorCampo;

                            break;

                        case 'empresa':

                            $data['cuerpo_correo']['empresa'] =
                                $valorCampo;

                            break;

                        case 'hoja_vida':

                            $data['adjuntos'][] =
                                $valorCampo;

                            break;

                        case 'id_integrante_2':

                            if (!empty($valorCampo)) {

                                $integrante =
                                    User::find($valorCampo);

                                if ($integrante) {

                                    $data['cuerpo_correo']['integrante_2'] =
                                        $integrante;

                                    $data['cuerpo_correo']['integrante_2_documento'] =
                                        ($integrante->tipo_documento->tag ?? '')
                                        . ' ' .
                                        ($integrante->nro_documento ?? '');

                                    $data['cuerpo_correo']['integrante_2_correo'] =
                                        $integrante->email ?? '';

                                    $data['cuerpo_correo']['integrante_2_celular'] =
                                        $integrante->nro_celular ?? '';
                                }
                            }

                            break;

                        default:

                            $data['cuerpo_correo'][$nombreCampo] =
                                $valorCampo;

                            break;
                    }
                }

                //Envio de correo a destinatarios , segundo integrante

                $destinatarios = [
                    $user->email,
                ];

                if (!empty($data['cuerpo_correo']['integrante_2_correo'])) {
                    $destinatarios[] = $data['cuerpo_correo']['integrante_2_correo'];
                }

                $destinatarios = array_unique(array_filter($destinatarios));

                Mail::to($destinatarios)
                    ->queue(new PracticasMail($data));

            } catch (\Throwable $e) {

                dd(
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                );

            }
    }

    public function sendRespuesta($practica, string $estadoActual, string $nuevoEstado, string $mensaje, string $estadoRespuesta): void 
    {
        $this->send(
            'respuesta_comite',
            $practica,
            [
                'comentarios' => $mensaje,
                'es_respuesta' => true,
                'estado_actual' => $estadoActual,
                'estado' => $estadoRespuesta,
                'nuevo_estado' => $nuevoEstado,
                'mensaje' => $mensaje,
                'adjuntar_archivos' => false,
            ]
        );
    }

    // ================= ENVIO Y RESPUESTA FASE 1 -  =================

    public function sendFase1($practica)
    {
        $practica->load([
            'user.tipo_documento',
            'valoresCampos.campo'
        ]);

        $user = $practica->user;

        $campos = [];

        foreach ($practica->valoresCampos as $valorCampo) {
            $campos[] = [
                'campo' => $valorCampo->campo->name,
                'valor' => $valorCampo->valor,
            ];
        }

        $empresa = collect($campos)
            ->firstWhere('campo', 'nombre_empresa');

        $practicaInstitucional = collect($campos)
            ->firstWhere('campo', 'es_institucional');

        $fdc126 = collect($campos)
            ->firstWhere('campo', 'doc_fdc126');

        $integrante2Campo = collect($campos)
            ->firstWhere('campo', 'id_integrante_2');

        $integrante2 = null;

        if (!empty($integrante2Campo['valor'])) {
            $integrante2 = User::with('tipo_documento')
                ->find($integrante2Campo['valor']);
        }

        $data = [
            'tipo_correo' => 'practicas_fase_1',

            'cuerpo_correo' => [
                'estado' => $practica->estado,

                'empresa' => $empresa['valor'] ?? 'No registra',

                'practica_institucional' =>
                ($practicaInstitucional['valor'] ?? false) == 'true'
                    ? 'Sí'
                    : 'No',

                'correo' => $user->email,
                'estudiante' => $user,

                'integrante_2' => $integrante2,
                'integrante_2_correo' => $integrante2->email ?? null,
                'integrante_2_documento' => $integrante2
                    ? (($integrante2->tipo_documento->tag ?? '') . ' ' . ($integrante2->nro_documento ?? ''))
                    : null,
                'integrante_2_celular' => $integrante2->nro_celular ?? null,
            ],

            'adjuntos' => [
                $fdc126['valor'] ?? null
            ],
            'adjuntar_archivos' => false,
        ];

        $data['adjuntos'] = array_filter($data['adjuntos']);

        $destinatarios = [
            $user->email,
        ];

        if (!empty($integrante2?->email)) {
            $destinatarios[] = $integrante2->email;
        }

        $destinatarios = array_unique(array_filter($destinatarios));

        Mail::to($destinatarios)
            ->queue(new PracticasMail($data));
    }

    public function sendRespuestaFase1($practica, $request) 
    {
        $practica->load([
            'user.tipo_documento',
            'valoresCampos.campo'
        ]);

        $user = $practica->user;

        $campos = [];

        foreach ($practica->valoresCampos as $valorCampo) {
            $campos[] = [
                'campo' => $valorCampo->campo->name,
                'valor' => $valorCampo->valor,
            ];
        }

        $empresa = collect($campos)
            ->firstWhere('campo', 'nombre_empresa');

        $practicaInstitucional = collect($campos)
            ->firstWhere('campo', 'es_institucional');

        $integrante2Campo = collect($campos)
            ->firstWhere('campo', 'id_integrante_2');

        $integrante2 = null;

        if (!empty($integrante2Campo['valor'])) {
            $integrante2 = User::with('tipo_documento')
                ->find($integrante2Campo['valor']);
        }

        $data = [
            'tipo_correo' => 'respuesta_fase_1',

            'cuerpo_correo' => [
                'estado' => $request->estado,
                'respuesta_fase1' => $request->respuesta_fase1,
                'nro_acta' => $request->nro_acta,
                'fecha_acta' => $request->fecha_acta,

                'empresa' => $empresa['valor'] ?? 'No registra',

                'practica_institucional' =>
                    ($practicaInstitucional['valor'] ?? false) == 'true'
                        ? 'Sí'
                        : 'No',

                'correo' => $user->email,
                'celular' => $user->nro_celular ?? '',
                'estudiante' => $user,

                'integrante_2' => $integrante2,
                'integrante_2_correo' => $integrante2->email ?? null,
                'integrante_2_documento' => $integrante2
                    ? (($integrante2->tipo_documento->tag ?? '') . ' ' . ($integrante2->nro_documento ?? ''))
                    : null,
                'integrante_2_celular' => $integrante2->nro_celular ?? null,
            ],
            'adjuntar_archivos' => false,
        ];

        $destinatarios = [
            $user->email,
        ];

        if (!empty($integrante2?->email)) {
            $destinatarios[] = $integrante2->email;
        }

        $destinatarios = array_unique(array_filter($destinatarios));

        Mail::to($destinatarios)
            ->queue(new PracticasMail($data));
    }
    
     // ================= ENVIO Y RESPUESTA FASE 2 -  =================                   
    public function sendFase2($practica)
    {
        $practica->load([
            'user.tipo_documento',
            'valoresCampos.campo'
        ]);

        // Obtener valores dinámicos
        $campos = [];

        foreach ($practica->valoresCampos as $valorCampo) {

            $campos[] = [
                'campo' => $valorCampo->campo->name,
                'valor' => $valorCampo->valor,
            ];
        }

        // Buscar documentos
        $liquidacionPago = collect($campos)
            ->firstWhere('campo', 'liquidacion_pago');

        $soportePago = collect($campos)
            ->firstWhere('campo', 'soporte_pago');

        // Data del correo
        $data = [

            'tipo_correo' => 'practicas_fase_2',

            'cuerpo_correo' => [

                'estado' => $practica->estado,

                'correo' => $practica->user->email,

                'estudiante' => $practica->user,

                'celular' => $practica->user->nro_celular ?? '',

                'campos' => $campos,
            ],

            // ADJUNTOS
            'adjuntos' => [

                $liquidacionPago['valor'] ?? null,

                $soportePago['valor'] ?? null,
            ],

            'adjuntar_archivos' => false,
        ];

        // Limpiar adjuntos null
        $data['adjuntos'] = array_filter(
            $data['adjuntos']
        );

        $integrante2Campo = collect($campos)
            ->firstWhere('campo', 'id_integrante_2');

        $integrante2 = null;

        if (!empty($integrante2Campo['valor'])) {
            $integrante2 = User::with('tipo_documento')
                ->find($integrante2Campo['valor']);
        }

       $data['cuerpo_correo']['integrante_2'] = $integrante2;

        $data['cuerpo_correo']['integrante_2_correo'] =
            $integrante2->email ?? null;

        $data['cuerpo_correo']['integrante_2_documento'] =
            $integrante2
                ? (($integrante2->tipo_documento->tag ?? '') . ' ' . ($integrante2->nro_documento ?? ''))
                : null;

        $data['cuerpo_correo']['integrante_2_celular'] =
            $integrante2->nro_celular ?? null;

        $destinatarios = [
            $practica->user->email,
        ];

        if (!empty($integrante2?->email)) {
            $destinatarios[] = $integrante2->email;
        }

        $destinatarios = array_unique(array_filter($destinatarios));

        Mail::to($destinatarios)
            ->queue(new PracticasMail($data));
    }

    
    public function sendRespuestaFase2($practica, $respuesta)
    {
        $practica->load([
            'user.tipo_documento',
            'valoresCampos.campo'
        ]);

        $campos = [];

        foreach ($practica->valoresCampos as $valorCampo) {
            $campos[] = [
                'campo' => $valorCampo->campo->name,
                'valor' => $valorCampo->valor,
            ];
        }

        $integrante2Campo = collect($campos)
            ->firstWhere('campo', 'id_integrante_2');

        $integrante2 = null;

        if (!empty($integrante2Campo['valor'])) {
            $integrante2 = User::with('tipo_documento')
                ->find($integrante2Campo['valor']);
        }

        $director = !empty($respuesta['director_id'])
            ? User::find($respuesta['director_id'])
            : null;

        $evaluador = !empty($respuesta['evaluador_id'])
            ? User::find($respuesta['evaluador_id'])
            : null;

        $codirector = !empty($respuesta['codirector_id'])
            ? User::find($respuesta['codirector_id'])
            : null;

        $dataBase = [
            'tipo_correo' => 'respuesta_fase_2',

            'cuerpo_correo' => [
                'estado' => $respuesta['estado'],
                'respuesta' => $respuesta['respuesta'],

                'nro_acta' => $respuesta['nro_acta'] ?? null,
                'fecha_acta' => $respuesta['fecha_acta'] ?? null,

                'correo' => $practica->user->email,
                'estudiante' => $practica->user,

                'integrante_2' => $integrante2,
                'integrante_2_correo' => $integrante2->email ?? null,
                'integrante_2_documento' => $integrante2
                    ? (($integrante2->tipo_documento->tag ?? '') . ' ' . ($integrante2->nro_documento ?? ''))
                    : null,
                'integrante_2_celular' => $integrante2->nro_celular ?? null,

                'director' => $director,
                'evaluador' => $evaluador,
                'codirector' => $codirector,

                'campos' => $campos,
            ],

            'adjuntar_archivos' => false,
        ];

        // 1. Correo para estudiante e integrante 2
        $destinatariosEstudiantes = [
            $practica->user->email,
        ];

        if (!empty($integrante2?->email)) {
            $destinatariosEstudiantes[] = $integrante2->email;
        }

        $destinatariosEstudiantes = array_unique(array_filter($destinatariosEstudiantes));

        $dataEstudiantes = $dataBase;
        $dataEstudiantes['cuerpo_correo']['destinatario'] = 'estudiante';

        /*Mail::to($destinatariosEstudiantes)
            ->queue(new PracticasMail($dataEstudiantes));*/
        Mail::to($destinatariosEstudiantes)
            ->send(new PracticasMail($dataEstudiantes));

        // Solo si fue aprobada se notifica a los docentes asignados
        if (($respuesta['estado'] ?? '') !== 'Aprobada') {
            return;
        }

        // 2. Un solo correo para los docentes asignados
        $destinatariosDocentes = [];

        if (!empty($director?->email)) {
            $destinatariosDocentes[] = $director->email;
        }

        if (!empty($evaluador?->email)) {
            $destinatariosDocentes[] = $evaluador->email;
        }

        if (!empty($codirector?->email)) {
            $destinatariosDocentes[] = $codirector->email;
        }

        $destinatariosDocentes = array_unique(
            array_filter($destinatariosDocentes)
        );

        if (!empty($destinatariosDocentes)) {

            $dataDocentes = $dataBase;

            $dataDocentes['cuerpo_correo']['destinatario'] =
                'docentes';

            Mail::to($destinatariosDocentes)
                ->queue(new PracticasMail($dataDocentes));
        }
    }

    // ================= ENVIO Y RESPUESTA FASE 3 -  =================    
    public function sendFase3($practica)
    {
        $practica->load([
            'user.tipo_documento',
            'valoresCampos.campo'
        ]);

        $user = $practica->user;

        $campos = [];

        foreach ($practica->valoresCampos as $valorCampo) {
            $campos[] = [
                'campo' => $valorCampo->campo->name,
                'valor' => $valorCampo->valor,
            ];
        }

        // ================= DOCUMENTOS =================

        $arl = collect($campos)
            ->firstWhere('campo', 'arl');

        $fdc127 = collect($campos)
            ->firstWhere('campo', 'doc_fdc127');

        $fdc195 = collect($campos)
            ->firstWhere('campo', 'doc_fdc195');

        // ================= INTEGRANTE 2 =================

        $integrante2Campo = collect($campos)
            ->firstWhere('campo', 'id_integrante_2');

        $integrante2 = null;

        if (!empty($integrante2Campo['valor'])) {
            $integrante2 = User::with('tipo_documento')
                ->find($integrante2Campo['valor']);
        }

        // ================= DIRECTOR =================

        $directorCampo = collect($campos)
            ->firstWhere('campo', 'director_id');

        $director = null;

        if (!empty($directorCampo['valor'])) {
            $director = User::find($directorCampo['valor']);
        }

        // ================= DATA CORREO =================

        $data = [
            'tipo_correo' => 'practicas_fase_3',

            'cuerpo_correo' => [
                'estado' => $practica->estado,

                'correo' => $user->email,
                'estudiante' => $user,
                'celular' => $user->nro_celular ?? '',

                'integrante_2' => $integrante2,
                'integrante_2_correo' => $integrante2->email ?? null,
                'integrante_2_documento' => $integrante2
                    ? (($integrante2->tipo_documento->tag ?? '') . ' ' . ($integrante2->nro_documento ?? ''))
                    : null,
                'integrante_2_celular' => $integrante2->nro_celular ?? null,

                'director' => $director,
                'director_correo' => $director->email ?? null,

                'campos' => $campos,
            ],

            'adjuntos' => [
                $arl['valor'] ?? null,
                $fdc127['valor'] ?? null,
                $fdc195['valor'] ?? null,
            ],
            'adjuntar_archivos' => false,
        ];

        $data['adjuntos'] = array_filter($data['adjuntos']);

        // ================= DESTINATARIOS =================

        $destinatarios = [
            $user->email,
        ];

        if (!empty($integrante2?->email)) {
            $destinatarios[] = $integrante2->email;
        }

        if (!empty($director?->email)) {
            $destinatarios[] = $director->email;
        }

        $destinatarios = array_unique(array_filter($destinatarios));

        Mail::to($destinatarios)
            ->queue(new PracticasMail($data));
    }
    
    public function sendRespuestaFase3($practica, $respuesta)
    {
        $practica->load([
            'user.tipo_documento',
            'valoresCampos.campo'
        ]);

        $campos = [];

        foreach ($practica->valoresCampos as $valorCampo) {
            $campos[] = [
                'campo' => $valorCampo->campo->name,
                'valor' => $valorCampo->valor,
            ];
        }

        $integrante2Campo = collect($campos)
            ->firstWhere('campo', 'id_integrante_2');

        $directorCampo = collect($campos)
            ->firstWhere('campo', 'director_id');

        $evaluadorCampo = collect($campos)
            ->firstWhere('campo', 'evaluador_id');

        $integrante2 = !empty($integrante2Campo['valor'])
            ? User::with('tipo_documento')->find($integrante2Campo['valor'])
            : null;

        $director = !empty($directorCampo['valor'])
            ? User::find($directorCampo['valor'])
            : null;

        $evaluador = !empty($evaluadorCampo['valor'])
            ? User::find($evaluadorCampo['valor'])
            : null;

        $dataBase = [
            'tipo_correo' => 'respuesta_fase_3',

            'cuerpo_correo' => [
                'estado' => $respuesta->estado,
                'respuesta' => $respuesta->respuesta,

                'correo' => $practica->user->email,
                'estudiante' => $practica->user,
                'celular' => $practica->user->nro_celular ?? '',

                'integrante_2' => $integrante2,
                'integrante_2_correo' => $integrante2->email ?? null,
                'integrante_2_documento' => $integrante2
                    ? (($integrante2->tipo_documento->tag ?? '') . ' ' . ($integrante2->nro_documento ?? ''))
                    : null,
                'integrante_2_celular' => $integrante2->nro_celular ?? null,

                'director' => $director,
                'evaluador' => $evaluador,

                'campos' => $campos,
            ],
            'adjuntar_archivos' => false,
        ];

        // 1. Correo para estudiante e integrante 2
        $destinatariosEstudiantes = [
            $practica->user->email,
        ];

        if (!empty($integrante2?->email)) {
            $destinatariosEstudiantes[] = $integrante2->email;
        }

        $destinatariosEstudiantes = array_unique(array_filter($destinatariosEstudiantes));

        $dataEstudiantes = $dataBase;
        $dataEstudiantes['cuerpo_correo']['destinatario'] = 'estudiante';

        Mail::to($destinatariosEstudiantes)
            ->send(new PracticasMail($dataEstudiantes));

        // 2. Si fue aprobada, enviar también al evaluador
        if (($respuesta->estado ?? '') === 'Aprobada' && !empty($evaluador?->email)) {
            $dataEvaluador = $dataBase;
            $dataEvaluador['cuerpo_correo']['destinatario'] = 'evaluador';

            Mail::to($evaluador->email)
                ->send(new PracticasMail($dataEvaluador));
        }
    }
                        
    // ================= RESPUESTA EVALUADOR Y COMITE  FASE 4   =================    
    public function sendRespuestaFase4Evaluador($practica, $respuesta)
    {
        $practica->load([
            'user.tipo_documento',
            'valoresCampos.campo'
        ]);

        $campos = [];

        foreach ($practica->valoresCampos as $valorCampo) {
            $campos[] = [
                'campo' => $valorCampo->campo->name,
                'valor' => $valorCampo->valor,
            ];
        }

        $integrante2Campo = collect($campos)->firstWhere('campo', 'id_integrante_2');
        $directorCampo = collect($campos)->firstWhere('campo', 'director_id');
        $evaluadorCampo = collect($campos)->firstWhere('campo', 'evaluador_id');
        $codirectorCampo = collect($campos)->firstWhere('campo', 'codirector_id');

        $fdc127Campo = collect($campos)
            ->firstWhere('campo', 'doc_fdc127');

        $integrante2 = !empty($integrante2Campo['valor'])
            ? User::with('tipo_documento')->find($integrante2Campo['valor'])
            : null;

        $director = !empty($directorCampo['valor'])
            ? User::find($directorCampo['valor'])
            : null;

        $evaluador = !empty($evaluadorCampo['valor'])
            ? User::find($evaluadorCampo['valor'])
            : null;

        $codirector = !empty($codirectorCampo['valor'])
            ? User::find($codirectorCampo['valor'])
            : null;

        $dataBase = [
            'tipo_correo' => 'respuesta_fase_4',

            'cuerpo_correo' => [
                'remitente' => 'evaluador',
                'destinatario' => 'estudiante',

                'estado' => $respuesta->estado,
                'respuesta' => $respuesta->respuesta,

                'correo' => $practica->user->email,
                'estudiante' => $practica->user,
                'celular' => $practica->user->nro_celular ?? '',

                'integrante_2' => $integrante2,
                'integrante_2_correo' => $integrante2->email ?? null,
                'integrante_2_documento' => $integrante2
                    ? (($integrante2->tipo_documento->tag ?? '') . ' ' . ($integrante2->nro_documento ?? ''))
                    : null,
                'integrante_2_celular' => $integrante2->nro_celular ?? null,

                'director' => $director,
                'evaluador' => $evaluador,
                'codirector' => $codirector,

                'campos' => $campos,
                ],

                'adjuntos' => [
                    $fdc127Campo['valor'] ?? null,
                ],

                'adjuntar_archivos' => in_array(
                    $respuesta->estado ?? '',
                    ['Rechazada', 'Aplazada'],
                    true
                ),
                ];

                $dataBase['adjuntos'] = array_filter(
                    $dataBase['adjuntos']
                );

        // Correo a estudiantes
        $destinatariosEstudiantes = [
            $practica->user->email,
        ];

        if (!empty($integrante2?->email)) {
            $destinatariosEstudiantes[] = $integrante2->email;
        }

        $destinatariosEstudiantes = array_unique(array_filter($destinatariosEstudiantes));

        Mail::to($destinatariosEstudiantes)
            ->queue(new PracticasMail($dataBase));

        // Si el evaluador aplaza o rechaza, notificar también al director
        if (
            in_array(
                $respuesta->estado ?? '',
                ['Rechazada', 'Aplazada'],
                true
            )
            && !empty($director?->email)
        ) {
            $dataDirector = $dataBase;

            $dataDirector['cuerpo_correo']['destinatario'] =
                'director';

            Mail::to($director->email)
                ->queue(new PracticasMail($dataDirector));
        }

        // Si aprueba, también notifica al comité
        if (($respuesta->estado ?? '') === 'Aprobada') {

            $dataComite = $dataBase;
            $dataComite['cuerpo_correo']['destinatario'] = 'comite';

            Mail::to(config('mail.correo_sistemas'))
                ->queue(new PracticasMail($dataComite));
        }
    }

    public function sendRespuestaFase4Comite($practica, $respuesta)
    {
        $practica->load([
            'user.tipo_documento',
            'valoresCampos.campo'
        ]);

        $campos = [];

        foreach ($practica->valoresCampos as $valorCampo) {
            $campos[] = [
                'campo' => $valorCampo->campo->name,
                'valor' => $valorCampo->valor,
            ];
        }

        $integrante2Campo = collect($campos)->firstWhere('campo', 'id_integrante_2');
        $directorCampo = collect($campos)->firstWhere('campo', 'director_id');
        $evaluadorCampo = collect($campos)->firstWhere('campo', 'evaluador_id');
        $codirectorCampo = collect($campos)->firstWhere('campo', 'codirector_id');

        $integrante2 = !empty($integrante2Campo['valor'])
            ? User::with('tipo_documento')->find($integrante2Campo['valor'])
            : null;

        $director = !empty($directorCampo['valor'])
            ? User::find($directorCampo['valor'])
            : null;

        $evaluador = !empty($evaluadorCampo['valor'])
            ? User::find($evaluadorCampo['valor'])
            : null;

        $codirector = !empty($codirectorCampo['valor'])
            ? User::find($codirectorCampo['valor'])
            : null;

        $data = [
            'tipo_correo' => 'respuesta_fase_4',

            'cuerpo_correo' => [
                'remitente' => 'comite',
                'destinatario' => 'todos',

                'estado' => $respuesta->estado,
                'respuesta' => $respuesta->respuesta,
                'nro_acta' => $respuesta->nro_acta ?? null,
                'fecha_acta' => $respuesta->fecha_acta ?? null,
                'titulo_propuesta' => $respuesta->titulo_propuesta ?? null,

                'correo' => $practica->user->email,
                'estudiante' => $practica->user,
                'celular' => $practica->user->nro_celular ?? '',

                'integrante_2' => $integrante2,
                'integrante_2_correo' => $integrante2->email ?? null,
                'integrante_2_documento' => $integrante2
                    ? (($integrante2->tipo_documento->tag ?? '') . ' ' . ($integrante2->nro_documento ?? ''))
                    : null,
                'integrante_2_celular' => $integrante2->nro_celular ?? null,

                'director' => $director,
                'evaluador' => $evaluador,
                'codirector' => $codirector,

                'campos' => $campos,
            ],
            'adjuntar_archivos' => false,
        ];

        $destinatarios = [
            $practica->user->email,
        ];

        if (!empty($integrante2?->email)) {
            $destinatarios[] = $integrante2->email;
        }

        if (!empty($director?->email)) {
            $destinatarios[] = $director->email;
        }

        if (!empty($evaluador?->email)) {
            $destinatarios[] = $evaluador->email;
        }

        if (($respuesta->estado ?? '') === 'Aprobada' && !empty($codirector?->email)) {
            $destinatarios[] = $codirector->email;
        }

        $destinatarios = array_unique(array_filter($destinatarios));

        Mail::to($destinatarios)
            ->queue(new PracticasMail($data));
    }

    // ================= ENVIO Y RESPUESTA FASE 5 =================    

    public function sendFase5($practica)
    {
        $practica->load(['user.tipo_documento', 'valoresCampos.campo']);

        $user = $practica->user;

        $campos = [];

        foreach ($practica->valoresCampos as $valorCampo) {
            $campos[] = [
                'campo' => $valorCampo->campo->name,
                'valor' => $valorCampo->valor,
            ];
        }

        $fdc128 = collect($campos)->firstWhere('campo', 'doc_fdc128');
        $fdc129 = collect($campos)->firstWhere('campo', 'doc_fdc129');
        $fdc196 = collect($campos)->firstWhere('campo', 'doc_fdc196');

        $integrante2Campo = collect($campos)->firstWhere('campo', 'id_integrante_2');
        $directorCampo = collect($campos)->firstWhere('campo', 'director_id');

        $integrante2 = !empty($integrante2Campo['valor'])
            ? User::with('tipo_documento')->find($integrante2Campo['valor'])
            : null;

        $director = !empty($directorCampo['valor'])
            ? User::find($directorCampo['valor'])
            : null;

        $data = [
            'tipo_correo' => 'practicas_fase_5',

            'cuerpo_correo' => [
                'estado' => $practica->estado,

                'correo' => $user->email,
                'estudiante' => $user,
                'celular' => $user->nro_celular ?? '',

                'integrante_2' => $integrante2,
                'integrante_2_correo' => $integrante2->email ?? null,
                'integrante_2_documento' => $integrante2
                    ? (($integrante2->tipo_documento->tag ?? '') . ' ' . ($integrante2->nro_documento ?? ''))
                    : null,
                'integrante_2_celular' => $integrante2->nro_celular ?? null,

                'director' => $director,
                'director_correo' => $director->email ?? null,

                'campos' => $campos,
            ],

            'adjuntos' => [
                $fdc128['valor'] ?? null,
                $fdc129['valor'] ?? null,
                $fdc196['valor'] ?? null,
            ],
            'adjuntar_archivos' => false,
        ];

        $data['adjuntos'] = array_filter($data['adjuntos']);

        $destinatarios = [
            $user->email,
        ];

        if (!empty($integrante2?->email)) {
            $destinatarios[] = $integrante2->email;
        }

        if (!empty($director?->email)) {
            $destinatarios[] = $director->email;
        }

        $destinatarios = array_unique(array_filter($destinatarios));

        Mail::to($destinatarios)
            ->queue(new PracticasMail($data));
    }

    public function sendRespuestaFase5($practica, $respuesta)
    {
        $practica->load(['user.tipo_documento', 'valoresCampos.campo']);
        $campos = [];

        foreach ($practica->valoresCampos as $valorCampo) {
            $campos[] = [
                'campo' => $valorCampo->campo->name,
                'valor' => $valorCampo->valor,
            ];
        }

        $integrante2Campo = collect($campos)->firstWhere('campo', 'id_integrante_2');
        $directorCampo = collect($campos)->firstWhere('campo', 'director_id');
        $evaluadorCampo = collect($campos)->firstWhere('campo', 'evaluador_id');

        $integrante2 = !empty($integrante2Campo['valor'])
            ? User::with('tipo_documento')->find($integrante2Campo['valor'])
            : null;

        $director = !empty($directorCampo['valor'])
            ? User::find($directorCampo['valor'])
            : null;

        $evaluador = !empty($evaluadorCampo['valor'])
            ? User::find($evaluadorCampo['valor'])
            : null;

        $dataBase = [
            'tipo_correo' => 'respuesta_fase_5',

            'cuerpo_correo' => [
                'estado' => $respuesta->estado,
                'respuesta' => $respuesta->respuesta,

                'correo' => $practica->user->email,
                'estudiante' => $practica->user,
                'celular' => $practica->user->nro_celular ?? '',

                'integrante_2' => $integrante2,
                'integrante_2_correo' => $integrante2->email ?? null,
                'integrante_2_documento' => $integrante2
                    ? (($integrante2->tipo_documento->tag ?? '') . ' ' . ($integrante2->nro_documento ?? ''))
                    : null,
                'integrante_2_celular' => $integrante2->nro_celular ?? null,

                'director' => $director,
                'evaluador' => $evaluador,

                'campos' => $campos,
            ],
            'adjuntar_archivos' => false,
        ];

        $destinatariosEstudiantes = [
            $practica->user->email,
        ];

        if (!empty($integrante2?->email)) {
            $destinatariosEstudiantes[] = $integrante2->email;
        }

        $destinatariosEstudiantes = array_unique(array_filter($destinatariosEstudiantes));
        $dataEstudiantes = $dataBase;
        $dataEstudiantes['cuerpo_correo']['destinatario'] = 'estudiante';

        Mail::to($destinatariosEstudiantes)
            ->queue(new PracticasMail($dataEstudiantes));

        if (($respuesta->estado ?? '') === 'Aprobada' && !empty($evaluador?->email)) {
            $dataEvaluador = $dataBase;
            $dataEvaluador['cuerpo_correo']['destinatario'] = 'evaluador';

            Mail::to($evaluador->email)
                ->queue(new PracticasMail($dataEvaluador));
        }
    }

    // ================= RESPUESTA EVALUADOR FASE 6 =================
    public function sendRespuestaFase6Evaluador($practica, $respuesta)
    {
        $practica->load([
            'user.tipo_documento',
            'valoresCampos.campo'
        ]);

        $campos = [];

        foreach ($practica->valoresCampos as $valorCampo) {
            $campos[] = [
                'campo' => $valorCampo->campo->name,
                'valor' => $valorCampo->valor,
            ];
        }

        $integrante2Campo = collect($campos)->firstWhere('campo', 'id_integrante_2');
        $directorCampo = collect($campos)->firstWhere('campo', 'director_id');
        $evaluadorCampo = collect($campos)->firstWhere('campo', 'evaluador_id');
        $codirectorCampo = collect($campos)->firstWhere('campo', 'codirector_id');

        $integrante2 = !empty($integrante2Campo['valor'])
            ? User::with('tipo_documento')->find($integrante2Campo['valor'])
            : null;

        $director = !empty($directorCampo['valor'])
            ? User::find($directorCampo['valor'])
            : null;

        $evaluador = !empty($evaluadorCampo['valor'])
            ? User::find($evaluadorCampo['valor'])
            : null;

        $codirector = !empty($codirectorCampo['valor'])
            ? User::find($codirectorCampo['valor'])
            : null;

        $dataBase = [
            'tipo_correo' => 'respuesta_fase_6',

            'cuerpo_correo' => [
                'remitente' => 'evaluador',
                'destinatario' => 'estudiante',

                'estado' => $respuesta->estado,
                'respuesta' => $respuesta->respuesta,

                'correo' => $practica->user->email,
                'estudiante' => $practica->user,
                'celular' => $practica->user->nro_celular ?? '',

                'integrante_2' => $integrante2,
                'integrante_2_correo' => $integrante2->email ?? null,
                'integrante_2_documento' => $integrante2
                    ? (($integrante2->tipo_documento->tag ?? '') . ' ' . ($integrante2->nro_documento ?? ''))
                    : null,
                'integrante_2_celular' => $integrante2->nro_celular ?? null,

                'director' => $director,
                'evaluador' => $evaluador,
                'codirector' => $codirector,

                'campos' => $campos,
            ],
            'adjuntar_archivos' => false,
        ];

        $destinatariosEstudiantes = [
            $practica->user->email,
        ];

        if (!empty($integrante2?->email)) {
            $destinatariosEstudiantes[] = $integrante2->email;
        }

        $destinatariosEstudiantes = array_unique(array_filter($destinatariosEstudiantes));

        Mail::to($destinatariosEstudiantes)
            ->queue(new PracticasMail($dataBase));

        if (($respuesta->estado ?? '') === 'Aprobada') {
            $dataComite = $dataBase;
            $dataComite['cuerpo_correo']['destinatario'] = 'comite';

            $correoComite = config('mail.correo_sistemas');

            if (!empty($correoComite)) {
                Mail::to($correoComite)
                    ->queue(new PracticasMail($dataComite));
            }
        }
    }

    // ================= RESPUESTA COMITÉ FASE 6 =================
    public function sendRespuestaFase6Comite($practica, $respuesta)
    {
        $practica->load([
            'user.tipo_documento',
            'valoresCampos.campo'
        ]);

        $campos = [];

        foreach ($practica->valoresCampos as $valorCampo) {
            $campos[] = [
                'campo' => $valorCampo->campo->name,
                'valor' => $valorCampo->valor,
            ];
        }

        $integrante2Campo = collect($campos)->firstWhere('campo', 'id_integrante_2');
        $directorCampo = collect($campos)->firstWhere('campo', 'director_id');
        $evaluadorCampo = collect($campos)->firstWhere('campo', 'evaluador_id');
        $codirectorCampo = collect($campos)->firstWhere('campo', 'codirector_id');

        $integrante2 = !empty($integrante2Campo['valor'])
            ? User::with('tipo_documento')->find($integrante2Campo['valor'])
            : null;

        $director = !empty($directorCampo['valor'])
            ? User::find($directorCampo['valor'])
            : null;

        $evaluador = !empty($evaluadorCampo['valor'])
            ? User::find($evaluadorCampo['valor'])
            : null;

        $codirector = !empty($codirectorCampo['valor'])
            ? User::find($codirectorCampo['valor'])
            : null;

        $data = [
            'tipo_correo' => 'respuesta_fase_6',

            'cuerpo_correo' => [
                'remitente' => 'comite',
                'destinatario' => 'todos',

                'estado' => $respuesta->estado,
                'respuesta' => $respuesta->respuesta,
                'nro_acta' => $respuesta->nro_acta ?? null,
                'fecha_acta' => $respuesta->fecha_acta ?? null,

                'correo' => $practica->user->email,
                'estudiante' => $practica->user,
                'celular' => $practica->user->nro_celular ?? '',

                'integrante_2' => $integrante2,
                'integrante_2_correo' => $integrante2->email ?? null,
                'integrante_2_documento' => $integrante2
                    ? (($integrante2->tipo_documento->tag ?? '') . ' ' . ($integrante2->nro_documento ?? ''))
                    : null,
                'integrante_2_celular' => $integrante2->nro_celular ?? null,

                'director' => $director,
                'evaluador' => $evaluador,
                'codirector' => $codirector,

                'campos' => $campos,
            ],
            'adjuntar_archivos' => false,
        ];

        $destinatarios = [
            $practica->user->email,
        ];

        if (!empty($integrante2?->email)) {
            $destinatarios[] = $integrante2->email;
        }

        if (!empty($director?->email)) {
            $destinatarios[] = $director->email;
        }

        if (!empty($evaluador?->email)) {
            $destinatarios[] = $evaluador->email;
        }

        if (!empty($codirector?->email)) {
            $destinatarios[] = $codirector->email;
        }

        $destinatarios = array_unique(array_filter($destinatarios));

        Mail::to($destinatarios)
            ->queue(new PracticasMail($data));
    }


    public function sendEstadoHabilitacion($practica, string $tipoCorreo): void
    {
        try {
            $user = $practica->user;
            $campos = $practica->camposConValores();

            $integrante2 = null;
            $integrante2Correo = null;

            foreach ($campos as $campo) {
                if (($campo['campo'] ?? null) === 'id_integrante_2' && !empty($campo['valor'])) {
                    $integrante2 = User::with('tipo_documento')->find($campo['valor']);
                    $integrante2Correo = $integrante2->email ?? null;
                    break;
                }
            }

            $data = [
                'tipo_correo' => $tipoCorreo,

                'cuerpo_correo' => [
                    'estado' => $practica->estado,
                    'estudiante' => $user,
                    'integrante_2' => $integrante2,
                    'campos' => $campos,
                ],
            ];

            $destinatarios = [
                $user->email,
            ];

            if (!empty($integrante2Correo)) {
                $destinatarios[] = $integrante2Correo;
            }

            $destinatarios = array_unique(array_filter($destinatarios));

            Mail::to($destinatarios)
                ->queue(new PracticasMail($data));

        } catch (\Throwable $e) {
            Log::error('Error enviando correo de habilitación/deshabilitación de práctica: ' . $e->getMessage());
        }
    }


}