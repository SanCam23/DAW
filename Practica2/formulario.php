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
    <title>Buscar - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/formulario.css">
    <link rel="stylesheet" type="text/css" href="css/print_formulario.css" media="print">
</head>

<body>
    <?php
    $zona = 'publica';
    require('cabecera.php');

    $tipo_anuncio = $_GET["tipo_anuncio"] ?? "";
    $tipo_vivienda = $_GET["tipo_vivienda"] ?? "";
    $ciudad = $_GET["ciudad"] ?? "";
    $pais = $_GET["pais"] ?? "";
    $precio_min = $_GET["precio_min"] ?? "";
    $precio_max = $_GET["precio_max"] ?? "";
    $fecha = $_GET["fecha"] ?? "";
    $errores = [];

    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["validar"])) {

        if ($tipo_anuncio === "") $errores[] = "Debe seleccionar un tipo de anuncio (Venta o Alquiler).";
        if ($tipo_vivienda === "") $errores[] = "Debe seleccionar un tipo de vivienda.";
        if (trim($ciudad) === "") $errores[] = "Debe indicar una ciudad.";
        if (trim($pais) === "") $errores[] = "Debe indicar un país.";
        if ($precio_min !== "" && $precio_max !== "" && $precio_min > $precio_max)
            $errores[] = "El precio mínimo no puede ser mayor que el máximo.";
        if ($fecha !== "") {
            $partes = explode("/", $fecha);
            if (count($partes) !== 3 || !checkdate($partes[1], $partes[0], $partes[2])) {
                $errores[] = "La fecha debe tener formato válido (dd/mm/yyyy).";
            } else {
                $fecha_introducida = mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
                $fecha_actual = time();
                if ($fecha_introducida > $fecha_actual) {
                    $errores[] = "La fecha no puede ser posterior a la actual.";
                }
            }
        }

        if (empty($errores)) {
            $query = http_build_query($_GET);
            header("Location: resultados.php?$query");
            exit();
        }
    }
    ?>

    <main>
        <?php
        if (!empty($errores)) {
            echo "<div class='mensaje-error'>";
            echo "<p><strong>Se han encontrado los siguientes errores:</strong></p><ul>";
            foreach ($errores as $e) {
                echo "<li>$e</li>";
            }
            echo "</ul></div>";
        }
        ?>

        <form action="formulario.php" method="get">
            <input type="hidden" name="validar" value="1">

            <section>
                <h3>Tipo de anuncio</h3>
                <label><input type="radio" name="tipo_anuncio" value="venta" <?php if ($tipo_anuncio === "venta") echo "checked"; ?>> Venta</label>
                <label><input type="radio" name="tipo_anuncio" value="alquiler" <?php if ($tipo_anuncio === "alquiler") echo "checked"; ?>> Alquiler</label>
            </section>

            <section>
                <h3>Tipo de vivienda</h3>
                <label><input type="radio" name="tipo_vivienda" value="obra_nueva" <?php if ($tipo_vivienda === "obra_nueva") echo "checked"; ?>> Obra nueva</label>
                <label><input type="radio" name="tipo_vivienda" value="vivienda" <?php if ($tipo_vivienda === "vivienda") echo "checked"; ?>> Vivienda</label>
                <label><input type="radio" name="tipo_vivienda" value="oficina" <?php if ($tipo_vivienda === "oficina") echo "checked"; ?>> Oficina</label>
                <label><input type="radio" name="tipo_vivienda" value="local" <?php if ($tipo_vivienda === "local") echo "checked"; ?>> Local</label>
                <label><input type="radio" name="tipo_vivienda" value="garaje" <?php if ($tipo_vivienda === "garaje") echo "checked"; ?>> Garaje</label>
            </section>

            <section>
                <h3>Ubicación</h3>
                <label for="ciudad">Ciudad:</label>
                <input type="text" id="ciudad" name="ciudad" value="<?php echo $ciudad; ?>">
                <label for="pais">País:</label>
                <input type="text" id="pais" name="pais" value="<?php echo $pais; ?>">
            </section>

            <section>
                <h3>Precio (€)</h3>
                <label for="precio_min">Mínimo:</label>
                <input type="number" id="precio_min" name="precio_min" min="0" step="1000" value="<?php echo $precio_min; ?>">
                <label for="precio_max">Máximo:</label>
                <input type="number" id="precio_max" name="precio_max" min="0" step="1000" value="<?php echo $precio_max; ?>">
            </section>

            <section>
                <h3>Fecha de publicación</h3>
                <label for="fecha">Desde (dd/mm/yyyy):</label>
                <input type="text" id="fecha" name="fecha" value="<?php echo $fecha; ?>">
            </section>

            <a href="formulario.php">Limpiar</a>
            <button type="submit">Buscar</button>
        </form>
        <?php require_once 'panel_visitados.php'; ?>

    </main>

    <?php include('pie.php'); ?>
</body>

</html>