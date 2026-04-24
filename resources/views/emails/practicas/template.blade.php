<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <p>Buen día,</p>
    @if ($tipo_correo === 'fase_0')
        <p>Estimado usuario, en este correo se le informa que se ha realizado una nueva solicitud para práctica, con la
            siguiente información:</p>
        <br>

        <p><strong>Información general:</strong></p>
        <ul>
            <li><strong>Estudiante:</strong> {{ $data['estudiante'] }}</li>
            <li><strong>Tipo de solicitud:</strong> SOLICITUD PRACTICAS EMPRESARIALES {{ $cuerpo_correo['periodo'] }}
            </li>
            <li><strong>Estado:</strong> {{ $data['estado'] ?? '' }}</li>

        </ul>
        <p><strong>Fecha y hora de carga:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
        <br>
        <p class="uppercase">Se verificará la información suministrada para dar accesso a las siguientes fases del
            proyecto.</p>
        <br>
    @endif


    <!-- Respuesta del comite Fase 0 - Solicitud -->
    @if ($tipo_correo === 'respuesta_comite')
        <p>Estimado usuario, en este correo se le informa que su solicitud para iniciar un proyecto de grado
            <span>{!! $cuerpo_correo['estado'] === 'Aprobada'
                ? 'ha pasado a <strong>FASE 1</strong>'
                : 'ha sido <strong>RECHAZADA</strong>' !!}</span>:</p>

        <br>
        <ul>
            <li><strong>Estudiante:</strong> {{ $data['estudiante'] }}</li>
            <li><strong>Tipo de solicitud:</strong> SOLICITUD PRACTICAS EMPRESARIALES {{ $cuerpo_correo['periodo'] }}
            </li>
            <li><strong>Estado:</strong> {{ $data['estado'] ?? '' }}</li>
            <li><strong>Empresa:</strong> {{ $cuerpo_correo['empresa'] }}</li>
            <li><strong>Nivel académico:</strong> {{ $cuerpo_correo['nivel'] }}</li>
        </ul>
        <p><strong>Fecha y hora de respuesta a la solicitud:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
        <br>
        @if ($cuerpo_correo['estado'] === 'Aprobada')
            <p>Se le recomienda ingresar al sistema para continuar con las siguientes fases del proyecto en curso.</p>
        @else
            <p>Se le recomienda ingresar al sistema para volver a realizar su solicitud.</p>
        @endif
    @endif

    {{--  CAMPOS DINÁMICOS --}}
    @if (!empty($data['campos']))
        @foreach ($data['campos'] as $campo)
            <li>
                <strong>{{ ucfirst(str_replace('_', ' ', $campo['campo'] ?? '')) }}:</strong>
                {{ $campo['valor'] ?? '' }}
            </li>
        @endforeach
    @endif

    <br>

    <p style="font-size: 12px;">
        Esto es un correo generado automáticamente por el sistema de trabajos de grado del programa, favor no responder
        al mismo.
    </p>

</body>

</html>
