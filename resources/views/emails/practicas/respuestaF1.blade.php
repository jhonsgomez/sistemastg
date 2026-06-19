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

    <p>Buen día,</p>

    <p>
        Estimado usuario, en este correo se le informa que su solicitud
        de prácticas empresariales en <strong>FASE 1</strong> 
        ha sido:

    <strong>
        {{ strtoupper($data['cuerpo_correo']['estado'] ?? '') }}
    </strong>
    </p>

    <br>

    <ul>

        <li>
            <strong>Estado actual: </strong>
            {{ $data['cuerpo_correo']['estado'] ?? '' }}
        </li>

        <li>
            <strong>Empresa: </strong>
            {{ $data['cuerpo_correo']['empresa'] ?? 'No registra' }}
        </li>

        <li>
            <strong>Práctica institucional: </strong>

            {{ $data['cuerpo_correo']['practica_institucional'] ?? 'No' }}
        </li>

        <li>
            <strong>Número de acta: </strong>
            {{ $data['cuerpo_correo']['nro_acta'] ?? '' }}
        </li>

        <li>
            <strong>Fecha de acta: </strong>
            {{ $data['cuerpo_correo']['fecha_acta'] ?? '' }}
        </li>

    </ul>

    <br>

    <p><strong>Integrantes: </strong></p>

    <ul>

        <li><strong>Nombre: </strong>{{ $data['cuerpo_correo']['estudiante']->name ?? '' }}</li>
        <li><strong>Documento: </strong>{{ $data['cuerpo_correo']['estudiante']->tipo_documento->tag ?? '' }}{{ $data['cuerpo_correo']['estudiante']->nro_documento ?? '' }}</li>
        <li>
            <strong>Correo: </strong>
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
            <li><strong>Documento: </strong> {{ $data['cuerpo_correo']['integrante_2_documento'] ?? '' }}</li>
            <li>
                <strong>Correo: </strong>
                <a href="mailto:{{ $data['cuerpo_correo']['integrante_2_correo'] ?? '' }}" class="email">
                    {{ $data['cuerpo_correo']['integrante_2_correo'] ?? '' }}
                </a>
            </li>
            <li><strong>Celular: </strong> {{ $data['cuerpo_correo']['integrante_2_celular'] ?? '' }}</li>
        @endif

    </ul>

    <br>


    @if (!empty($data['cuerpo_correo']['respuesta_fase1']))
        <p><strong>Respuesta del comité:</strong></p>

        <div class="comentarios-quill">
            {!! $data['cuerpo_correo']['respuesta_fase1'] !!}
        </div>
    @endif

    <br>
    <p><strong>Fecha y hora de envío: </strong>{{ now()->format('d/m/Y H:i:s') }}</p>
    <br>

    @if (($data['cuerpo_correo']['estado'] ?? '') === 'Aprobada')
        <p> Su práctica ha avanzado correctamente a la<strong> FASE 2</strong>.</p>

        <p>Se recomienda ingresar al sistema para continuarel proceso correspondiente.</p>
    @else
        <p>
            La solicitud fue rechazada.
        </p>

        <p>Debe ingresar nuevamente al sistema,corregir la información solicitada y reenviar la FASE 1.</p>
    @endif


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
