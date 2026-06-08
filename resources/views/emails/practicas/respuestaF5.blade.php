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

    @if ($destinatario === 'evaluador')
        <p>
            Estimado docente, en este correo se le informa que el director ha aprobado los documentos
            correspondientes a la <strong>FASE 5</strong> de prácticas empresariales y ha enviado la solicitud
            para su revisión como <strong class="uppercase">EVALUADOR</strong> en la <strong>FASE 6</strong>.
        </p>
    @else
        <p>
            Estimado estudiante, en este correo se le informa la respuesta del director a los documentos
            correspondientes a la <strong>FASE 5</strong> de prácticas empresariales
            {!! $estado === 'Aprobada'
                ? 'ha sido <strong>APROBADA</strong>.'
                : 'ha sido <strong>RECHAZADA</strong>.' !!}
        </p>
    @endif

    <ul>
        <li><strong>Estado:</strong> {{ $estado }}</li>
        <li><strong>Fecha y hora:</strong> {{ now()->format('d/m/Y H:i:s') }}</li>
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

            <li>
                <strong>Documento:</strong>
                {{ $cuerpo['integrante_2_documento'] ?? '' }}
            </li>

            <li>
                <strong>Correo:</strong>
                <a href="mailto:{{ $cuerpo['integrante_2_correo'] ?? '' }}" class="email">
                    {{ $cuerpo['integrante_2_correo'] ?? '' }}
                </a>
            </li>

            <li>
                <strong>Celular:</strong>
                {{ $cuerpo['integrante_2_celular'] ?? '' }}
            </li>
        @endif
    </ul>

    <p><strong>Respuesta del director:</strong></p>

    <p>{!! $cuerpo['respuesta'] ?? '' !!}</p>

    @if ($estado === 'Aprobada')

        @if ($destinatario === 'evaluador')
            <p>
                <strong>NOTA:</strong> El evaluador será el encargado de revisar y validar los documentos
                correspondientes al informe final de prácticas empresariales para continuar con el proceso.
            </p>

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
        @else
            <p>
                Los documentos correspondientes a la <strong>FASE 5</strong> han sido aprobados por el director.
                El evaluador será el encargado de continuar con la revisión en la <strong>FASE 6</strong>
                del proceso de prácticas empresariales.
            </p>

            <p>
                La práctica ha sido habilitada para continuar con la <strong>FASE 6</strong> del proceso.
            </p>
        @endif

    @else

        <p>
            Debe revisar las observaciones realizadas por el director, realizar las correcciones correspondientes
            al informe final y efectuar nuevamente el envío de los documentos requeridos.
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