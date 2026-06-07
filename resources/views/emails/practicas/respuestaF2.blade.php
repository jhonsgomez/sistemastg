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
        $destinatario = $cuerpo['destinatario'] ?? 'estudiante';
        $estado = $cuerpo['estado'] ?? '';
    @endphp

    <p>Buen día,</p>

    @if ($destinatario === 'director')
        <p>
            Estimado docente, en este correo se le informa que ha sido asignado como
            <strong class="uppercase">DIRECTOR DE PRÁCTICAS EMPRESARIALES</strong>.
        </p>

    @elseif ($destinatario === 'evaluador')
        <p>
            Estimado docente, en este correo se le informa que ha sido asignado como
            <strong class="uppercase">EVALUADOR DE PRÁCTICAS EMPRESARIALES</strong>.
        </p>

    @elseif ($destinatario === 'codirector')
        <p>
            Estimado docente, en este correo se le informa que ha sido asignado como
            <strong class="uppercase">CODIRECTOR DE PRÁCTICAS EMPRESARIALES</strong>.
        </p>

    @else
        <p>
            Estimado estudiante, en este correo se le informa la respuesta de su solicitud
            correspondiente a la <strong>FASE 2</strong> de prácticas empresariales
            {!! $estado === 'Aprobada'
                ? 'ha sido <strong>APROBADA</strong>.'
                : 'ha sido <strong>RECHAZADA</strong>.' !!}
        </p>
    @endif

    <ul>
        <li><strong>Estado:</strong> {{ $estado }}</li>
        <li><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i:s') }}</li>
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

    <p><strong>Respuesta del comité:</strong></p>
    <p>{{ $cuerpo['respuesta'] ?? '' }}</p>

    @if ($estado === 'Aprobada')

        @if ($destinatario === 'director')

            @if (!empty($cuerpo['codirector']))
                <p><strong>Codirector asignado:</strong></p>
                <ul>
                    <li><strong>Nombre:</strong> {{ $cuerpo['codirector']->name ?? '' }}</li>
                    <li>
                        <strong>Correo:</strong>
                        <a href="mailto:{{ $cuerpo['codirector']->email ?? '' }}" class="email">
                            {{ $cuerpo['codirector']->email ?? '' }}
                        </a>
                    </li>
                </ul>
            @endif

            @if (!empty($cuerpo['evaluador']))
                <p><strong>Evaluador asignado:</strong></p>
                <ul>
                    <li><strong>Nombre:</strong> {{ $cuerpo['evaluador']->name ?? '' }}</li>
                    <li>
                        <strong>Correo:</strong>
                        <a href="mailto:{{ $cuerpo['evaluador']->email ?? '' }}" class="email">
                            {{ $cuerpo['evaluador']->email ?? '' }}
                        </a>
                    </li>
                </ul>
            @endif

        @elseif ($destinatario === 'evaluador')

            @if (!empty($cuerpo['director']))
                <p><strong>Director asignado:</strong></p>
                <ul>
                    <li><strong>Nombre:</strong> {{ $cuerpo['director']->name ?? '' }}</li>
                    <li>
                        <strong>Correo:</strong>
                        <a href="mailto:{{ $cuerpo['director']->email ?? '' }}" class="email">
                            {{ $cuerpo['director']->email ?? '' }}
                        </a>
                    </li>
                </ul>
            @endif

            @if (!empty($cuerpo['codirector']))
                <p><strong>Codirector asignado:</strong></p>
                <ul>
                    <li><strong>Nombre:</strong> {{ $cuerpo['codirector']->name ?? '' }}</li>
                    <li>
                        <strong>Correo:</strong>
                        <a href="mailto:{{ $cuerpo['codirector']->email ?? '' }}" class="email">
                            {{ $cuerpo['codirector']->email ?? '' }}
                        </a>
                    </li>
                </ul>
            @endif

        @elseif ($destinatario === 'codirector')

            @if (!empty($cuerpo['director']))
                <p><strong>Director asignado:</strong></p>
                <ul>
                    <li><strong>Nombre:</strong> {{ $cuerpo['director']->name ?? '' }}</li>
                    <li>
                        <strong>Correo:</strong>
                        <a href="mailto:{{ $cuerpo['director']->email ?? '' }}" class="email">
                            {{ $cuerpo['director']->email ?? '' }}
                        </a>
                    </li>
                </ul>
            @endif

            @if (!empty($cuerpo['evaluador']))
                <p><strong>Evaluador asignado:</strong></p>
                <ul>
                    <li><strong>Nombre:</strong> {{ $cuerpo['evaluador']->name ?? '' }}</li>
                    <li>
                        <strong>Correo:</strong>
                        <a href="mailto:{{ $cuerpo['evaluador']->email ?? '' }}" class="email">
                            {{ $cuerpo['evaluador']->email ?? '' }}
                        </a>
                    </li>
                </ul>
            @endif

        @else

            @if (!empty($cuerpo['director']))
                <p>
                    <strong>Director asignado:</strong>
                    {{ $cuerpo['director']->name ?? '' }}
                    -
                    <a href="mailto:{{ $cuerpo['director']->email ?? '' }}" class="email">
                        {{ $cuerpo['director']->email ?? '' }}
                    </a>
                </p>
            @endif

            @if (!empty($cuerpo['codirector']))
                <p>
                    <strong>Codirector asignado:</strong>
                    {{ $cuerpo['codirector']->name ?? '' }}
                    -
                    <a href="mailto:{{ $cuerpo['codirector']->email ?? '' }}" class="email">
                        {{ $cuerpo['codirector']->email ?? '' }}
                    </a>
                </p>
            @endif

        @endif

        <p>
            Puede continuar con la siguiente fase del proceso de prácticas empresariales.
        </p>

    @else

        <p>
            Debe revisar las observaciones realizadas por el comité y volver a realizar el proceso correspondiente.
        </p>

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