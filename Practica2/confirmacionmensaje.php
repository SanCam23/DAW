<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="VENTAPLUS: portal de anuncios de venta y alquiler de viviendas. Busca tu próximo hogar fácilmente.">
    <meta name="keywords" content="viviendas, pisos, casas, alquiler, compra, venta, inmuebles">
    <meta name="author" content="Santino Campessi Lojo">
    <meta name="author" content="Mario Laguna Contreras">
    <title>Confirmación Mensaje - VENTAPLUS</title>
    <link rel="stylesheet" href="css/general.css" title="Estilo normal">
    <link rel="stylesheet" href="css/confirmacionmensaje.css">
    <link rel="alternate stylesheet" href="css/contraste_alto.css" title="Alto contraste">
    <link rel="alternate stylesheet" href="css/letra_grande.css" title="Letra Grande">
    <link rel="alternate stylesheet" href="css/contraste_letra.css" title="Letra Grande+Alto contraste">
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print">
    <link rel="stylesheet" href="css/fontello.css">
    <link href="https://fonts.googleapis.com/css2?family=Leckerli+One&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
</head>

<body>
    <!-- Cabecera -->
    <?php include('cabecera.php'); ?>

    <main>
        <article>
            <?php
            // --- Validación de datos ---
            $tipo_valido = ['informacion', 'cita', 'oferta'];

            $tipo = $_POST['tipo'] ?? '';
            $mensaje = trim($_POST['mensaje'] ?? '');

            // Comprobamos errores
            $errores = [];

            if (!in_array($tipo, $tipo_valido)) {
                $errores[] = "Tipo de mensaje no válido.";
            }

            if ($mensaje === '') {
                $errores[] = "El campo del mensaje no puede estar vacío.";
            }

            if (empty($errores)) {
                echo "<section>";
                echo "<h2>Mensaje enviado correctamente</h2>";
                echo "<p>Tu mensaje ha sido enviado correctamente.</p>";
                echo "</section>";

                echo "<section>";
                echo "<h3>Datos enviados</h3>";

                // Convertimos el tipo en texto legible
                $texto_tipo = [
                    'informacion' => 'Más información',
                    'cita' => 'Solicitar una cita',
                    'oferta' => 'Comunicar una oferta'
                ];

                echo "<p><strong>Tipo de mensaje:</strong> " . $texto_tipo[$tipo] . "</p>";
                echo "<p><strong>Mensaje:</strong> " . htmlspecialchars($mensaje) . "</p>";
                echo "</section>";
            } else {
                echo "<section>";
                echo "<h2>Error al enviar el mensaje</h2>";
                echo "<ul>";
                foreach ($errores as $error) {
                    echo "<li>" . htmlspecialchars($error) . "</li>";
                }
                echo "</ul>";
                echo "<p><a href='enviarmensaje.php'>Volver al formulario</a></p>";
                echo "</section>";
            }
            ?>
        </article>
    </main>

    <?php include('pie.php'); ?>
</body>
</html>
