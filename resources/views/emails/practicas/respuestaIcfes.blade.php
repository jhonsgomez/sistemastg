<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: Calibri, sans-serif;
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

    $estudiante = $cuerpo['estudiante'] ?? null;
    $estado = $cuerpo['estado'] ?? '';
    $respuesta = $cuerpo['respuesta'] ?? '';
    $nroActa = $cuerpo['nro_acta'] ?? '';
    $fechaActa = $cuerpo['fecha_acta'] ?? '';
@endphp

<p>Buen día,</p>

<p>
    Estimado estudiante, en este correo se le informa la respuesta a su solicitud
    de beneficio por resultados de pruebas Saber TyT/Pro dentro del proceso de
    prácticas empresariales.
</p>

<p>
    <strong>Estado de la solicitud:</strong>
    <span class="uppercase">{{ $estado }}</span>
</p>

@if ($respuesta)
    <p><strong>Respuesta del comité:</strong></p>

    <div class="comentarios-quill">
        {!! $respuesta !!}
    </div>
@endif

<ul>
    <li><strong>Número de acta:</strong> {{ $nroActa }}</li>
    <li><strong>Fecha de acta:</strong> {{ $fechaActa }}</li>
</ul>

@if ($estudiante)
    <p><strong>Estudiante:</strong> {{ $estudiante->name }}</p>
@endif

<br>

<p>
    Esto es un correo generado automáticamente por el sistema de prácticas empresariales,
    favor no responder al mismo.
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