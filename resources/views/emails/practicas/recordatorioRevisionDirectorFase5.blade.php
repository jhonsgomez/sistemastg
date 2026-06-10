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
    $director = $cuerpo['director'] ?? null;
    $estudiante = $cuerpo['estudiante'] ?? null;
    $integrante2 = $cuerpo['integrante_2'] ?? null;
    $estado = $cuerpo['estado'] ?? 'No especificado';
@endphp

<p>Buen día,</p>

<p>
    Estimado docente director, en este correo se le recuerda que tiene pendiente
    la revisión de los documentos finales de una práctica empresarial.
</p>

<p>
    Estado actual de la práctica:
    <strong class="uppercase">{{ $estado }}</strong>
</p>

<p>Documentos pendientes por revisar:</p>

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
    <strong>NOTA:</strong> Si ya realizó la revisión correspondiente,
    por favor omita este correo.
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