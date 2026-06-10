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
    </style>
</head>

<body>

@php
    $cuerpo = $data['cuerpo_correo'];

    $estudiante = $cuerpo['estudiante'] ?? null;
    $integrante2 = $cuerpo['integrante_2'] ?? null;

    $fechaLimite = $cuerpo['fecha_limite_practica'] ?? 'No especificada';
    $estado = $cuerpo['estado'] ?? 'No especificado';
@endphp

<p>Buen día,</p>

<p>
    Estimado estudiante, en este correo se le recuerda que su práctica empresarial
    se encuentra actualmente en estado
    <strong class="uppercase">{{ $estado }}</strong>.
</p>

<p>
    La fecha límite registrada para la finalización de la práctica es:
    <strong>{{ $fechaLimite }}</strong>.
</p>

<p>
    Recuerde que debe realizar la entrega de los documentos finales correspondientes:
</p>

<ul>
    <li>Informe final F-DC-128</li>
    <li>Rejilla de Evaluación F-DC-129</li>
    <li>Acta de Terminación F-DC-196</li>
</ul>

@if ($estudiante)
    <p><strong>Estudiante:</strong></p>
    <ul>
        <li><strong>Nombre:</strong> {{ $estudiante->name }}</li>
        <li><strong>Correo:</strong> <span class="email">{{ $estudiante->email }}</span></li>
        <li><strong>Celular:</strong> {{ $estudiante->nro_celular ?? 'No registra' }}</li>
    </ul>
@endif

@if ($integrante2)
    <p><strong>Segundo integrante:</strong></p>
    <ul>
        <li><strong>Nombre:</strong> {{ $integrante2->name }}</li>
        <li><strong>Correo:</strong> <span class="email">{{ $integrante2->email }}</span></li>
        <li><strong>Celular:</strong> {{ $integrante2->nro_celular ?? 'No registra' }}</li>
    </ul>
@endif

<p>
    <strong>NOTA:</strong> Si requiere solicitar una prórroga, debe realizar el trámite
    antes de la fecha límite establecida.
</p>

<p>
    Si ya realizó la entrega de los documentos finales, por favor omita este correo.
</p>

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