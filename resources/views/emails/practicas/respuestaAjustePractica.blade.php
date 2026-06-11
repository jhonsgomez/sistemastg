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
    <p>{!! $comentarios !!}</p>
@endif

<br>

<p>
    Esto es un correo generado automáticamente por el sistema de prácticas empresariales,
    favor no responder al mismo.
</p>

<br>

<p>Atentamente,</p>

<p>
    Software - Prácticas Empresariales<br>
    Programa de Tecnología en Desarrollo de Sistemas Informáticos e Ingeniería de Sistemas<br>
    Unidades Tecnológicas de Santander
</p>

</body>
</html>