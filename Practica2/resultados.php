<?php
$ciudad = $_GET["ciudad"] ?? "";
$pais = $_GET["pais"] ?? "";
$tipo_anuncio = $_GET["tipo_anuncio"] ?? "";
$tipo_vivienda = $_GET["tipo_vivienda"] ?? "";
$precio_min = $_GET["precio_min"] ?? "";
$precio_max = $_GET["precio_max"] ?? "";
$fecha = $_GET["fecha"] ?? "";
?>
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
    <title>Resultados - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/resultados.css">
    <link rel="stylesheet" type="text/css" href="css/print_resultados.css" media="print">
</head>

<body>
    <?php 
        $zona = 'publica';
        include('cabecera.php'); 
    ?>

    <main>
        <h2>Resultados de la búsqueda</h2>

        <?php
        echo "<section id='datos-busqueda'>";
        echo "<h3>Datos de la búsqueda</h3>";
        echo "<ul>";

        if ($ciudad != "") echo "<li><strong>Ciudad:</strong> $ciudad</li>";
        if ($pais != "") echo "<li><strong>País:</strong> $pais</li>";
        if ($tipo_anuncio != "") echo "<li><strong>Tipo de anuncio:</strong> $tipo_anuncio</li>";
        if ($tipo_vivienda != "") echo "<li><strong>Tipo de vivienda:</strong> $tipo_vivienda</li>";
        
        if ($precio_min != "" && $precio_max != "") {
            echo "<li><strong>Rango de precio:</strong> $precio_min € - $precio_max €</li>";
        } elseif ($precio_min != "") {
            echo "<li><strong>Precio mínimo:</strong> $precio_min €</li>";
        } elseif ($precio_max != "") {
            echo "<li><strong>Precio máximo:</strong> $precio_max €</li>";
        }

        if ($fecha != "") echo "<li><strong>Fecha desde:</strong> $fecha</li>";

        echo "</ul>";
        echo "</section>";
        ?>

        <section id="resultados">
            <h3>Anuncios encontrados</h3>

            <article class="destacado">
                <figure>
                    <img src="./img/completo.jpg" alt="Vivienda en Madrid">
                </figure>
                <h4>Vivienda en Madrid</h4>
                <p>Fecha: 23/09/2025</p>
                <p>Ciudad: Madrid</p>
                <p>Precio: 350.000 €</p>
                <a href="detalle_anuncio.php?id=1">Ver detalle</a>
            </article>

            <article class="destacado">
                <figure>
                    <img src="./img/barcelona.jpeg" alt="Piso en Barcelona">
                </figure>
                <h4>Piso en Barcelona</h4>
                <p>Fecha: 28/08/2025</p>
                <p>Ciudad: Barcelona</p>
                <p>Precio: 180.000 €</p>
                <a href="detalle_anuncio.php?id=2">Ver detalle</a>
            </article>

            <article class="destacado">
                <figure>
                    <img src="./img/sevilla.jpeg" alt="Casa en Sevilla">
                </figure>
                <h4>Casa en Sevilla</h4>
                <p>Fecha: 20/08/2025</p>
                <p>Ciudad: Sevilla</p>
                <p>Precio: 150.000 €</p>
                <a href="404.php">Ver detalle</a>
            </article>

            <article class="destacado">
                <figure>
                    <img src="./img/balcon.jpg" alt="Casa en Málaga">
                </figure>
                <h4>Casa en Malaga</h4>
                <p>Fecha: 1/08/2025</p>
                <p>Ciudad: Málaga</p>
                <p>Precio: 170.000€</p>
                <a href="404.php">Ver detalle</a>
            </article>
            <article class="destacado">
                <figure>
                    <img src="./img/salon.jpg" alt="Piso en Castellón">
                </figure>
                <h4>Piso en Castellón</h4>
                <p>Fecha: 10/10/2025</p>
                <p>Ciudad: Castellón</p>
                <p>Precio: 1250.000€</p>
                <a href="404.php">Ver detalle</a>
            </article>
        </section>

        <a href="formulario.php">Volver al formulario de búsqueda</a>
    </main>

    <?php include('pie.php'); ?>
</body>

</html>
