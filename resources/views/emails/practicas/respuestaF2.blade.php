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
    <p>Estimado estudiante, en este correo se le informa la respuesta de su solicitud correspondiente a la<strong>FASE 2</strong> de prácticas empresariales.</p>

    <p>
        Su solicitud

        {!! ($data['cuerpo_correo']['estado'] ?? '') === 'Aprobada'
            ? 'ha sido <strong>APROBADA</strong>'
            : 'ha sido <strong>RECHAZADA</strong>' !!}
    </p>

    <ul>

        <li>
            <strong>Estado actual:</strong>
            {{ $data['cuerpo_correo']['estado'] ?? '' }}
        </li>

        <li>
            <strong>Fecha:</strong>
            {{ now()->format('d/m/Y H:i:s') }}
        </li>

    </ul>

    <br>

    <p><strong>Datos del estudiante:</strong></p>
    <ul>

        <li><strong>Nombre:</strong>{{ $data['cuerpo_correo']['estudiante']->name ?? '' }}</li>
        <li>
            <strong>Documento:</strong>
            {{ optional($data['cuerpo_correo']['estudiante']->tipo_documento)->tag }}
            {{ $data['cuerpo_correo']['estudiante']->nro_documento ?? '' }}
        </li>
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

    <p>
        <strong>Respuesta del comité:</strong>
    </p>

    <p>
        {{ $data['cuerpo_correo']['respuesta'] ?? '' }}
    </p>

    <br>

    @if (!empty($data['cuerpo_correo']['director']))
        <p>
            <strong>Director asignado:</strong>
            {{ $data['cuerpo_correo']['director'] }}
        </p>
    @endif

    @if (!empty($data['cuerpo_correo']['evaluador']))
        <p>
            <strong>Evaluador asignado:</strong>
            {{ $data['cuerpo_correo']['evaluador'] }}
        </p>
    @endif

    @if (!empty($data['cuerpo_correo']['codirector']))
        <p>
            <strong>Codirector asignado:</strong>
            {{ $data['cuerpo_correo']['codirector'] }}
        </p>
    @endif

    <br>

    @if (($data['cuerpo_correo']['estado'] ?? '') === 'Aprobada')

        <p>
            Puede continuar con la siguiente fase del proceso
            de prácticas empresariales.
        </p>

    @else

        <p>
            Debe revisar las observaciones realizadas por el comité
            y volver a realizar el proceso correspondiente.
        </p>

    @endif

    <br>

    <p>
        Este es un correo generado automáticamente
        por el sistema de prácticas,
        por favor no responder.
    </p>

    <br>

    <p>Atentamente,</p>

    <p>
        Comité de Trabajos de Grado<br>

        Programa de Tecnología en Desarrollo de Sistemas Informáticos e Ingeniería de Sistemas<br>

        Unidades Tecnológicas de Santander
    </p>

</body>

</html>
