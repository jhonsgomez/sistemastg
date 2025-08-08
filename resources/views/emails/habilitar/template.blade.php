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
            <p>Buen día,</p>
            <p>Estimado usuario, En este correo se le informa que el comité de trabajos de grado ha <strong><span class="uppercase">HABILITADO</span></strong> el proyecto titulado <strong><span class="uppercase">{{ $titulo }}</span></strong>, proyecto al cual usted está vinculado actualmente.</p>
            <p>Información del proyecto:</p>
            <ul>
                <li><strong>Titúlo:</strong><span class="uppercase"> {{ $titulo }}</span></li>
                <li><strong>Nivel académico:</strong> {{ $nivel }}</li>
                <li><strong>Modalidad del proyecto:</strong> {{ $modalidad }}</li>
                <li><strong>Acta de registro:</strong> #{{ $acta->numero }} del {{ $acta->fecha }}</li>
            </ul>
            @if (isset($comentarios) && $comentarios !== null)
            <br>
                <p>
                    <strong>Comentarios del comité:</strong>
                </p>
                {!! $comentarios !!}
            <br>
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