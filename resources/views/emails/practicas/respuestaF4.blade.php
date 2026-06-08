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
    $remitente = $cuerpo['remitente'] ?? '';
    $destinatario = $cuerpo['destinatario'] ?? '';
    $estado = $cuerpo['estado'] ?? '';
@endphp

<p>Buen día,</p>

@if ($remitente === 'evaluador')

    @if ($destinatario === 'comite')
        <p>
            Estimado comité, en este correo se le informa que el
            <strong class="uppercase">DOCENTE EVALUADOR</strong>
            ha revisado los documentos de la <strong>FASE 4</strong> de prácticas empresariales
            y los ha enviado para revisión del comité.
        </p>
    @else
        <p>
            Estimado estudiante, en este correo se le informa que el
            <strong class="uppercase">DOCENTE EVALUADOR</strong>
            ha revisado los documentos correspondientes a la <strong>FASE 4</strong>
            de prácticas empresariales
            {!! $estado === 'Aprobada'
                ? 'y los ha enviado al <strong>COMITÉ DE TRABAJOS DE GRADO</strong>.'
                : 'y los ha <strong>RECHAZADO</strong>.' !!}
        </p>
    @endif

@else

    <p>
        Estimado usuario, en este correo se le informa que el
        <strong class="uppercase">COMITÉ DE TRABAJOS DE GRADO</strong>
        ha revisado los documentos correspondientes a la <strong>FASE 4</strong>
        de prácticas empresariales
        {!! $estado === 'Aprobada'
            ? 'y ha pasado la práctica a <strong>FASE 5</strong>.'
            : 'y la ha <strong>RECHAZADO</strong>.' !!}
    </p>

@endif

<ul>
    <li><strong>Estado:</strong> {{ $estado }}</li>
    <li><strong>Fecha y hora:</strong> {{ now()->format('d/m/Y H:i:s') }}</li>

    @if (!empty($cuerpo['titulo_propuesta']))
        <li><strong>Título de la propuesta:</strong> {{ $cuerpo['titulo_propuesta'] }}</li>
    @endif

    @if (!empty($cuerpo['nro_acta']) && !empty($cuerpo['fecha_acta']))
        <li><strong>Acta de registro:</strong> #{{ $cuerpo['nro_acta'] }} - {{ $cuerpo['fecha_acta'] }}</li>
    @endif
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
        <li><strong>Documento:</strong> {{ $cuerpo['integrante_2_documento'] ?? '' }}</li>
        <li>
            <strong>Correo:</strong>
            <a href="mailto:{{ $cuerpo['integrante_2_correo'] ?? '' }}" class="email">
                {{ $cuerpo['integrante_2_correo'] ?? '' }}
            </a>
        </li>
        <li><strong>Celular:</strong> {{ $cuerpo['integrante_2_celular'] ?? '' }}</li>
    @endif
</ul>

<p><strong>Respuesta:</strong></p>
<p>{!! $cuerpo['respuesta'] ?? '' !!}</p>

@if ($remitente === 'evaluador')

    @if ($estado === 'Aprobada')
        <p>
            Se le recuerda que el <strong>Comité de Trabajos de Grado</strong>
            será el encargado de aprobar el paso a la siguiente fase.
        </p>
    @else
        <p>
            Se recomienda revisar las observaciones del evaluador,
            corregir los documentos y realizar nuevamente el envío correspondiente.
        </p>
    @endif

@else

    @if ($estado === 'Aprobada')
        <p>
            A partir de este momento, los estudiantes podrán continuar con la
            <strong>FASE 5</strong> del proceso de prácticas empresariales.
        </p>
    @else
        <p>
            Se recomienda revisar las observaciones del comité.
            La práctica volverá a la fase correspondiente para realizar los ajustes necesarios.
        </p>
    @endif

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