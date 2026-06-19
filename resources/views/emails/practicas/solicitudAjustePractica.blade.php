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

<p>
    {!! $comentarios !!}
</p>

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
    Software - Prácticas Empresariales<br>
    Programa de Tecnología en Desarrollo de Sistemas Informáticos e Ingeniería de Sistemas<br>
    Unidades Tecnológicas de Santander
</p>

</body>
</html>