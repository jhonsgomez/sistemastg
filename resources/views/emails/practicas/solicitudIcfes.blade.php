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
    $estudiante = $cuerpo['estudiante'] ?? null;
    $integrante2 = $cuerpo['integrante_2'] ?? null;
    $estado = $cuerpo['estado'] ?? 'No especificado';
@endphp

<p>Buen día,</p>

<p>
    Estimado estudiante, en este correo se confirma que la solicitud del beneficio
    por resultados de pruebas Saber TyT/Pro fue enviada correctamente.
</p>

<p>
    Estado actual de la práctica:
    <strong class="uppercase">{{ $estado }}</strong>
</p>

<p>
    La solicitud se encuentra pendiente de revisión por parte del comité.
</p>

@if ($estudiante)
    <p><strong>Estudiante que realizó la solicitud:</strong></p>
    <ul>
        <li><strong>Nombre:</strong> {{ $estudiante->name }}</li>
        <li><strong>Correo:</strong> <span class="email">{{ $estudiante->email }}</span></li>
        <li><strong>Documento:</strong> {{ $estudiante->tipo_documento->tag ?? '' }} {{ $estudiante->nro_documento ?? '' }}</li>
    </ul>
@endif

@if ($integrante2)
    <p><strong>Segundo integrante:</strong></p>
    <ul>
        <li><strong>Nombre:</strong> {{ $integrante2->name }}</li>
        <li><strong>Correo:</strong> <span class="email">{{ $integrante2->email }}</span></li>
        <li><strong>Documento:</strong> {{ $integrante2->tipo_documento->tag ?? '' }} {{ $integrante2->nro_documento ?? '' }}</li>
    </ul>
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