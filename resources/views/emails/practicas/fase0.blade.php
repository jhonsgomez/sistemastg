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

        .footer {
            margin-top: 30px;
        }

        .uppercase {
            text-transform: uppercase;
        }

    </style>

</head>
<body>
    <div>

        <p>Buen día,</p>

        <p>Estimado usuario, en este correo se le informa que se ha realizado una nueva solicitud para iniciar la modalidad de trabajo de grado correspondiente a prácticas, con la siguiente información:</p>
        <ul>
            <li><strong>Tipo de solicitud:</strong> SOLICITUD PRACTICAS EMPRESARIALES
                {{ $data['cuerpo_correo']['periodo'] ?? '' }}</li>
            <li><strong>Nivel académico: </strong>{{ $data['cuerpo_correo']['nivel'] ?? '' }}</li>
            <li><strong>Estado: </strong>{{ $data['cuerpo_correo']['estado'] ?? '' }}</li>
        </ul>
        <br>
        <p>Integrantes del proyecto: </p>
        <ul>
            <li><strong>Nombre: </strong>{{ $data['cuerpo_correo']['estudiante']->name ?? '' }}</li>

            <li>
                <strong>Documento: </strong>
                {{ $data['cuerpo_correo']['estudiante']->tipo_documento->tag ?? '' }}
                {{ $data['cuerpo_correo']['estudiante']->nro_documento ?? '' }}
            </li>
            <li>
                <strong>Correo:</strong>
                <a href="mailto:{{ $data['cuerpo_correo']['correo'] ?? '' }}" class="email">
                    {{ $data['cuerpo_correo']['correo'] ?? '' }}
                </a>
            </li>
            <li><strong>Celular: </strong> {{ $data['cuerpo_correo']['celular'] ?? '' }}</li>
            <br>

            @if (!empty($data['cuerpo_correo']['integrante_2']))
                <li>
                    <strong>Nombre: </strong>

                    {{ is_object($data['cuerpo_correo']['integrante_2'])
                        ? $data['cuerpo_correo']['integrante_2']->name
                        : $data['cuerpo_correo']['integrante_2'] }}
                </li>

                <li><strong>Documento:</strong> {{ $data['cuerpo_correo']['integrante_2_documento'] ?? '' }}</li>

                <li>
                    <strong>Correo: </strong>
                    <a href="mailto:{{ $data['cuerpo_correo']['integrante_2_correo'] ?? '' }}" class="email">
                        {{ $data['cuerpo_correo']['integrante_2_correo'] ?? '' }}
                    </a>
                </li>

                <li><strong>Celular: </strong> {{ $data['cuerpo_correo']['integrante_2_celular'] ?? '' }}</li>
            @endif
        </ul>

            <p><strong>Fecha y hora de envío:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>


            @if (!empty($data['comentarios']))
                <p><strong>Comentarios:</strong>{{ strip_tags($data['comentarios']) }}</p>
            @endif

            <div class="footer">

                <br><p>Se verificará la información suministrada para dar accesso a las siguientes fases del proyecto.</p><br>
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
            </div>
    </div>
</body>
</html>