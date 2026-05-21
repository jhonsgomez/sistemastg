<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: Calibri, sans-serif;
        }

        .email {
            color: blue;
            text-decoration: underline;
        }

        .uppercase {
            text-transform: uppercase;
        }


    </style>
</head>

<body>

    <p>Buen día,</p>

    <p>
        Estimado usuario, en este correo se le informa que su solicitud
        de prácticas empresariales en <strong>FASE 1</strong> 
        ha sido:

        <strong>
            {{ $data['cuerpo_correo']['estado'] ?? '' }}
        </strong>
    </p>

    <br>

    <ul>

        <li>
            <strong>Estado actual:</strong>
            {{ $data['cuerpo_correo']['estado'] ?? '' }}
        </li>

        <li>
            <strong>Empresa:</strong>
            {{ $data['cuerpo_correo']['empresa'] ?? 'No registra' }}
        </li>

        <li>
            <strong>Práctica institucional:</strong>

            {{ $data['cuerpo_correo']['practica_institucional'] ?? 'No' }}
        </li>

        <li>
            <strong>Número de acta:</strong>
            {{ $data['cuerpo_correo']['nro_acta'] ?? '' }}
        </li>

        <li>
            <strong>Fecha de acta:</strong>
            {{ $data['cuerpo_correo']['fecha_acta'] ?? '' }}
        </li>

    </ul>

    <br>

    <p><strong>Integrantes:</strong></p>

    <ul>

        <li><strong>Nombre:</strong>{{ $data['cuerpo_correo']['estudiante']->name ?? '' }}</li>
        <li><strong>Documento:</strong>{{ $data['cuerpo_correo']['estudiante']->tipo_documento->tag ?? '' }}{{ $data['cuerpo_correo']['estudiante']->nro_documento ?? '' }}</li>
        <li><strong>Correo:</strong>{{ $data['cuerpo_correo']['correo'] ?? '' }}</li>

        @if (!empty($data['cuerpo_correo']['integrante_2']))
            <li>
                <strong>Nombre:</strong>
                {{ is_object($data['cuerpo_correo']['integrante_2'])
                    ? $data['cuerpo_correo']['integrante_2']->name
                    : $data['cuerpo_correo']['integrante_2'] }}
            </li>
            <li><strong>Documento:</strong> {{ $data['cuerpo_correo']['integrante_2_documento'] ?? '' }}</li>
            <li><strong>Correo:</strong> {{ $data['cuerpo_correo']['integrante_2_correo'] ?? '' }}</li>
            <li><strong>Celular:</strong> {{ $data['cuerpo_correo']['integrante_2_celular'] ?? '' }}</li>
        @endif

    </ul>

    <br>

    <p><strong>Respuesta del comité:</strong></p>
    <p>{{ $data['cuerpo_correo']['respuesta'] ?? '' }} </p>

    <br>

    @if (($data['cuerpo_correo']['estado'] ?? '') === 'Aprobada')
        <p> Su práctica ha avanzado correctamente a la<strong>FASE 2</strong>.</p>

        <p>Se recomienda ingresar al sistema para continuarel proceso correspondiente.</p>
    @else
        <p>
            La solicitud fue rechazada.
        </p>

        <p>Debe ingresar nuevamente al sistema,corregir la información solicitada y reenviar la FASE 1.</p>
    @endif

    <br>

    <p> Este es un correo generado automáticamente por el sistema de prácticas,por favor no responder.</p>

    <br>

    <p>Atentamente,</p>

    <p>
        Comité de Trabajos de Grado<br>
        Programa de Tecnología en Desarrollo de Sistemas Informáticos e Ingeniería de Sistemas<br>
        Unidades Tecnológicas de Santander
    </p>

</body>

</html>
