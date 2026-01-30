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

    @if ($tipo_correo === 'config_estudiante')
        @if (isset($cuerpo_correo['tipo_accion']) && $cuerpo_correo['tipo_accion'] == 'retiro')
            <p>Estimado estudiante <strong>{{ mb_strtoupper($cuerpo_correo['estudiante']->name) ?? '' }}</strong>, en este correo se le informa que se ha realizado una nueva solicitud para el proyecto {!! !empty($cuerpo_correo['titulo']) ? 'titulado "<strong>' . mb_strtoupper($cuerpo_correo['titulo']) . '</strong>"' : 'con id "<strong>' . $cuerpo_correo['proyecto_id'] . '</strong>"' !!}, con la siguiente información:</p>
        @else
            <p>Estimado usuario, en este correo se le informa que se ha realizado una nueva solicitud para el proyecto titulado "<strong>{{ mb_strtoupper($cuerpo_correo['titulo']) }}</strong>", con la siguiente información:</p>
        @endif

        <p>Información de la solicitud: </p>
        <ul>
            <li><strong>Tipo de solicitud: </strong>{{ $cuerpo_correo['tipo_solicitud'] }}</li>
            <li><strong>Fecha y hora de la solicitud: </strong>{{ now()->format('d/m/Y H:i:s') }}</li>
            <li><strong>Modalidad del proyecto: </strong>{{ $cuerpo_correo['modalidad'] }}</li>
            <li><strong>Nivel académico: </strong>{{ $cuerpo_correo['nivel'] }}</li>
            <li><strong>Código de modalidad: </strong>{{ $cuerpo_correo['codigo_modalidad'] }}</li>
        </ul>

        <p>Integrantes del proyecto: </p>
        <ul>
            @if (isset($cuerpo_correo['integrante_1']))
                <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_1']->name }}</li>
                <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_1']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_1']->nro_documento }}</li>
                <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_1']->email }}</span></li>
                <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_1']->nro_celular }}</li>
            @endif

            @if (isset($cuerpo_correo['integrante_2']))
                <br>
                <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_2']->name }}</li>
                <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_2']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_2']->nro_documento }}</li>
                <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_2']->email }}</span></li>
                <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_2']->nro_celular }}</li>
            @endif

            @if (isset($cuerpo_correo['integrante_3']))
                <br>
                <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_3']->name }}</li>
                <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_3']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_3']->nro_documento }}</li>
                <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_3']->email }}</span></li>
                <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_3']->nro_celular }}</li>
            @endif
        </ul>

        <br>
        <b>Comentarios:</b>
        {!! $cuerpo_correo['solicitud'] !!}

        <br>
        <p><strong>NOTA: </strong>El comité de trabajos de grado revisará la solicitud y dará respuesta a los estudiantes. Si presenta algún inconveniente, favor comunicarse al correo electrónico del comité de trabajos de grado: <span class="email">{{ config('custom.correo_sistemas') }}</span></p>
    @endif

    @if ($tipo_correo === 'config_admin')
        @if ($cuerpo_correo['destinatario'] === 'director')
            <p>Estimado usuario, en este correo se le informa que se ha realizado un <strong>{{ isset($cuerpo_correo['tipo_solicitud']) ? $cuerpo_correo['tipo_solicitud'] : 'No Especificado' }}</strong> al proyecto titulado "<strong>{{ mb_strtoupper($cuerpo_correo['titulo']) }}</strong>", con la siguiente información:</p>
            
            <br>
            <p>Información de la solicitud:</p>
            <ul>
                <li><strong>Tipo de solicitud: </strong>{{ $cuerpo_correo['tipo_solicitud'] }}</li>
                <li><strong>Fecha y hora de la respuesta: </strong>{{ now()->format('d/m/Y H:i:s') }}</li>
                <li><strong>Modalidad del proyecto: </strong>{{ $cuerpo_correo['modalidad'] }}</li>
                <li><strong>Nivel académico: </strong>{{ $cuerpo_correo['nivel'] }}</li>
                <li><strong>Código de modalidad: </strong>{{ $cuerpo_correo['codigo_modalidad'] }}</li>
                <li><strong>Acta de registro: </strong>#{{ $cuerpo_correo['nro_acta'] }} del {{ $cuerpo_correo['fecha_acta'] }}</li>
            </ul>

            <br>
            <p>Integrantes del proyecto: </p>
            <ul>
                @if (isset($cuerpo_correo['integrante_1']))
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_1']->name }}</li>
                    <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_1']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_1']->nro_documento }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_1']->email }}</span></li>
                    <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_1']->nro_celular }}</li>
                @endif

                @if (isset($cuerpo_correo['integrante_2']))
                    <br>
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_2']->name }}</li>
                    <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_2']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_2']->nro_documento }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_2']->email }}</span></li>
                    <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_2']->nro_celular }}</li>
                @endif

                @if (isset($cuerpo_correo['integrante_3']))
                    <br>
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_3']->name }}</li>
                    <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_3']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_3']->nro_documento }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_3']->email }}</span></li>
                    <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_3']->nro_celular }}</li>
                @endif
            </ul>
            
            <br>
            <b>Respuesta de la solicitud:</b>
            {!! $cuerpo_correo['solicitud'] !!}

            @if (isset($cuerpo_correo['fecha_maxima_informe']))
                <br>    
                <p>Se le recuerda que la fecha máxima de aprobación del <strong>F-DC-125</strong> para este proyecto quedó establecida para el <strong>{{ $cuerpo_correo['fecha_maxima_informe'] }}</strong>. Si no es aprobado, una vez pasada esta fecha el sistema denegará el acceso al proyecto.</p>
            @endif

            <br>
            @if (isset($cuerpo_correo['nuevo_evaluador_nombre']))
                <br>
                <p>Nuevo evaluador asignado: </p>
                <ul>
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['nuevo_evaluador_nombre'] }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['nuevo_evaluador_correo'] }}</span></li>
                </ul>
            @endif
        @elseif ($cuerpo_correo['destinatario'] === 'evaluador')
            <p>Estimado usuario, en este correo se le informa que se ha realizado un <strong>{{ isset($cuerpo_correo['tipo_solicitud']) ? $cuerpo_correo['tipo_solicitud'] : 'No Especificado' }}</strong> al proyecto titulado "<strong>{{ mb_strtoupper($cuerpo_correo['titulo']) }}</strong>", con la siguiente información:</p>
            
            <br>
            <p>Información de la solicitud: </p>
            <ul>
                <li><strong>Tipo de solicitud: </strong>{{ $cuerpo_correo['tipo_solicitud'] }}</li>
                <li><strong>Fecha y hora de la respuesta: </strong>{{ now()->format('d/m/Y H:i:s') }}</li>
                <li><strong>Modalidad del proyecto: </strong>{{ $cuerpo_correo['modalidad'] }}</li>
                <li><strong>Nivel académico: </strong>{{ $cuerpo_correo['nivel'] }}</li>
                <li><strong>Código de modalidad: </strong>{{ $cuerpo_correo['codigo_modalidad'] }}</li>
                <li><strong>Acta de registro: </strong>#{{ $cuerpo_correo['nro_acta'] }} del {{ $cuerpo_correo['fecha_acta'] }}</li>
            </ul>

            <br>
            <p>Integrantes del proyecto: </p>
            <ul>
                @if (isset($cuerpo_correo['integrante_1']))
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_1']->name }}</li>
                    <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_1']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_1']->nro_documento }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_1']->email }}</span></li>
                    <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_1']->nro_celular }}</li>
                @endif

                @if (isset($cuerpo_correo['integrante_2']))
                    <br>
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_2']->name }}</li>
                    <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_2']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_2']->nro_documento }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_2']->email }}</span></li>
                    <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_2']->nro_celular }}</li>
                @endif

                @if (isset($cuerpo_correo['integrante_3']))
                    <br>
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_3']->name }}</li>
                    <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_3']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_3']->nro_documento }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_3']->email }}</span></li>
                    <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_3']->nro_celular }}</li>
                @endif
            </ul>

            <br>
            <b>Respuesta de la solicitud:</b>
            {!! $cuerpo_correo['solicitud'] !!}

            @if (isset($cuerpo_correo['fecha_maxima_informe']))
                <br>
                <p>Se le recuerda que la fecha máxima de aprobación del <strong>F-DC-125</strong> para este proyecto quedó establecida para el <strong>{{ $cuerpo_correo['fecha_maxima_informe'] }}</strong>. Si no es aprobado, una vez pasada esta fecha el sistema denegará el acceso al proyecto.</p>
            @endif

            @if (isset($cuerpo_correo['nuevo_director_nombre']))
                <br>
                <p>Nuevo director asignado: </p>
                <ul>
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['nuevo_director_nombre'] }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['nuevo_director_correo'] }}</span></li>
                </ul>
            @endif
        @elseif (str_contains($cuerpo_correo['destinatario'], 'antiguo')) 
            <p>Estimado docente, en este correo se le informa que actualmente usted ya <strong>NO</strong> es el {!! str_contains($cuerpo_correo['destinatario'], 'director') ? '<strong>DIRECTOR</strong>' : '<strong>EVALUADOR</strong>' !!} del proyecto titulado "<strong>{{ mb_strtoupper($cuerpo_correo['titulo']) }}</strong>", con la siguiente información:</p>
            
            <br>
            <p>Información de la solicitud:</p>
            <ul>
                <li><strong>Tipo de solicitud: </strong>{{ $cuerpo_correo['tipo_solicitud'] }}</li>
                <li><strong>Fecha y hora de la emisión: </strong>{{ now()->format('d/m/Y H:i:s') }}</li>
                <li><strong>Modalidad del proyecto: </strong>{{ $cuerpo_correo['modalidad'] }}</li>
                <li><strong>Nivel académico: </strong>{{ $cuerpo_correo['nivel'] }}</li>
                <li><strong>Código de modalidad: </strong>{{ $cuerpo_correo['codigo_modalidad'] }}</li>
                <li><strong>Acta de registro: </strong>#{{ $cuerpo_correo['nro_acta'] }} del {{ $cuerpo_correo['fecha_acta'] }}</li>
            </ul>

            <br>
            <p>Integrantes del proyecto: </p>
            <ul>
                @if (isset($cuerpo_correo['integrante_1']))
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_1']->name }}</li>
                    <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_1']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_1']->nro_documento }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_1']->email }}</span></li>
                    <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_1']->nro_celular }}</li>
                @endif

                @if (isset($cuerpo_correo['integrante_2']))
                    <br>
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_2']->name }}</li>
                    <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_2']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_2']->nro_documento }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_2']->email }}</span></li>
                    <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_2']->nro_celular }}</li>
                @endif

                @if (isset($cuerpo_correo['integrante_3']))
                    <br>
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_3']->name }}</li>
                    <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_3']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_3']->nro_documento }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_3']->email }}</span></li>
                    <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_3']->nro_celular }}</li>
                @endif
            </ul>

            <br>
            <b>Comentarios de la solicitud:</b>
            {!! $cuerpo_correo['solicitud'] !!}
        @elseif ($cuerpo_correo['destinatario'] === 'retiro')
            <p>Estimado usuario, en este correo se le informa que se ha realizado un <strong>{{ mb_strtoupper($cuerpo_correo['tipo_solicitud']) }}</strong> para el proyecto {!! !empty($cuerpo_correo['titulo']) ? 'titulado "<strong>' . mb_strtoupper($cuerpo_correo['titulo']) . '</strong>"' : 'con id "<strong>' . $cuerpo_correo['proyecto_id'] . '</strong>"' !!}, con la siguiente información:</p>
            
            <p>Información de la solicitud:</p>
            <ul>
                <li><strong>Tipo de solicitud: </strong>{{ $cuerpo_correo['tipo_solicitud'] }}</li>
                <li><strong>Fecha y hora de la emisión: </strong>{{ now()->format('d/m/Y H:i:s') }}</li>
                <li><strong>Modalidad del proyecto: </strong>{{ $cuerpo_correo['modalidad'] }}</li>
                <li><strong>Nivel académico: </strong>{{ $cuerpo_correo['nivel'] }}</li>
                <li><strong>Código de modalidad: </strong>{{ $cuerpo_correo['codigo_modalidad'] }}</li>
                <li><strong>Acta de registro: </strong>#{{ $cuerpo_correo['nro_acta'] }} del {{ $cuerpo_correo['fecha_acta'] }}</li>
            </ul>

            @if (isset($cuerpo_correo['estudiante_retirado']))
                <p>Estudiante retirado: </p>
                <ul>
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['estudiante_retirado']->name }}</li>
                    <li><strong>Documento: </strong>{{ $cuerpo_correo['estudiante_retirado']->tipo_documento->tag }} {{ $cuerpo_correo['estudiante_retirado']->nro_documento }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['estudiante_retirado']->email }}</span></li>
                    <li><strong>Celular: </strong>{{ $cuerpo_correo['estudiante_retirado']->nro_celular }}</li>
                </ul>
            @endif

            <p>Resto de los integrantes: </p>
            <ul>
                @if (!isset($cuerpo_correo['integrante_1']) && !isset($cuerpo_correo['integrante_2']) && !isset($cuerpo_correo['integrante_3']))
                    <li>No hay más integrantes activos en este proyecto.</li>
                    <br>
                @endif

                @if (isset($cuerpo_correo['integrante_1']))
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_1']->name }}</li>
                    <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_1']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_1']->nro_documento }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_1']->email }}</span></li>
                    <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_1']->nro_celular }}</li>
                @endif

                @if (isset($cuerpo_correo['integrante_2']))
                    <br>
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_2']->name }}</li>
                    <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_2']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_2']->nro_documento }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_2']->email }}</span></li>
                    <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_2']->nro_celular }}</li>
                @endif

                @if (isset($cuerpo_correo['integrante_3']))
                    <br>
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_3']->name }}</li>
                    <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_3']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_3']->nro_documento }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_3']->email }}</span></li>
                    <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_3']->nro_celular }}</li>
                @endif
            </ul>

            <p>A partir de este momento, el estudiante <strong>{{ mb_strtoupper($cuerpo_correo['estudiante_retirado']->name) }}</strong> ya no forma parte del proyecto, lo que implica que la continuidad del mismo se llevará a cabo por el resto de los integrantes. Si el proyecto no cuenta con más integrantes activos, se procederá a su cierre de forma automática.</p>

            <br>
            <b>Comentarios de la solicitud:</b>
            {!! $cuerpo_correo['solicitud'] !!}

            <br>
            <p>Si presenta algún inconveniente, favor comunicarse al correo electrónico del comité de trabajos de grado: <span class="email">{{ config('custom.correo_sistemas') }}</span></p>
        @else
            <p>Estimado usuario, en este correo se le informa que el comité de trabajos de grado ha revisado la solicitud para el proyecto titulado "<strong>{{ mb_strtoupper($cuerpo_correo['titulo']) }}</strong>", con la siguiente información:</p>
            
            <br>
            <p>Información de la solicitud:</p>
            <ul>
                <li><strong>Tipo de solicitud: </strong>{{ isset($cuerpo_correo['tipo_solicitud']) ? $cuerpo_correo['tipo_solicitud'] : 'No Especificado' }}</li>
                <li><strong>Fecha y hora de la respuesta: </strong>{{ now()->format('d/m/Y H:i:s') }}</li>
                <li><strong>Modalidad del proyecto: </strong>{{ $cuerpo_correo['modalidad'] }}</li>
                <li><strong>Nivel académico: </strong>{{ $cuerpo_correo['nivel'] }}</li>
                <li><strong>Código de modalidad: </strong>{{ $cuerpo_correo['codigo_modalidad'] }}</li>
                <li><strong>Acta de registro: </strong>#{{ $cuerpo_correo['nro_acta'] }} del {{ $cuerpo_correo['fecha_acta'] }}</li>
            </ul>

            <br>
            <p>Integrantes del proyecto: </p>
            <ul>
                @if (isset($cuerpo_correo['integrante_1']))
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_1']->name }}</li>
                    <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_1']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_1']->nro_documento }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_1']->email }}</span></li>
                    <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_1']->nro_celular }}</li>
                @endif

                @if (isset($cuerpo_correo['integrante_2']))
                    <br>
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_2']->name }}</li>
                    <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_2']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_2']->nro_documento }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_2']->email }}</span></li>
                    <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_2']->nro_celular }}</li>
                @endif

                @if (isset($cuerpo_correo['integrante_3']))
                    <br>
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_3']->name }}</li>
                    <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_3']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_3']->nro_documento }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_3']->email }}</span></li>
                    <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_3']->nro_celular }}</li>
                @endif
            </ul>

            <br>
            <b>Respuesta de la solicitud:</b>
            {!! $cuerpo_correo['solicitud'] !!}

            @if (isset($cuerpo_correo['fecha_maxima_informe']))
                <br>
                <p>Se le recuerda que la fecha máxima de aprobación del <strong>F-DC-125</strong> para este proyecto quedó establecida para el <strong>{{ $cuerpo_correo['fecha_maxima_informe'] }}</strong>. Si no es aprobado, una vez pasada esta fecha el sistema denegará el acceso al proyecto.</p>
            @endif

            @if (isset($cuerpo_correo['nuevo_director_nombre']))
                <br>    
                <p>Nuevo director asignado: </p>
                <ul>
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['nuevo_director_nombre'] }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['nuevo_director_correo'] }}</span></li>
                </ul>
            @endif
        @endif
    @endif

    @if ($tipo_correo === 'fase_0')
    <p>Estimado usuario, en este correo se le informa que se ha realizado la carga de una nueva solicitud para iniciar un proyecto de grado con la siguiente información:</p>

    <ul>
        <li><strong>Tipo de solicitud:</strong> NUEVO PROYECTO DE GRADO {{ $cuerpo_correo['periodo'] }}</li>
        <li><strong>Modalidad del proyecto:</strong> {{ $cuerpo_correo['modalidad'] }}</li>
        <li><strong>Nivel académico:</strong> {{ $cuerpo_correo['nivel'] }}</li>
    </ul>

    <p>Integrantes del proyecto: </p>

    <ul>
        @if (isset($cuerpo_correo['integrante_1']))
            <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_1']->name }}</li>
            <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_1']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_1']->nro_documento }}</li>
            <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_1']->email }}</span></li>
            <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_1']->nro_celular }}</li>
        @endif
        
        @if (isset($cuerpo_correo['integrante_2']))
            <br>
            <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_2']->name }}</li>
            <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_2']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_2']->nro_documento }}</li>
            <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_2']->email }}</span></li>
            <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_2']->nro_celular }}</li>
        @endif

        @if (isset($cuerpo_correo['integrante_3']))
            <br>
            <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_3']->name }}</li>
            <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_3']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_3']->nro_documento }}</li>
            <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_3']->email }}</span></li>
            <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_3']->nro_celular }}</li>
        @endif
    </ul>
    <p><strong>Fecha y hora de carga:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
    <br>
    <p class="uppercase">Se verificará la información suministrada para dar accesso a las siguientes fases del proyecto.</p>
    @endif

    @if ($tipo_correo === 'respuesta_fase_0')
    <p>Estimado usuario, en este correo se le informa que su solicitud para iniciar un proyecto de grado <span>{!! $cuerpo_correo['estado'] === 'Aprobada' ? 'ha pasado a <strong>FASE 1</strong>' : 'ha sido <strong>RECHAZADA</strong>' !!}</span>:</p>

    <ul>
        <li><strong>Tipo de solicitud:</strong> NUEVO PROYECTO DE GRADO {{ $cuerpo_correo['periodo'] }}</li>
        <li><strong>Modalidad del proyecto:</strong> {{ $cuerpo_correo['modalidad'] }}</li>
        <li><strong>Nivel académico:</strong> {{ $cuerpo_correo['nivel'] }}</li>
    </ul>

    <p>Integrantes del proyecto: </p>

    <ul>
        @if (isset($cuerpo_correo['integrante_1']))
            <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_1']->name }}</li>
            <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_1']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_1']->nro_documento }}</li>
            <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_1']->email }}</span></li>
            <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_1']->nro_celular }}</li>
        @endif
        
        @if (isset($cuerpo_correo['integrante_2']))
            <br>
            <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_2']->name }}</li>
            <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_2']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_2']->nro_documento }}</li>
            <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_2']->email }}</span></li>
            <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_2']->nro_celular }}</li>
        @endif

        @if (isset($cuerpo_correo['integrante_3']))
            <br>
            <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_3']->name }}</li>
            <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_3']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_3']->nro_documento }}</li>
            <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_3']->email }}</span></li>
            <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_3']->nro_celular }}</li>
        @endif
    </ul>
    <p><strong>Fecha y de la respuesta a la solicitud:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
    <br>
        @if ($cuerpo_correo['estado'] === 'Aprobada')
            <p>Se le recomienda ingresar al sistema para continuar con las siguientes fases del proyecto en curso.</p>
        @else
            <p>Se le recomienda ingresar al sistema para volver a realizar su solicitud.</p>
        @endif
    @endif

    @if ($tipo_correo === 'fase_1')
    <p>Estimado usuario, en este correo se le informa el envío de su proyecto en <strong>FASE 1</strong>:</p>
    <ul>
        <li><strong>Título: </strong>{{ $cuerpo_correo['titulo'] }}</li>
        <li><strong>Objetivo: </strong> {{ $cuerpo_correo['objetivo'] }}</li>
        <li><strong>Línea de investigacion: </strong> {{ $cuerpo_correo['linea_investigacion'] }}</li>
        <li><strong>Descripción: </strong> @if (isset($cuerpo_correo['descripcion'])) {{ $cuerpo_correo['descripcion'] }} @else No Aplica @endif</li>
        <li><strong>Fecha y hora de envío:</strong> {{ now()->format('d/m/Y H:i:s') }}</li>
    </ul>

    <p>Se le recuerda que la fecha máxima de aprobación del <strong>F-DC-124</strong> para este proyecto es el <strong>{{ $cuerpo_correo['fecha_aprobacion'] }}</strong>. Si no es aprobado, una vez pasada esta fecha el sistema denegará el acceso al proyecto.</p>
    @endif

    @if ($tipo_correo === 'respuesta_fase_1')
        @if ($cuerpo_correo['destinatario'] === 'director')
        <p>Estimado docente, en este correo se le informa que se le ha asignado como <strong class="uppercase">DOCENTE DIRECTOR</strong> del siguiente proyecto:</p>
        @elseif ($cuerpo_correo['destinatario'] === 'evaluador')
        <p>Estimado docente, en este correo se le informa que se le ha asignado como <strong class="uppercase">DOCENTE EVALUADOR</strong> del siguiente proyecto:</p>
        @elseif ($cuerpo_correo['destinatario'] === 'codirector')
        <p>Estimado docente, en este correo se le informa que se le ha asignado como <strong class="uppercase">DOCENTE CODIRECTOR</strong> del siguiente proyecto:</p>
        @else
            <p>Estimado usuario, en este correo se le informa que su proyecto titulado "<strong class="uppercase">{{ $cuerpo_correo['titulo'] }}</strong>", {!! $cuerpo_correo['estado'] == 'Aprobado' ? 'ha pasado a <strong class="uppercase">FASE 2</strong>:' : 'ha sido <strong class="uppercase">APLAZADO</strong>:' !!}</p>
        @endif
        <ul>
            <li><strong>Título: </strong>{{ $cuerpo_correo['titulo'] }}</li>
            <li><strong>Objetivo: </strong> {{ $cuerpo_correo['objetivo'] }}</li>
            <li><strong>Línea de investigacion: </strong> {{ $cuerpo_correo['linea_investigacion'] }}</li>
            <li><strong>Descripción: </strong> @if (isset($cuerpo_correo['descripcion'])) {{ $cuerpo_correo['descripcion'] }} @else No Aplica @endif</li>
            <li><strong>Modalidad del proyecto:</strong> {{ $cuerpo_correo['modalidad'] }}</li>
            <li><strong>Nivel académico:</strong> {{ $cuerpo_correo['nivel'] }}</li>
            <br>
            @if ($cuerpo_correo['estado'] === 'Aprobado')
                <li><strong>Código de modalidad:</strong> {{ $cuerpo_correo['codigo_modalidad'] }}</li>
            @endif
            <li><strong>Acta de registro:</strong> #{{ $cuerpo_correo['nro_acta'] }} del {{ $cuerpo_correo['fecha_acta'] }}</li>
        </ul>

        <p>Integrantes del proyecto: </p>

        <ul>
            @if (isset($cuerpo_correo['integrante_1']))
                <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_1']->name }}</li>
                <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_1']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_1']->nro_documento }}</li>
                <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_1']->email }}</span></li>
                <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_1']->nro_celular }}</li>
            @endif
            
            @if (isset($cuerpo_correo['integrante_2']))
                <br>
                <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_2']->name }}</li>
                <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_2']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_2']->nro_documento }}</li>
                <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_2']->email }}</span></li>
                <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_2']->nro_celular }}</li>
            @endif

            @if (isset($cuerpo_correo['integrante_3']))
                <br>
                <li><strong>Nombre: </strong>{{ $cuerpo_correo['integrante_3']->name }}</li>
                <li><strong>Documento: </strong>{{ $cuerpo_correo['integrante_3']->tipo_documento->tag }} {{ $cuerpo_correo['integrante_3']->nro_documento }}</li>
                <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['integrante_3']->email }}</span></li>
                <li><strong>Celular: </strong>{{ $cuerpo_correo['integrante_3']->nro_celular }}</li>
            @endif
        </ul>
        <p><strong>Fecha y hora de envío:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
        <br>
        @if ($cuerpo_correo['estado'] === 'Aprobado')
            @if ($cuerpo_correo['destinatario'] === 'director')
                @if ($cuerpo_correo['codirector_nombre'] !== null && $cuerpo_correo['codirector_correo'] !== null)
                    <p>Codirector asignado:</p>
                    <ul>
                        <li><strong>Nombre: </strong>{{ $cuerpo_correo['codirector_nombre'] }}</li>
                        <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['codirector_correo'] }}</span></li>
                    </ul>
                @endif
                <br>
                <p>Evaluador asignado:</p>
                <ul>
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['evaluador_nombre'] }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['evaluador_correo'] }}</span></li>
                </ul>
            @elseif ($cuerpo_correo['destinatario'] === 'evaluador')
                <p>Director asignado:</p>
                <ul>
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['director_nombre'] }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['director_correo'] }}</span></li>
                </ul>
                <br>
                @if ($cuerpo_correo['codirector_nombre'] !== null && $cuerpo_correo['codirector_correo'] !== null)
                    <p>Codirector asignado:</p>
                    <ul>
                        <li><strong>Nombre: </strong>{{ $cuerpo_correo['codirector_nombre'] }}</li>
                        <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['codirector_correo'] }}</span></li>
                    </ul>
                @endif
            @elseif ($cuerpo_correo['destinatario'] === 'codirector')
                <p>Director asignado:</p>
                <ul>
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['director_nombre'] }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['director_correo'] }}</span></li>
                </ul>
                <br>
                <p>Evaluador asignado:</p>
                <ul>
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['evaluador_nombre'] }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['evaluador_correo'] }}</span></li>
                </ul>
            @else
                <p>Director asignado:</p>
                <ul>
                    <li><strong>Nombre: </strong>{{ $cuerpo_correo['director_nombre'] }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['director_correo'] }}</span></li>
                </ul>
                <br>
                @if ($cuerpo_correo['codirector_nombre'] !== null && $cuerpo_correo['codirector_correo'] !== null)
                    <p>Codirector asignado:</p>
                    <ul>
                        <li><strong>Nombre: </strong>{{ $cuerpo_correo['codirector_nombre'] }}</li>
                        <li><strong>Correo: </strong><span class="email">{{ $cuerpo_correo['codirector_correo'] }}</span></li>
                    </ul>
                @endif
            @endif
            <br><br><p>Se le recuerda que la fecha máxima de aprobación del <strong>F-DC-124</strong> para este proyecto es el <strong>{{ $cuerpo_correo['fecha_aprobacion'] }}</strong>. Si no es aprobado, una vez pasada esta fecha el sistema denegará el acceso al proyecto. Omita esta notificación si ya se aprobó la propuesta del proyecto.</p>
        @else
            <p>Se le recomienda revisar los comentarios para enviar nuevamente la información.</p>
        @endif
    @endif

    @if ($tipo_correo === 'fase_2')
        <p>Estimado usuario, en este correo se le informa que se ha realizado el envío del proyecto titulado <strong class="uppercase">"{{ $cuerpo_correo['titulo'] }}"</strong>, el cual se encuentra actualmente en <strong>FASE 2</strong>.</p>
        <br>
        <p><strong>NOTA: </strong>El director del proyecto será el encargado de verificar y validar el documento cargado.</p>
        <br>
        <p>Se le recuerda que la fecha máxima de aprobación del <strong>F-DC-124</strong> para este proyecto es el <strong>{{ $cuerpo_correo['fecha_aprobacion'] }}</strong>. Si no es aprobado, una vez pasada esta fecha el sistema denegará el acceso al proyecto.</p>
    @endif

    @if ($tipo_correo === 'respuesta_fase_2')
        <p>Estimado usuario, en este correo se le informa que el proyecto titulado "<strong class="uppercase">{{ $cuerpo_correo['titulo'] }}</strong>" al cual usted se encuentra vinculado, {!! $cuerpo_correo['estado'] === 'Aprobado' ? 'ha pasado a <strong class="uppercase">FASE 3</strong>:' : 'ha sido <strong class="uppercase">APLAZADO</strong>:' !!}</p>
        <p><strong>Fecha y hora de envío:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
        @if ($cuerpo_correo['estado'] === 'Aprobado')
            <br>
            <p><strong>NOTA: </strong>El evaluador del proyecto será el encargado de verificar y validar el documento cargado.</p>
            <br>    
            <p>Se le recuerda que la fecha máxima de aprobación del <strong>F-DC-124</strong> para este proyecto es el <strong>{{ $cuerpo_correo['fecha_aprobacion'] }}</strong>. Si no es aprobado, una vez pasada esta fecha el sistema denegará el acceso al proyecto. Omita esta notificación si ya se aprobó la propuesta del proyecto.</p>
        @else
            <p>Se le recomienda revisar los comentarios y el archivo adjunto para enviar nuevamente la información.</p>
        @endif
    @endif

    @if ($tipo_correo === 'respuesta_fase_3')
        @if ($cuerpo_correo['remitente'] == 0)
            <!-- AQUI EL EVALUADOR HA APROBADO PARA ENVIAR A COMITE  -->
            <p>Estimado usuario, en este correo se le informa que el <strong class="uppercase">DOCENTE EVALUADOR</strong> del proyecto titulado "<strong class="uppercase">{{ $cuerpo_correo['titulo'] }}</strong>", al cual usted está vinculado y que actualmente se encuentra en <strong class="uppercase">FASE 3</strong>, {!! $cuerpo_correo['estado'] === 'Aprobado' ? 'ha enviado el proyecto al <strong class="uppercase">COMITÉ DE TRABAJOS DE GRADO</strong> para verificarlo y pasarlo a la siguiente fase.' : 'ha <strong class="uppercase">APLAZADO</strong> el proyecto.' !!}</p>
            <p><strong>Fecha y hora de envío:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
            <p><strong>Código de modalidad:</strong> {{ $cuerpo_correo['codigo_modalidad'] }}</p>
            <p>Se le recuerda que el <strong class="uppercase">COMITÉ DE TRABAJOS DE GRADO</strong> será el único ente encargado de dar paso a la siguientes fases del proyecto.</p>
        @else
            <!-- AQUI EL COMITÉ HA APROBADO PARA FASE 4  -->
            <p>Estimado usuario, en este correo se le informa que el <strong class="uppercase">COMITÉ DE TRABAJOS DE GRADO</strong> ha revisado el proyecto titulado "<strong class="uppercase">{{ $cuerpo_correo['titulo'] }}</strong>", al cual usted está vinculado y que actualmente se encuentra en <strong class="uppercase">FASE 3</strong>, {!! $cuerpo_correo['estado'] == 'Aprobado' ? 'y lo ha pasado a <strong class="uppercase">FASE 4</strong>.' : 'y lo ha <strong class="uppercase">APLAZADO</strong>.' !!}</p>
            <p><strong>Fecha y hora de envío:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
            <p><strong>Código de modalidad:</strong> {{ $cuerpo_correo['codigo_modalidad'] }}</p>
            @if (isset($cuerpo_correo['nro_acta']) && isset($cuerpo_correo['fecha_acta']))
                <p><strong>Acta de registro:</strong> #{{ $cuerpo_correo['nro_acta'] }} - {{ $cuerpo_correo['fecha_acta'] }}</p>
            @endif
            @if ($cuerpo_correo['estado'] === 'Aprobado')
                <p>Se le recuerda que a partir de hoy los estudiantes cuentan con <strong class="uppercase">{{ config('custom.dias_maximos_informe') }} días</strong> calendario para cargar su <strong class="uppercase">F-DC-125</strong>. Tenga en cuenta que el sistema <strong class="uppercase">SOLO</strong> le permitirá cargar este documento una vez hayan pasado como mínimo <strong class="uppercase">{{ config('custom.dias_minimos_informe') }} días</strong> calendario a partir de hoy. Podrá cargar el <strong>F-DC-125</strong> en el sistema a partir del <strong class="uppercase">{{ $cuerpo_correo['fecha_minima_informe'] }}</strong>, y tendrá como fecha máxima de carga el <strong class="uppercase">{{ $cuerpo_correo['fecha_maxima_informe'] }}</strong>.</p>
            @endif
        @endif
        @if ($cuerpo_correo['estado'] === 'Rechazado')
            <p>Se le recomienda a los estudiantes revisar los comentarios y el archivo adjunto a este correo para enviar nuevamente la propuesta. Recuerde que nuevamente el director, el evaluador y el comité de trabajos de grado deberán revisar la propuesta enviada y aprobarla.</p>
        @endif
    @endif

    @if ($tipo_correo === 'fase_4')
        <p>Estimado usuario, en este correo se le informa que se ha realizado el envío del proyecto titulado <strong class="uppercase">"{{ $cuerpo_correo['titulo'] }}"</strong>, el cual se encuentra actualmente en <strong>FASE 4</strong>.</p>
        <br>
        <p><strong>NOTA: </strong>El director del proyecto será el encargado de verificar y validar el documento cargado.</p>
        <br>
        <p>Se le recuerda que la fecha máxima de aprobación del <strong>F-DC-125</strong> para este proyecto es el <strong>{{ $cuerpo_correo['fecha_maxima_informe'] }}</strong>. Si no es aprobado, una vez pasada esta fecha el sistema denegará el acceso al proyecto.</p>
    @endif

    @if ($tipo_correo === 'respuesta_fase_4')
        <p>Estimado usuario, en este correo se le informa que el proyecto titulado "<strong class="uppercase">{{ $cuerpo_correo['titulo'] }}</strong>" al cual usted se encuentra vinculado, {!! $cuerpo_correo['estado'] === 'Aprobado' ? 'ha pasado a <strong class="uppercase">FASE 5</strong>:' : 'ha sido <strong class="uppercase">APLAZADO</strong>:' !!}</p>
        <p><strong>Fecha y hora de envío:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
        @if ($cuerpo_correo['estado'] === 'Aprobado')    
            <p>Se le recuerda que la fecha máxima de aprobación del <strong>F-DC-125</strong> para este proyecto es el <strong>{{ $cuerpo_correo['fecha_maxima_informe'] }}</strong>. Si no es aprobado, una vez pasada esta fecha el sistema denegará el acceso al proyecto. Omita esta notificación si ya se aprobó el informe del proyecto.</p>
        @else
            <p>Se le recomienda revisar los comentarios y el archivo adjunto para enviar nuevamente la información.</p>
        @endif
    @endif
    
    @if ($tipo_correo === 'respuesta_fase_5')
        @if ($cuerpo_correo['remitente'] == 0)
            <!-- AQUI EL EVALUADOR HA APROBADO PARA ENVIAR A COMITE  -->
            <p>Estimado usuario, en este correo se le informa que el <strong class="uppercase">DOCENTE EVALUADOR</strong> del proyecto titulado "<strong class="uppercase">{{ $cuerpo_correo['titulo'] }}</strong>", al cual usted está vinculado y que actualmente se encuentra en <strong class="uppercase">FASE 5</strong>, {!! $cuerpo_correo['estado'] === 'Aprobado' ? 'ha enviado el proyecto al <strong class="uppercase">COMITÉ DE TRABAJOS DE GRADO</strong> para verificarlo y pasarlo a la siguiente fase.' : 'ha <strong class="uppercase">APLAZADO</strong> el proyecto.' !!}</p>
            <p><strong>Fecha y hora de envío:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
            <p>Se le recuerda que el <strong class="uppercase">COMITÉ DE TRABAJOS DE GRADO</strong> será el único ente encargado de dar paso a la siguientes fases del proyecto.</p>     
        @else
            <!-- AQUI EL COMITÉ HA APROBADO PARA FASE FINAL  -->
            <p>Estimado usuario, en este correo se le informa que el <strong class="uppercase">COMITÉ DE TRABAJOS DE GRADO</strong> ha revisado el proyecto titulado "<strong class="uppercase">{{ $cuerpo_correo['titulo'] }}</strong>", al cual usted está vinculado y que actualmente se encuentra en <strong class="uppercase">FASE 5</strong>, {!! $cuerpo_correo['estado'] == 'Aprobado' ? 'y lo ha pasado a <strong class="uppercase">FASE FINAL</strong>.' : 'y lo ha <strong class="uppercase">APLAZADO</strong>.' !!}</p>
            <p><strong>Fecha y hora de envío:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
            <p><strong>Acta de registro: </strong>#{{  $cuerpo_correo['nro_acta'] }} del {{ $cuerpo_correo['fecha_acta'] }}</p>
            @if ($cuerpo_correo['estado'] === 'Aprobado')
                <p>Se le indica que su proyecto en el sistema ha <strong class="uppercase">FINALIZADO</strong>. A partir de ahora es responsabilidad de los estudiantes, el director y el evaluador programar una fecha para la debida <strong class="uppercase">SUSTENTACIÓN</strong> del proyecto.</p>
                <br>
                <p><strong>NOTA: </strong>Una vez realizada la <strong>SUSTENTACIÓN</strong> del proyecto, los estudiantes deberán acercarse a la coordinación del programa para la respectiva entrega de sus documentos de grado. Si presenta dudas sobre estos documentos podrá realizar consultas a través del correo de la coordinación: <span class="email">{{ config('custom.correo_coordinacion') }}</span>.<br><br>
                Asimismo, se le recuerda al <strong class="uppercase">DIRECTOR DEL PROYECTO</strong> que deberá publicar el proyecto en el repostiorio institucional, para ello podrá seguir el siguiente tutorial: <a href="https://www.youtube.com/watch?v=IBM-tEMNVAw&t=1s" target="_blank" class="text-blue-500 underline">TUTORIAL</a> o tambien podrá consultar la documentación oficial del proceso en <a href="http://repositorio.uts.edu.co:8080/xmlui/handle/123456789/9967" target="_blank" class="text-blue-500 underline">ESTE LINK</a>.</p>
            @endif
        @endif
        @if ($cuerpo_correo['estado'] === 'Rechazado')
            <p>Se le recomienda a los estudiantes revisar los comentarios y el archivo adjunto a este correo para enviar nuevamente el informe. Recuerde que nuevamente el director, el evaluador y el comité de trabajos de grado deberán revisar el informe enviado y aprobarlo.</p>
        @endif
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

    <p>Atentamente,</p>

    <p>Comité de Trabajos de Grado<br>
        Programa de Tecnología en Desarrollo de Sistemas Informáticos e Ingeniería de Sistemas<br>
        Unidades Tecnológicas de Santander</p>
</body>

</html>
