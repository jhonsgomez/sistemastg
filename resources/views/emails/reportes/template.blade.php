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
    <p>Buen día,</p><br>

    <p>Estimado usuario, en este correo se le informa que se ha realizado un PQRSD con el siguiente contenido:</p>
    <br>

    @if (isset($cuerpo_correo['mensaje']))
        <br>
        <p><strong>Comentarios del reporte:</strong></p>
        <p>{!! $cuerpo_correo['mensaje'] !!}</p>
    @endif

    <br>
    <p>Agradecemos todos sus comentarios, los cuales nos permiten mejorar el servicio.</p>

    <div>
        <br>
        <p>Esto es un correo generado automáticamente por el sistema de trabajos de grado del programa, favor no responder al mismo.</p>
        <br>
        <br>
    </div>

    <br>
    <br>
    <p>Atentamente,</p>

    <p>Software - Trabajos de grado<br>
        Programa de Tecnología en Desarrollo de Sistemas Informáticos e Ingeniería de Sistemas<br>
        Unidades Tecnológicas de Santander</p>
</body>

</html>