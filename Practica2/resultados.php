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
    <link rel="stylesheet" href="css/general.css" title="Estilo normal">
    <link rel="stylesheet" href="css/resultados.css">
    <link rel="alternate stylesheet" href="css/contraste_alto.css" title="Alto contraste">
    <link rel="alternate stylesheet" href="css/letra_grande.css" title="Letra Grande">
    <link rel="alternate stylesheet" href="css/contraste_letra.css" title="Letra Grande+Alto contraste">
    <link rel="stylesheet" type="text/css" href="css/print_resultados.css" media="print">
    <link rel="stylesheet" href="css/fontello.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Leckerli+One&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php 
        $zona = 'publica';
        include('cabecera.php'); 
    ?>

    <main>
        <h2>Resultados de la búsqueda</h2>

        <?php
        // Recoger los datos enviados por GET (desde formulario o búsqueda rápida)
        $ciudad = $_GET["ciudad"] ?? "";
        $pais = $_GET["pais"] ?? "";
        $tipo_anuncio = $_GET["tipo_anuncio"] ?? "";
        $tipo_vivienda = $_GET["tipo_vivienda"] ?? "";
        $precio_min = $_GET["precio_min"] ?? "";
        $precio_max = $_GET["precio_max"] ?? "";
        $fecha = $_GET["fecha"] ?? "";

        // Mostrar los datos introducidos
        echo "<section>";
        echo "<h3>Datos introducidos</h3>";
        echo "<ul>";

        if ($ciudad != "") echo "<li><strong>Ciudad:</strong> $ciudad</li>";
        if ($pais != "") echo "<li><strong>País:</strong> $pais</li>";
        if ($tipo_anuncio != "") echo "<li><strong>Tipo de anuncio:</strong> $tipo_anuncio</li>";
        if ($tipo_vivienda != "") echo "<li><strong>Tipo de vivienda:</strong> $tipo_vivienda</li>";
        if ($precio_min != "" && $precio_max != "") echo "<li><strong>Rango de precio:</strong> $precio_min € - $precio_max €</li>";
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

            <article id="destacado">
                <figure>
                    <img src="./img/balcon.jpg" alt="Casa en Málaga">
                </figure>
                <h3>Casa en Malaga</h3>
                <p>Fecha: 1/08/2025</p>
                <p>Ciudad: Málaga</p>
                <p>Precio: 170.000€</p>
                <a href="404.php">Ver detalle</a>
            </article>
            <article id="destacado">
                <figure>
                    <img src="./img/salon.jpg" alt="Piso en Castellón">
                </figure>
                <h3>Piso en Castellón</h3>
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