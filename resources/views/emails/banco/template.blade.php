<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Idea para Banco</title>
    <style>
        body {
            font-family: Calibri, sans-serif;
        }
    </style>
</head>

<body>
    <p>Buen día,</p><br>

    @if ($tipo_correo === 'propuesta')
        <p>Estimado usuario, en este correo se le informa que se ha realizado la carga o modificación de una idea para el banco con la siguiente información:</p>

        <ul>
            <li><strong>Tipo de solicitud:</strong> NUEVA IDEA PARA BANCO {{ $cuerpo_correo['periodo_academico'] }}</li>
            <li><strong>Docente encargado:</strong> {{ $cuerpo_correo['nombre'] }}</li>
            <li><strong>Título de la idea:</strong> {{ $cuerpo_correo['titulo'] }}</li>
            <li><strong>Modalidad de la idea:</strong> {{ $cuerpo_correo['modalidad'] }}</li>
            <li><strong>Línea de investigación:</strong> {{ $cuerpo_correo['linea_investigacion'] }}</li>
            <li><strong>Nivel académico:</strong> {{ $cuerpo_correo['nivel'] }}</li>
            <li><strong>Fecha y hora de carga:</strong> {{ now()->format('d/m/Y H:i:s') }}</li>
        </ul>
    @endif


    @if ($tipo_correo === 'respuesta')
        <p>Estimado usuario, en este correo se le informa que la siguiente idea para banco ha sido <strong style="text-transform: uppercase;">{{ $cuerpo_correo['estado_solicitud'] }}</strong>:</p>

        <ul>
            <li><strong>Tipo de solicitud:</strong> NUEVA IDEA PARA BANCO {{ $cuerpo_correo['periodo_academico'] }}</li>
            <li><strong>Docente encargado:</strong> {{ $cuerpo_correo['nombre'] }}</li>
            <li><strong>Título de la idea:</strong> {{ $cuerpo_correo['titulo'] }}</li>
            <li><strong>Modalidad de la idea:</strong> {{ $cuerpo_correo['modalidad'] }}</li>
            <li><strong>Línea de investigación:</strong> {{ $cuerpo_correo['linea_investigacion'] }}</li>
            <li><strong>Nivel académico:</strong> {{ $cuerpo_correo['nivel'] }}</li>
            <li><strong>Fecha y hora de carga:</strong> {{ now()->format('d/m/Y H:i:s') }}</li>
        </ul>
    @endif


    @if ($comentarios)
    <br>
    <p>Comentarios del lider de investigación:</p>
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