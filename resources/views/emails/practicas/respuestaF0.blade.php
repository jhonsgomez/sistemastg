<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <style>

        body {
            font-family: Calibri, sans-serif;
        }

        .container {
            padding: 20px;
        }

        .footer {
            margin-top: 30px;
        }

        .uppercase {
            text-transform: uppercase;
        }
        .email {
            color: blue;
            text-decoration: underline;
        }
        .comentarios-quill {
            font-family: Calibri, sans-serif;
            color: #1f2937;
            font-size: 15px;
            line-height: 1.5;
            margin-top: 5px;
        }

        .comentarios-quill p {
            margin: 0 0 8px 0;
        }

        .comentarios-quill h1 {
            font-size: 22px;
            font-weight: bold;
            margin: 10px 0;
        }

        .comentarios-quill h2 {
            font-size: 18px;
            font-weight: bold;
            margin: 8px 0;
        }

        .comentarios-quill ul,
        .comentarios-quill ol {
            margin: 8px 0 8px 25px;
            padding-left: 18px;
        }

        .comentarios-quill li {
            margin-bottom: 5px;
        }

        .comentarios-quill strong {
            font-weight: bold;
        }

        .comentarios-quill em {
            font-style: italic;
        }

        .comentarios-quill u {
            text-decoration: underline;
        }

        .comentarios-quill img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 12px 0;
            border-radius: 6px;
        }

        .comentarios-quill .ql-align-center {
            text-align: center;
        }

        .comentarios-quill .ql-align-right {
            text-align: right;
        }

        .comentarios-quill .ql-align-justify {
            text-align: justify;
        }

        .comentarios-quill a {
            color: blue;
            text-decoration: underline;
        }

        .comentarios-quill .ql-size-small {
            font-size: 12px;
        }

        .comentarios-quill .ql-size-large {
            font-size: 18px;
        }

        .comentarios-quill .ql-size-huge {
            font-size: 24px;
        }
       
    </style>

</head>
<body>
    <div class="container">

        <p>Buen día,</p>

        @php
            $estado = $data['cuerpo_correo']['estado'] ?? '';
            $nuevoEstado = $data['cuerpo_correo']['nuevo_estado'] ?? '';
        @endphp

        <p>
            Estimado usuario, en este correo se le informa que su solicitud para iniciar prácticas
            @if($estado === 'Aprobada')
                ha pasado a <strong>{{ strtoupper($nuevoEstado) }}</strong>
            @elseif($estado === 'Aplazada')
                ha sido <strong>APLAZADA</strong>
            @elseif($estado === 'Rechazada')
                ha sido <strong>RECHAZADA</strong>
            @endif
            :
        </p>

     

        <ul>
            <li><strong>Tipo de solicitud:</strong> SOLICITUD PRACTICAS EMPRESARIALES
                {{ $data['cuerpo_correo']['periodo'] ?? '' }}</li>
            <li><strong>Nivel académico: </strong>{{ $data['cuerpo_correo']['nivel'] ?? '' }}</li>
            <li><strong>Estado:</strong> <strong>{{ strtoupper($estado) }}</strong></li>
        </ul>
        <br>
        <p>Integrantes:</p>
        <ul>
            <li><strong>Nombre:</strong> {{ $data['cuerpo_correo']['estudiante']->name ?? '' }}</li>

            <li>
                <strong>Documento:</strong>
                {{ $data['cuerpo_correo']['estudiante']->tipo_documento->tag ?? '' }}
                {{ $data['cuerpo_correo']['estudiante']->nro_documento ?? '' }}
            </li>
            <li>
                <strong>Correo:</strong>
                <a href="mailto:{{ $data['cuerpo_correo']['correo'] ?? '' }}" class="email">
                    {{ $data['cuerpo_correo']['correo'] ?? '' }}
                </a>
            </li>
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
                <li>
                    <strong>Correo:</strong>
                    <a href="mailto:{{ $data['cuerpo_correo']['integrante_2_correo'] ?? '' }}" class="email">
                        {{ $data['cuerpo_correo']['integrante_2_correo'] ?? '' }}
                    </a>
                </li>
                <li><strong>Celular:</strong> {{ $data['cuerpo_correo']['integrante_2_celular'] ?? '' }}</li>
            @endif
        </ul>

        <p><strong>Fecha y hora de envío: </strong>{{ now()->format('d/m/Y H:i:s') }}</p>

        @if ($data['cuerpo_correo']['estado'] === 'Aprobada')
            <p>Se le recomienda ingresar al sistema para continuar con las siguientes fases del proyecto en curso.</p>
        @else
            <p>Se le recomienda ingresar al sistema para volver a realizar su solicitud.</p>
        @endif

        @if (!empty($data['comentarios']))
            <p><strong>Comentarios:</strong></p>

            <div class="comentarios-quill">
                {!! $data['comentarios'] !!}
            </div>
        @endif

        <div class="footer">
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

        </div>

    </div>
</body>
</html>