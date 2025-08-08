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
            <div>
                <p>Buen día,</p>
                <p>Estimado docente {{ $perfil }}, en este correo se le recuerda que usted actualmente está vinculado al proyecto titulado <strong class="uppercase">"{{ isset($titulo) ? $titulo : 'No Específicado' }}"</strong>, el cual se encuentra en <strong class="uppercase">{{ $proyecto->estado }}</strong>, y que aún <strong>NO</strong> se ha revisado el <strong class="uppercase">{{ $type ?? 'No Especificado' }}</strong>:</p>
                <ul>
                    <li><strong>Tipo de solicitud:</strong> PROYECTO DE GRADO</li>
                    <li><strong>Nivel académico:</strong> {{ $nivel }}</li>
                    <li><strong>Modalidad del proyecto:</strong> {{ $modalidad }}</li>
                    <li><strong>Código de modalidad:</strong> {{ $codigo_modalidad }}</li>
                    <li><strong>Periodo Académico:</strong> {{ $periodo }}</li>
                </ul>
            </div>
            <div>
                <p>Integrantes del proyecto: </p>

                <ul>
                    @if (isset($integrante_1))
                        <li><strong>Nombre: </strong>{{ $integrante_1->name }}</li>
                        <li><strong>Documento: </strong>{{ $integrante_1->tipo_documento->tag }} {{ $integrante_1->nro_documento }}</li>
                        <li><strong>Correo: </strong><span class="email">{{ $integrante_1->email }}</span></li>
                        <li><strong>Celular: </strong>{{ $integrante_1->nro_celular }}</li>
                    @endif

                    @if (isset($integrante_2))
                    <br>
                    <li><strong>Nombre: </strong>{{ $integrante_2->name }}</li>
                    <li><strong>Documento: </strong>{{ $integrante_2->tipo_documento->tag }} {{ $integrante_2->nro_documento }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $integrante_2->email }}</span></li>
                    <li><strong>Celular: </strong>{{ $integrante_2->nro_celular }}</li>
                    @endif

                    @if (isset($integrante_3))
                    <br>
                    <li><strong>Nombre: </strong>{{ $integrante_3->name }}</li>
                    <li><strong>Documento: </strong>{{ $integrante_3->tipo_documento->tag }} {{ $integrante_3->nro_documento }}</li>
                    <li><strong>Correo: </strong><span class="email">{{ $integrante_3->email }}</span></li>
                    <li><strong>Celular: </strong>{{ $integrante_3->nro_celular }}</li>
                    @endif
                </ul>
            </div>
            <br>
            <p>Recuerde que este proyecto tiene como fecha máxima de aprobación y/o entrega del <strong>{{ $type ?? 'No Especificado' }}</strong> el <strong>{{ $fecha_maxima ?? 'No Especificado' }}</strong>. Si no se aprueba, una vez superada esta fecha el sistema inhabilitará el proyecto.</p>
            <br>
            <p><strong>NOTA: </strong>Si el proyecto ya fue revisado por favor omita el contenido de este correo.</p>
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