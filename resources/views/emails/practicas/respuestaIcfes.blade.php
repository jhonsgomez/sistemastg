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
    <p>{!! $respuesta !!}</p>
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