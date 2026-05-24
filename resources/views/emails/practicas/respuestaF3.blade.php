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
        Estimado estudiante, en este correo se le informa la respuesta de su solicitud correspondiente a la
        <strong>FASE 3</strong> de prácticas empresariales
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

        <li>
            <strong>Nombre:</strong>
            {{ $data['cuerpo_correo']['estudiante']->name ?? '' }}
        </li>

        <li>
            <strong>Documento:</strong>
            {{ optional($data['cuerpo_correo']['estudiante']->tipo_documento)->tag }}
            {{ $data['cuerpo_correo']['estudiante']->nro_documento ?? '' }}
        </li>

        <li>
            <strong>Correo:</strong>
            {{ $data['cuerpo_correo']['correo'] ?? '' }}
        </li>

    </ul>

    <br>

    <p>
        <strong>Respuesta del comité:</strong>
    </p>

    <p>
        {{ $data['cuerpo_correo']['respuesta'] ?? '' }}
    </p>

    <br>

    @if (($data['cuerpo_correo']['estado'] ?? '') === 'Aprobada')

        <p>
            Los documentos correspondientes a la fase 3 han sido aprobados correctamente.
            Puede continuar con la siguiente fase del proceso de prácticas empresariales.
        </p>

    @else

        <p>
            Debe revisar las observaciones realizadas por el comité,
            corregir los documentos solicitados y realizar nuevamente el proceso correspondiente.
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