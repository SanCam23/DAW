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
    <title>Crear Anuncio - VENTAPLUS</title>
    <link rel="stylesheet" href="css/general.css" title="Estilo normal">
    <link rel="stylesheet" href="css/crear_anuncio.css">
    <link rel="stylesheet" href="css/fontello.css">
    <link rel="alternate stylesheet" href="css/contraste_alto.css" title="Alto contraste">
    <link rel="alternate stylesheet" href="css/letra_grande.css" title="Letra Grande">
    <link rel="alternate stylesheet" href="css/contraste_letra.css" title="Letra Grande+Alto contraste">
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print">
    <link href="https://fonts.googleapis.com/css2?family=Leckerli+One&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php 
        $zona = 'privada';
        include('cabecera.php'); 
    ?>

    <!-- Contenido principal -->
    <main>
        <h2>Crear un nuevo anuncio</h2>

        <?php
        // Inicializar variables
        $titulo = $_POST["titulo"] ?? "";
        $descripcion = $_POST["descripcion"] ?? "";
        $precio = $_POST["precio"] ?? "";
        $ciudad = $_POST["ciudad"] ?? "";
        $pais = $_POST["pais"] ?? "";
        $tipo = $_POST["tipo"] ?? "";
        $errores = [];

        // Si se envía el formulario
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Validaciones básicas
            if ($titulo == "") $errores[] = "El título es obligatorio.";
            if ($descripcion == "") $errores[] = "La descripción es obligatoria.";
            if ($precio == "" || !is_numeric($precio) || $precio <= 0) $errores[] = "El precio debe ser un número positivo.";
            if ($ciudad == "") $errores[] = "La ciudad es obligatoria.";
            if ($pais == "") $errores[] = "El país es obligatorio.";
            if ($tipo == "") $errores[] = "Debe seleccionar el tipo de anuncio.";

            // Si no hay errores → mostrar resumen del anuncio
            if (empty($errores)) {
                echo "<section class='confirmacion'>";
                echo "<h3>Anuncio creado correctamente</h3>";
                echo "<p>Los datos introducidos son los siguientes:</p>";
                echo "<ul>";
                echo "<li><strong>Título:</strong> $titulo</li>";
                echo "<li><strong>Descripción:</strong> $descripcion</li>";
                echo "<li><strong>Precio:</strong> $precio €</li>";
                echo "<li><strong>Ciudad:</strong> $ciudad</li>";
                echo "<li><strong>País:</strong> $pais</li>";
                echo "<li><strong>Tipo de anuncio:</strong> $tipo</li>";
                echo "</ul>";
                echo "</section>";
            } else {
                // Mostrar lista de errores
                echo "<section class='errores'>";
                echo "<h3>Se han encontrado errores:</h3>";
                echo "<ul>";
                foreach ($errores as $e) {
                    echo "<li>$e</li>";
                }
                echo "</ul>";
                echo "</section>";
            }
        }
        ?>

        <form action="crear_anuncio.php" method="post">
            <fieldset>
                <legend>Datos del anuncio</legend>

                <label for="titulo">Título*:</label><br>
                <input type="text" id="titulo" name="titulo" value="<?php echo $titulo; ?>"><br><br>

                <label for="descripcion">Descripción*:</label><br>
                <textarea id="descripcion" name="descripcion" rows="5" cols="40"><?php echo $descripcion; ?></textarea><br><br>

                <label for="precio">Precio (€)*:</label><br>
                <input type="number" id="precio" name="precio" min="0" value="<?php echo $precio; ?>"><br><br>

                <label for="ciudad">Ciudad*:</label><br>
                <input type="text" id="ciudad" name="ciudad" value="<?php echo $ciudad; ?>"><br><br>

                <label for="pais">País*:</label><br>
                <input type="text" id="pais" name="pais" value="<?php echo $pais; ?>"><br><br>

                <label for="tipo">Tipo de anuncio*:</label><br>
                <select id="tipo" name="tipo">
                    <option value="">Seleccione...</option>
                    <option value="venta" <?php if ($tipo == "venta") echo "selected"; ?>>Venta</option>
                    <option value="alquiler" <?php if ($tipo == "alquiler") echo "selected"; ?>>Alquiler</option>
                </select><br><br>

                <button type="submit">Crear anuncio</button>
                <button type="reset">Borrar</button>
            </fieldset>
        </form>
    </main>

    <?php include('pie.php'); ?>
    
</body>

</html>