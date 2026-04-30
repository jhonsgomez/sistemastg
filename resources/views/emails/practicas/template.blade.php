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
    <p>Buen día,</p>

    @if ($tipo_correo === 'practicas_fase_0')
        <p>Estimado usuario, se ha realizado una nueva solicitud de práctica:</p>

        <p><strong>Información general:</strong></p>
        <ul>
            <li><strong>Estudiante:</strong> {{ $cuerpo_correo['estudiante'] ?? '' }}</li>
            <li><strong>Tipo de solicitud:</strong> SOLICITUD PRACTICAS EMPRESARIALES {{ $cuerpo_correo['periodo'] ?? '' }}</li>
            <li><strong>Estado:</strong> {{ $cuerpo_correo['estado'] ?? '' }}</li>
        </ul>

        <p><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>

        <p class="uppercase">
            Se verificará la información para continuar el proceso.
        </p>
    @endif


    @if ($tipo_correo === 'respuesta_comite')
        <p>
            Su solicitud ha sido
            {!! ($cuerpo_correo['estado'] ?? '') === 'Aprobada'
                ? '<strong>APROBADA</strong>'
                : '<strong>RECHAZADA</strong>' !!}
        </p>

        <ul>
            <li><strong>Estudiante:</strong> {{ $cuerpo_correo['estudiante'] ?? '' }}</li>
            <li><strong>Empresa:</strong> {{ $cuerpo_correo['empresa'] ?? '' }}</li>
            <li><strong>Nivel:</strong> {{ $cuerpo_correo['nivel'] ?? '' }}</li>
        </ul>
    @endif


    {{-- CAMPOS DINÁMICOS --}}
    @if (!empty($cuerpo_correo['campos']))
        <ul>
            @foreach ($cuerpo_correo['campos'] as $campo)
                <li>
                    <strong>
                        {{ ucfirst(str_replace('_', ' ', $campo['campo'] ?? '')) }}:
                    </strong>
                    {{ $campo['valor'] ?? '' }}
                </li>
            @endforeach
        </ul>
    @endif

    @if ($comentarios)
        <br>
        <p><strong>Comentarios:</strong></p>
        {!! $comentarios !!}
        <br>
    @endif

    @if ($mensaje_adicional)
        <br>
        <p>{!! $mensaje_adicional !!}</p><br><br>
    @endif

    <br>

    <p>Atentamente,</p>
        <p>Comité de Trabajos de Grado<br>
        Programa de Tecnología en Desarrollo de Sistemas Informáticos e Ingeniería de Sistemas<br>
        Unidades Tecnológicas de Santander</p>

</body>
</html>