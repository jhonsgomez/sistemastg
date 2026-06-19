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

        .comentarios-quill blockquote {
            border-left: 4px solid #d1d5db;
            margin: 10px 0;
            padding-left: 12px;
            color: #4b5563;
        }

        .comentarios-quill pre {
            background: #f3f4f6;
            padding: 10px;
            border-radius: 6px;
            overflow-x: auto;
        }

        .comentarios-quill code {
            background: #f3f4f6;
            padding: 2px 4px;
            border-radius: 4px;
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

        .comentarios-quill .ql-indent-1 {
            padding-left: 3em;
        }

        .comentarios-quill .ql-indent-2 {
            padding-left: 6em;
        }

        .comentarios-quill .ql-indent-3 {
            padding-left: 9em;
        }
    </style>
    
</head>

<body>

    @php
        $cuerpo = $data['cuerpo_correo'];
        $destinatario = $cuerpo['destinatario'] ?? 'estudiante';
        $estado = $cuerpo['estado'] ?? '';
    @endphp

    <p>Buen día,</p>

    @if ($destinatario === 'evaluador')
        <p>
            Estimado docente, en este correo se le informa que el director ha aprobado los documentos
            correspondientes a la <strong>FASE 5</strong> de prácticas empresariales y ha enviado la solicitud
            para su revisión como <strong class="uppercase">EVALUADOR</strong> en la <strong>FASE 6</strong>.
        </p>
    @else
        <p>
            Estimado estudiante, en este correo se le informa la respuesta del director a los documentos
            correspondientes a la <strong>FASE 5</strong> de prácticas empresariales
            @if ($estado === 'Aprobada')
                ha sido <strong>APROBADA</strong>.
            @elseif ($estado === 'Aplazada' || $estado === 'Aplazado')
                ha sido <strong>APLAZADA</strong>.
            @else
                ha sido <strong>RECHAZADA</strong>.
            @endif
        </p>
    @endif

    <ul>
        <li><strong>Estado:</strong> {{ $estado }}</li>
        <li><strong>Fecha y hora:</strong> {{ now()->format('d/m/Y H:i:s') }}</li>
    </ul>

    <p><strong>Integrantes:</strong></p>

    <ul>
        <li><strong>Nombre:</strong> {{ $cuerpo['estudiante']->name ?? '' }}</li>

        <li>
            <strong>Documento:</strong>
            {{ optional($cuerpo['estudiante']->tipo_documento)->tag }}
            {{ $cuerpo['estudiante']->nro_documento ?? '' }}
        </li>

        <li>
            <strong>Correo:</strong>
            <a href="mailto:{{ $cuerpo['correo'] ?? '' }}" class="email">
                {{ $cuerpo['correo'] ?? '' }}
            </a>
        </li>

        <li><strong>Celular:</strong> {{ $cuerpo['celular'] ?? '' }}</li>

        @if (!empty($cuerpo['integrante_2']))
            <br>

            <li><strong>Nombre:</strong> {{ $cuerpo['integrante_2']->name ?? '' }}</li>

            <li>
                <strong>Documento:</strong>
                {{ $cuerpo['integrante_2_documento'] ?? '' }}
            </li>

            <li>
                <strong>Correo:</strong>
                <a href="mailto:{{ $cuerpo['integrante_2_correo'] ?? '' }}" class="email">
                    {{ $cuerpo['integrante_2_correo'] ?? '' }}
                </a>
            </li>

            <li>
                <strong>Celular:</strong>
                {{ $cuerpo['integrante_2_celular'] ?? '' }}
            </li>
        @endif
    </ul>

    @if (!empty($cuerpo['respuesta']))
        <p><strong>Respuesta del director:</strong></p>

        <div class="comentarios-quill">
            {!! $cuerpo['respuesta'] !!}
        </div>
    @endif

    @if ($estado === 'Aprobada')

        @if ($destinatario === 'evaluador')
            <p>
                <strong>NOTA:</strong> El evaluador será el encargado de revisar y validar los documentos
                correspondientes al informe final de prácticas empresariales para continuar con el proceso.
            </p>

            @if (!empty($cuerpo['director']))
                <p>
                    <strong>Director asignado:</strong>
                    {{ $cuerpo['director']->name ?? '' }}
                    -
                    <a href="mailto:{{ $cuerpo['director']->email ?? '' }}" class="email">
                        {{ $cuerpo['director']->email ?? '' }}
                    </a>
                </p>
            @endif
        @else
            <p>
                Los documentos correspondientes a la <strong>FASE 5</strong> han sido aprobados por el director.
                El evaluador será el encargado de continuar con la revisión en la <strong>FASE 6</strong>
                del proceso de prácticas empresariales.
            </p>

            <p>
                La práctica ha sido habilitada para continuar con la <strong>FASE 6</strong> del proceso.
            </p>
        @endif

    @else

        <p>
            Debe revisar las observaciones realizadas por el director, realizar las correcciones correspondientes
            al informe final y efectuar nuevamente el envío de los documentos requeridos.
        </p>

    @endif

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