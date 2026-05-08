<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <style>

        body {
            font-family: Calibri, sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .container {
            padding: 20px;
        }

        .footer {
            margin-top: 30px;
        }

        .uppercase {
            text-transform: uppercase;
        }

    </style>

</head>

<body>

    <div class="container">

        <p>Buen día,</p>

        <p>Estimado usuario, en este correo se le informa que se ha realizado una nueva solicitud para iniciar de
            prácticas empresarial u institucional con la siguiente información:</p>
        <ul>
            <li><strong>Tipo de solicitud:</strong> SOLICITUD PRACTICAS EMPRESARIALES
                {{ $data['cuerpo_correo']['periodo'] ?? '' }}</li>
            <li><strong>Nivel académico: </strong>{{ $data['cuerpo_correo']['nivel'] ?? '' }}</li>
            <li><strong>Estado:</strong> {{ $data['cuerpo_correo']['estado'] ?? '' }}</li>
        </ul>
        <br>
        <p>Integrantes del proyecto: </p>
        <ul>
            <li><strong>Nombre:</strong> {{ $data['cuerpo_correo']['estudiante']->name ?? '' }}</li>

            <li>
                <strong>Documento:</strong>
                {{ $data['cuerpo_correo']['estudiante']->tipo_documento->tag ?? '' }}
                {{ $data['cuerpo_correo']['estudiante']->nro_documento ?? '' }}
            </li>
            <li><strong>Correo:</strong> {{ $data['cuerpo_correo']['correo'] ?? '' }}</li>
            <li><strong>Celular:</strong> {{ $data['cuerpo_correo']['celular'] ?? '' }}</li>
            <br>

            @if (!empty($data['cuerpo_correo']['integrante_2']))
                <li>
                    <strong>Nombre:</strong>

                    {{ is_object($data['cuerpo_correo']['integrante_2'])
                        ? $data['cuerpo_correo']['integrante_2']->name
                        : $data['cuerpo_correo']['integrante_2'] }}
                </li>

                <li><strong>Documento:</strong> {{ $data['cuerpo_correo']['integrante_2_documento'] ?? '' }}</li>

                <li><strong>Correo:</strong> {{ $data['cuerpo_correo']['integrante_2_correo'] ?? '' }}</li>

                <li><strong>Celular:</strong> {{ $data['cuerpo_correo']['integrante_2_celular'] ?? '' }}</li>
            @endif
        </ul>

        <p><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>

        <p class="uppercase">
            Se verificará la información para continuar el proceso.
        </p>

        <p>
            Este es un correo generado automáticamente por el sistema de prácticas,
            por favor no responder.
        </p>


    {{-- CAMPOS DINÁMICOS --}}
    @forelse ($data['cuerpo_correo']['campos'] as $campo)
        @if (!in_array($campo['campo'], ['id_integrante_2', 'tiene_empresa', 'periodo', 'hoja_vida', 'respuesta_comite', 'submited_fase0']))
            <li>
                <strong>
                    {{ ucfirst(str_replace('_', ' ', $campo['campo'] ?? '')) }}:
                </strong>
                {{ $campo['valor'] ?? '' }}
            </li>
        @endif
    @empty
    <p>No hay información adicional.</p>
    @endforelse

    @if (!empty($data['comentarios']))
        <p><strong>Comentarios:</strong>{{ strip_tags($data['comentarios']) }}</p>
    @endif

<div class="footer">

            <br>
            <p>Se verificará la información suministrada para dar accesso a las siguientes fases del proyecto.</p>
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

        </div>

    </div>

</body>

</html>