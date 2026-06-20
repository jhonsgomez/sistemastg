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
    </style>
</head>

<body>

@php
    $cuerpo = $data['cuerpo_correo'];

    $tipoSolicitud = $cuerpo['tipo_solicitud'] ?? '';

    $tiposSolicitud = [
        'retiro' => 'Retiro de la práctica',
        'cambio_director' => 'Cambio de director',
        'cambio_evaluador' => 'Cambio de evaluador',
        'prorroga' => 'Prórroga',
    ];

    $tipoSolicitudTexto = $tiposSolicitud[$tipoSolicitud] ?? $tipoSolicitud;

    $comentarios =
        $cuerpo['comentarios'] ?? '';

    $estudiante =
        $cuerpo['estudiante'] ?? null;

    $integrante2 =
        $cuerpo['integrante_2'] ?? null;
@endphp

<p>Buen día,</p>

<p>
    Se ha recibido una nueva solicitud de ajuste de práctica empresarial.
</p>

<ul>
    <li>
        <strong>Tipo de solicitud:</strong>
        <span class="uppercase">
           {{ $tipoSolicitudTexto }}
        </span>
    </li>

    <li>
        <strong>Estado actual:</strong>
        {{ $cuerpo['estado'] ?? '' }}
    </li>
</ul>

@if($estudiante)

<p><strong>Estudiante:</strong></p>

<ul>
    <li>{{ $estudiante->name }}</li>
    <li>{{ $estudiante->email }}</li>
</ul>

@endif

@if($integrante2)

<p><strong>Segundo integrante:</strong></p>

<ul>
    <li>{{ $integrante2->name }}</li>
    <li>{{ $integrante2->email }}</li>
</ul>

@endif

@if(!empty($comentarios))
    <p><strong>Comentarios:</strong></p>

    <div class="comentarios-quill">
        {!! $comentarios !!}
    </div>
@endif

@endif

<br>

@if(!empty($data['adjuntos']))
    <p><strong>Documentos adjuntos:</strong></p>
    <ul>
        @foreach($data['adjuntos'] as $adjunto)
            <li>{{ basename($adjunto) }}</li>
        @endforeach
    </ul>
@endif
<br>

<p>
    La solicitud queda pendiente de revisión por parte del comité.
</p>

<br>

<p>
    Esto es un correo generado automáticamente por el sistema de prácticas empresariales,
    favor no responder al mismo.
</p>

<br>

<p>Atentamente,</p>

<p>
    Software - Trabajos de Grado<br>
    Programa de Tecnología en Desarrollo de Sistemas Informáticos e Ingeniería de Sistemas<br>
    Unidades Tecnológicas de Santander
</p>

</body>
</html>