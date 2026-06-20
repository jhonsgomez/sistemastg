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

@php
    $cuerpo = $data['cuerpo_correo'];

    $tipoSolicitud = $cuerpo['tipo_solicitud'] ?? '';
    $comentarios = $cuerpo['comentarios'] ?? '';
    $estudiante = $cuerpo['estudiante'] ?? null;
    $integrante2 = $cuerpo['integrante_2'] ?? null;
    $nroActa = $cuerpo['nro_acta'] ?? '';
    $fechaActa = $cuerpo['fecha_acta'] ?? '';
    $nuevaFechaLimite = $cuerpo['nueva_fecha_limite'] ?? null;
    $nuevoDirector = $cuerpo['nuevo_director'] ?? null;
    $nuevoEvaluador = $cuerpo['nuevo_evaluador'] ?? null;
    $estudianteRetirado = $cuerpo['estudiante_retirado'] ?? null;
@endphp

<p>Buen día,</p>

<p>
    Se informa que el comité ha dado respuesta a la solicitud de ajuste de práctica empresarial.
</p>

<ul>
    <li>
        <strong>Tipo de solicitud:</strong>
        <span class="uppercase">{{ str_replace('_', ' ', $tipoSolicitud) }}</span>
    </li>

    <li><strong>Número de acta:</strong> {{ $nroActa }}</li>
    <li><strong>Fecha de acta:</strong> {{ $fechaActa }}</li>
</ul>

@if($nuevaFechaLimite)
    <p>
        <strong>Nueva fecha límite de la práctica:</strong>
        {{ $nuevaFechaLimite }}
    </p>
@endif

@if($nuevoDirector)
    <p><strong>Nuevo director asignado:</strong></p>
    <ul>
        <li>{{ $nuevoDirector->name }}</li>
        <li>{{ $nuevoDirector->email }}</li>
    </ul>
@endif

@if($nuevoEvaluador)
    <p><strong>Nuevo evaluador asignado:</strong></p>
    <ul>
        <li>{{ $nuevoEvaluador->name }}</li>
        <li>{{ $nuevoEvaluador->email }}</li>
    </ul>
@endif

@if($estudianteRetirado)
    <p><strong>Estudiante retirado:</strong></p>
    <ul>
        <li>{{ $estudianteRetirado->name }}</li>
        <li>{{ $estudianteRetirado->email }}</li>
    </ul>
@endif

@if($estudiante)
    <p><strong>Estudiante principal:</strong></p>
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
    <p><strong>Comentarios del comité:</strong></p>

    <div class="comentarios-quill">
        {!! $comentarios !!}
    </div>
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