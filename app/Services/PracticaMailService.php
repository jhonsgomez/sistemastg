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

    public function sendRespuesta(
        $practica,
        string $nuevoEstado,
        string $mensaje,
        string $estadoRespuesta
    ): void {

    $this->send(
        'respuesta_comite',
        $practica,
        [

            'comentarios' => $mensaje,

            'es_respuesta' => true,

            'estado' => $estadoRespuesta,

            'nuevo_estado' => $nuevoEstado,

            'mensaje' => $mensaje,
        ]
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


}