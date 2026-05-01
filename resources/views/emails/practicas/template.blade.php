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
        <p>Estimado usuario, en este correo se le informa que se ha realizado una nueva solicitud para iniciar de
            prácticas empresarial u institucional con la siguiente información:</p>
        <ul>
            <li><strong>Tipo de solicitud:</strong> SOLICITUD PRACTICAS EMPRESARIALES
                {{ $cuerpo_correo['periodo'] ?? '' }}</li>
            <li><strong>Nivel académico: </strong>{{ $cuerpo_correo['nivel'] ?? '' }}</li>
            <li><strong>Estado:</strong> {{ $cuerpo_correo['estado'] ?? '' }}</li>
        </ul>
        <br>
        <p>Integrantes del proyecto: </p>
        <ul>
            <li><strong>Nombre:</strong> {{ $cuerpo_correo['estudiante']->name ?? '' }}</li>

            <li>
                <strong>Documento:</strong>
                {{ $cuerpo_correo['estudiante']->tipo_documento->tag ?? '' }}
                {{ $cuerpo_correo['estudiante']->nro_documento ?? '' }}
            </li>
            <li><strong>Correo:</strong> {{ $cuerpo_correo['correo'] ?? '' }}</li>
            <li><strong>Celular:</strong> {{ $cuerpo_correo['celular'] ?? '' }}</li>
            <br>

            @if (!empty($cuerpo_correo['integrante_2']))
                <li><strong>Nombre:</strong> {{ $cuerpo_correo['integrante_2'] }}</li>

                <li><strong>Documento:</strong> {{ $cuerpo_correo['integrante_2_documento'] ?? '' }}</li>

                <li><strong>Correo:</strong> {{ $cuerpo_correo['integrante_2_correo'] ?? '' }}</li>

                <li><strong>Celular:</strong> {{ $cuerpo_correo['integrante_2_celular'] ?? '' }}</li>
            @endif
        </ul>

        <p><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>

        <p class="uppercase">
            Se verificará la información para continuar el proceso.
        </p>
    @endif


    @if ($tipo_correo === 'respuesta_comite')
        <p>Estimado usuario, en este correo se le informa que su solicitud para iniciar practicas
            <span> {!! ($cuerpo_correo['estado'] ?? '') === 'Aprobada'
                ? 'ha pasado a <strong>FASE 1</strong>'
                : 'ha sido <strong>RECHAZADA</strong>' !!}</span> :
        </p>

        <ul>
            <li><strong>Tipo de solicitud:</strong> SOLICITUD PRACTICAS EMPRESARIALES
                {{ $cuerpo_correo['periodo'] ?? '' }}</li>
            <li><strong>Nivel académico: </strong>{{ $cuerpo_correo['nivel'] ?? '' }}</li>
            <li><strong>Estado:</strong> {{ $cuerpo_correo['estado'] ?? '' }}</li>
        </ul>
        <br>
        <p>Integrantes del proyecto: </p>
        <ul>
            <li><strong>Nombre:</strong> {{ $cuerpo_correo['estudiante']->name ?? '' }}</li>

            <li>
                <strong>Documento:</strong>
                {{ $cuerpo_correo['estudiante']->tipo_documento->tag ?? '' }}
                {{ $cuerpo_correo['estudiante']->nro_documento ?? '' }}
            </li>
            <li><strong>Correo:</strong> {{ $cuerpo_correo['correo'] ?? '' }}</li>
            <li><strong>Celular:</strong> {{ $cuerpo_correo['celular'] ?? '' }}</li>
            <br>

            @if (!empty($cuerpo_correo['integrante_2']))
                <li><strong>Nombre:</strong> {{ $cuerpo_correo['integrante_2']->name ?? '' }}</li>
                <li><strong>Documento:</strong> {{ $cuerpo_correo['integrante_2_documento'] ?? '' }}</li>
                <li><strong>Correo:</strong> {{ $cuerpo_correo['integrante_2_correo'] ?? '' }}</li>
                <li><strong>Celular:</strong> {{ $cuerpo_correo['integrante_2_celular'] ?? '' }}</li>
            @endif
        </ul>

        <p><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>

        @if ($cuerpo_correo['estado'] === 'Aprobada')
            <p>Se le recomienda ingresar al sistema para continuar con las siguientes fases del proyecto en curso.</p>
        @else
            <p>Se le recomienda ingresar al sistema para volver a realizar su solicitud.</p>
        @endif

    @endif


    {{-- CAMPOS DINÁMICOS --}}
    @foreach ($cuerpo_correo['campos'] as $campo)
        @if (!in_array($campo['campo'], ['id_integrante_2', 'tiene_empresa', 'periodo', 'hoja_vida', 'respuesta_comite']))
            <li>
                <strong>
                    {{ ucfirst(str_replace('_', ' ', $campo['campo'] ?? '')) }}:
                </strong>
                {{ $campo['valor'] ?? '' }}
            </li>
        @endif
    @endforeach

    @if(!empty($comentarios))
        <p><strong>Comentarios:</strong>{{ strip_tags($comentarios) }}</p>
    @endif

    @if (!empty($mensaje_adicional))
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
