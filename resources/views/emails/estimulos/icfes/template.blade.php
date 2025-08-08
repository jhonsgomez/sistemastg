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
    <main>
        <div>
            @if($tipo_destinatario === 'solicitud_estudiante')
                <div>
                    <p>Buen día,</p>
                    <p>Estimado estudiante <strong><span class="uppercase">{{ $estudiante->name }}</span></strong>. En este correo se le informa que usted ha realizado la siguiente solicitud en su proyecto:</p>
                    <ul>
                        <li><strong>Tipo de solicitud:</strong> SOLICITUD DE ESTÍMULO POR SABER TYT/PRO</li>
                        <li><strong>Titúlo:</strong><span class="uppercase"> {{ $titulo }}</span></li>
                        <li><strong>Nivel académico:</strong> {{ $nivel }}</li>
                        <li><strong>Modalidad del proyecto:</strong> {{ $modalidad }}</li>
                    </ul>
                    <p>Información del estudiante solicitante:</p>
                    <ul>
                        <li><strong>Nombre:</strong> <span class="uppercase">{{ $estudiante->name }}</span></li>
                        <li><strong>Documento:</strong> {{ $estudiante->tipo_documento->tag }} {{ $estudiante->nro_documento }}</li>
                        <li><strong>Teléfono:</strong> {{ $estudiante->nro_celular }}</li>
                        <li><strong>Email: </strong><span class="email">{{ $estudiante->email }}</span></li>
                    </ul>
                    <p>El comité de tabajos de grado, revisará la información enviada y dará respuesta a su solicitud.</p>
                </div>
            @endif
            @if ($tipo_destinatario === 'respuesta_estudiante')
                <div>
                    <p>Buen día,</p>
                    <p>Estimado estudiante, En este correo se le informa que el comité de trabajos de grado ha <strong><span class="uppercase">{{ $estado_solicitud }}</span></strong> una solicitud de tipo <strong>ESTIMULO ICFES SABER PRO/TYT</strong> en el proyecto titulado <strong><span class="uppercase">{{ $titulo }}</span></strong>, proyecto al cual usted está vinculado actualmente.</p>
                    @if ($estado_solicitud === 'Aprobado')
                        <p>A partir de este momento unicamente el/la estudiante <strong><span class="uppercase">{{ $estudiante->name }}</span></strong> quedará eximido de la entrega del informe final (F-DC-125) del proyecto, tenga en cuenta que si en el proyecto hay más de un integrante, en el sistema le seguirá apareciendo el progreso del proyecto hasta que los demás integrantes culminen el desarrollo del mismo, se le recomienda comunicarse con el comité de trabajos de grado para consultar el paso a seguir del estudiante beneficiario.</p>
                    @endif
                    <p>Información de la solicitud: </p>
                    <ul>
                        <li><strong>Acta de registro: </strong>#{{ $acta->numero }} del {{ $acta->fecha }}</li>
                    </ul>
                    @if (isset($comentarios_comite) && $comentarios_comite !== null)
                        <br>
                        <p><strong>Comentarios del comité:</strong></p>
                        {!! $comentarios_comite !!}
                        <br>
                    @endif
                </div>
            @endif
            @if ($tipo_destinatario === 'respuesta_docentes')
                <div>
                    <p>Buen día,</p>
                    <p>Estimado usuario, En este correo se le informa que el <strong><span class="uppercase">COMITÉ DE TRABAJOS DE GRADO</span></strong> ha <strong><span class="uppercase">{{ $estado_solicitud }}</span></strong> la solicitud de <strong>ESTIMULO ICFES SABER PRO/TYT</strong> para el/la estudiante <strong><span class="uppercase">{{ $estudiante->name }}</span></strong>, el cual hace parte del proyecto titulado <strong><span class="uppercase">{{ $titulo }}</span></strong>.</p>
                    <p>A partir de este momento unicamente el/la estudiante <strong><span class="uppercase">{{ $estudiante->name }}</span></strong> quedará eximido de la entrega del informe final (F-DC-125) del proyecto, tenga en cuenta que si en el proyecto hay más de un integrante, deberá seguir el procedimiento normal en el sistema para culminar el desarrollo del proyecto para los demás integrantes.</p>
                    <p>Información de la solicitud: </p>
                    <ul>
                        <li><strong>Acta de registro: </strong>#{{ $acta->numero }} del {{ $acta->fecha }}</li>
                    </ul>
                    @if (isset($comentarios_comite) && $comentarios_comite !== null)
                        <br>
                        <p><strong>Comentarios del comité:</strong></p>
                        {!! $comentarios_comite !!}
                        <br>
                    @endif
                </div>
            @endif
            @if ($tipo_destinatario === "finalizacion_estudiantes" || $tipo_destinatario === "finalizacion_docentes")
                <div>
                    <p>Buen día,</p>
                    <p>Estimado usuario, en este correo se le informa que el <strong><span class="uppercase">COMITÉ DE TRABAJOS DE GRADO</span></strong> ha finalizado el proyecto titulado <strong><span class="uppercase">{{ $titulo }}</span></strong>, proyecto al cual usted está vinculado actualmente. El proyecto ha sido finalizado debido a que todos los integrantes del proyecto han obtenido el beneficio de <strong><span class="uppercase">ESTIMULO ICFES SABER PRO/TYT</span></strong>.</p>
                    <p>Se le recuerda que gracias a este beneficio todos los integrantes quedan exentos de la entrega del informe final del proyecto (F-DC-125)</p>
                    <p>En el sistema podrá apreciar el estado del proyecto. Se le recomienda comunicarse con el comité en caso de presentar alguna inquietud sobre el proyecto.</p>
                    <p>Información del proyecto:</p>
                    <ul>
                        <li><strong>Titúlo:</strong><span class="uppercase"> {{ $titulo }}</span></li>
                        <li><strong>Nivel académico:</strong> {{ $nivel }}</li>
                        <li><strong>Modalidad del proyecto:</strong> {{ $modalidad }}</li>
                        <li><strong>Acta de registro:</strong> #{{ $acta->numero }} del {{ $acta->fecha }}</li>
                    </ul>
                    <p>Información de los integrantes:</p>
                    @foreach ($integrantes as $integrante)
                        <ul>
                            <li><strong>Nombre:</strong> <span class="uppercase">{{ $integrante->name }}</span></li>
                            <li><strong>Documento:</strong> {{ $integrante->tipo_documento->tag }} {{ $integrante->nro_documento }}</li>
                            <li><strong>Teléfono:</strong> {{ $integrante->nro_celular }}</li>
                            <li><strong>Email: </strong><span class="email">{{ $integrante->email }}</span></li>
                        </ul>
                        <br>
                    @endforeach
                    @if (isset($comentarios_comite) && $comentarios_comite !== null)
                        <p><strong>Comentarios del comité:</strong></p>
                        {!! $comentarios_comite !!}
                        <br>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <br>
            <p>Esto es un correo generado automáticamente por el sistema de trabajos de grado del programa, favor no responder al mismo.</p>
            <br>
            <br>
        </div>

        <div>
            <br>
            <p>Atentamente,</p>

            <p>Comité de Trabajos de Grado<br>
                Programa de Tecnología en Desarrollo de Sistemas Informáticos e Ingeniería de Sistemas<br>
                Unidades Tecnológicas de Santander</p>
        </div>
    </main>
</body>

</html>