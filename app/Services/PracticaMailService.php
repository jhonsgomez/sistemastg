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

        private function send(string $tipoCorreo,$practica, array $extraData = []): void {
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

                Mail::to([$user->email])
                    ->queue(new PracticasMail($data));

            } catch (\Throwable $e) {

                dd(
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                );

            }
        }

    public function sendFase1($practica)
    {
        $practica->load([
            'user.tipo_documento',
            'valoresCampos.campo'
        ]);

        // Obtener valores dinámicos
        $campos = [];

        foreach ($practica->valoresCampos as $valorCampo){

            $campos[] = [
                'campo' => $valorCampo->campo->name,
                'valor' => $valorCampo->valor,
            ];
        }

        // Buscar datos específicos
        $empresa = collect($campos)
            ->firstWhere('campo', 'nombre_empresa');

        $practicaInstitucional = collect($campos)
            ->firstWhere('campo', 'es_institucional');

        $fdc126 = collect($campos)
            ->firstWhere('campo', 'doc_fdc126');

        // Data del correo
        $data = [

            'tipo_correo' => 'practicas_fase_1',

            'cuerpo_correo' => [

                'estado' => $practica->estado,

                'empresa' => $empresa['valor'] ?? null,

                'practica_institucional' =>
                    ($practicaInstitucional['valor'] ?? false) == '1'
                        ? 'Sí'
                        : 'No',

                'correo' => $practica->user->email,

                'estudiante' => $practica->user,
            ],

            // ADJUNTOS
            'adjuntos' => [
                $fdc126['valor'] ?? null
            ],
        ];

        // Limpiar adjuntos null
        $data['adjuntos'] = array_filter($data['adjuntos']);

        Mail::to(config('mail.from.address'))
            ->queue(new PracticasMail($data));
    }

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

                'campos' => $campos,
            ],

            // ADJUNTOS
            'adjuntos' => [

                $liquidacionPago['valor'] ?? null,

                $soportePago['valor'] ?? null,
            ],
        ];

        // Limpiar adjuntos null
        $data['adjuntos'] = array_filter(
            $data['adjuntos']
        );

        Mail::to(config('mail.from.address'))
            ->queue(new PracticasMail($data));
    }

    public function sendFase3($practica)
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

        // ================= DOCUMENTOS =================

        $arl = collect($campos)
            ->firstWhere('campo', 'arl');

        $fdc127 = collect($campos)
            ->firstWhere('campo', 'doc_fdc127');

        $fdc195 = collect($campos)
            ->firstWhere('campo', 'doc_fdc195');

        // ================= DATA CORREO =================

        $data = [

            'tipo_correo' => 'practicas_fase_3',

            'cuerpo_correo' => [

                'estado' => $practica->estado,

                'correo' => $practica->user->email,

                'estudiante' => $practica->user,

                'campos' => $campos,
            ],

            // ================= ADJUNTOS =================

            'adjuntos' => [

                $arl['valor'] ?? null,

                $fdc127['valor'] ?? null,

                $fdc195['valor'] ?? null,
            ],
        ];

        // Limpiar adjuntos null
        $data['adjuntos'] = array_filter(
            $data['adjuntos']
        );

        Mail::to(config('mail.from.address'))
            ->queue(new PracticasMail($data));
    }

   


    public function sendRespuestaFase1($practica,$request) {

        $practica->load([
            'user.tipo_documento',
            'valoresCampos.campo'
        ]);

        // Obtener campos dinámicos
        $campos = [];

        foreach ($practica->valoresCampos as $valorCampo) {

            $campos[] = [
                'campo' => $valorCampo->campo->name,
                'valor' => $valorCampo->valor,
            ];
        }

        // Buscar datos específicos
        $empresa = collect($campos)
            ->firstWhere('campo', 'nombre_empresa');

        $practicaInstitucional = collect($campos)
            ->firstWhere('campo', 'practica_institucional');

        // Construcción del correo
        $data = [

            'tipo_correo' => 'respuesta_fase_1',

            'cuerpo_correo' => [

                'estado' => $request->estado,

                'respuesta' => $request->respuesta,

                'nro_acta' => $request->nro_acta,

                'fecha_acta' => $request->fecha_acta,

                'empresa' =>
                    $empresa['valor'] ?? 'No registra',

                'practica_institucional' =>

                    ($practicaInstitucional['valor'] ?? false) == '1'
                        ? 'Sí'
                        : 'No',

                'correo' => $practica->user->email,

                'estudiante' => $practica->user,
            ],
        ];

        // Enviar correo
        Mail::to($practica->user->email)
            ->queue(new PracticasMail($data));
    }

    public function sendRespuestaFase2($practica, $respuesta)
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

        $data = [

            'tipo_correo' => 'respuesta_fase_2',

            'cuerpo_correo' => [
            
                'estado' => $respuesta['estado'],

                'respuesta' => $respuesta['respuesta'],

                'correo' => $practica->user->email,

                'estudiante' => $practica->user,

                
                'director' => $respuesta['director'] ?? null,

                'evaluador' => $respuesta['evaluador'] ?? null,

                'codirector' => $respuesta['codirector'] ?? null,

                'campos' => $campos,
            ],
        ];

        Mail::to($practica->user->email)
            ->queue(new PracticasMail($data));
    }
    
    public function sendRespuestaFase3($practica, $respuesta)
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

        $data = [

            'tipo_correo' => 'respuesta_fase_3',

            'cuerpo_correo' => [

                'estado' => $respuesta['estado'],

                'respuesta' => $respuesta['respuesta'],

                'correo' => $practica->user->email,

                'estudiante' => $practica->user,

                'director' => $respuesta['director'] ?? null,

                'evaluador' => $respuesta['evaluador'] ?? null,

                'codirector' => $respuesta['codirector'] ?? null,

                'campos' => $campos,
            ],
        ];

        Mail::to($practica->user->email)
            ->queue(new PracticasMail($data));
    }




}