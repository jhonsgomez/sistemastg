<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: Calibri, sans-serif;
        }
    </style>
</head>

<body>

    <p>Buen día,</p>

    <p>
        Estimado usuario, en este correo se le informa el envío de la
        solicitud de prácticas empresariales en FASE 1.
    </p>

    <ul>

        <li>
            <strong>Estado:</strong>
            {{ $data['cuerpo_correo']['estado'] ?? '' }}
        </li>

        <li>
            <strong>¿Es práctica institucional?:</strong>
            {{ $data['cuerpo_correo']['practica_institucional'] ?? 'No' }}
        </li>

        <li>
            <strong>Empresa:</strong>
            {{ $data['cuerpo_correo']['empresa'] ?? 'No registra' }}
        </li>

    </ul>

    <br>

    <p><strong>Integrantes:</strong></p>

    <ul>

        <li>
            <strong>Nombre:</strong>
            {{ $data['cuerpo_correo']['estudiante']->name ?? '' }}
        </li>

        <li>
            <strong>Documento:</strong>

            {{ $data['cuerpo_correo']['estudiante']->tipo_documento->tag ?? '' }}

            {{ $data['cuerpo_correo']['estudiante']->nro_documento ?? '' }}
        </li>

        <li>
            <strong>Correo:</strong>
            {{ $data['cuerpo_correo']['correo'] ?? '' }}
        </li>

    </ul>

    <br>

    <p>
        El estudiante ha realizado el envío del formato FDC-126.
    </p>

    <p>
        <strong>Fecha y hora:</strong>
        {{ now()->format('d/m/Y H:i:s') }}
    </p>

    <br>

    <p>
        Este es un correo generado automáticamente por el sistema de prácticas,
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