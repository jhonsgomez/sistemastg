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
        Estimado usuario, en este correo se le informa que se ha realizado
        el envío de documentos correspondientes a la <strong>FASE 2</strong>
        de prácticas empresariales.
    </p>

    <ul>
        <li>
            <strong>Tipo de solicitud:</strong>
            PAGO DE LA MODALIDAD
        </li>

        <li>
            <strong>Estado actual:</strong>
            {{ $data['cuerpo_correo']['estado'] ?? '' }}
        </li>
    </ul>

    <br>

    <p><strong>Estudiante:</strong></p>
        <ul>
            <li><strong>Nombre:</strong> {{ $data['cuerpo_correo']['estudiante']->name ?? '' }}</li>

            <li>
                <strong>Documento:</strong>
                {{ $data['cuerpo_correo']['estudiante']->tipo_documento->tag ?? '' }}
                {{ $data['cuerpo_correo']['estudiante']->nro_documento ?? '' }}
            </li>
            <li><strong>Correo:</strong> {{ $data['cuerpo_correo']['correo'] ?? '' }}</li>
            <li><strong>Celular:</strong> {{ $data['cuerpo_correo']['celular'] ?? '' }}</li>
            <br>

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
        El estudiante ha adjuntado los siguientes documentos:
    </p>

    <ul>
        <li>Liquidación de pago</li>
        <li>Soporte de pago</li>
    </ul>

    <p>
        Fecha y hora de envío:
        {{ now()->format('d/m/Y H:i:s') }}
    </p>

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