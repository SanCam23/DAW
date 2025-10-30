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
    <title>Inicio - VENTAPLUS</title>
    <link rel="stylesheet" href="css/general.css" title="Estilo normal">
    <link rel="stylesheet" href="css/index.css">
    <link rel="alternate stylesheet" href="css/contraste_alto.css" title="Alto contraste">
    <link rel="alternate stylesheet" href="css/letra_grande.css" title="Letra Grande">
    <link rel="alternate stylesheet" href="css/contraste_letra.css" title="Letra Grande+Alto contraste">
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print">
    <link rel="stylesheet" href="css/fontello.css">
    <link href="https://fonts.googleapis.com/css2?family=Leckerli+One&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
</head>

<body class="inicio">

    <?php 
        $zona = 'publica';
        include('cabecera.php'); 
    ?>

    <!-- Pop-up de inicio de sesión -->
    <section id="login-popup">
        <h2>Iniciar Sesión</h2>
        <form id="login" action="acceso.php" method="post">
            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario">

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password">

            <button type="submit">Entrar</button>
        </form>
    </section>

    <?php
    session_start();
    if (isset($_SESSION["mensaje_error"])) {
        echo "<p class='mensaje-error'>" . $_SESSION["mensaje_error"] . "</p>";
        unset($_SESSION["mensaje_error"]); // se borra tras mostrarlo
    }
    ?>

    <!-- Contenido principal -->
    <main>
        <!-- Búsqueda rápida -->
        <section id="busqueda-rapida">
            <form action="resultados.php" method="get">
                <input type="text" id="ciudad" name="ciudad" placeholder="Introduce la ciudad donde deseas buscar...">
                <button type="submit"><b><i class="icon-search"></i>Buscar</b></button>
            </form>
        </section>

        <!-- Últimos anuncios -->
        <section id="ultimos-anuncios">
            <h2>Últimos anuncios</h2>

            <section class="anuncio">
                <figure>
                    <img src="./img/completo.jpg" alt="Vivienda en Madrid">
                </figure>
                <h3>Vivienda en Madrid</h3>
                <p>Fecha: 23/09/2025</p>
                <p>Ciudad: Madrid</p>
                <p>Precio: 350.000€</p>
                <a href="detalle_anuncio.php?id=1">Ver detalle</a>
            </section>

            <section class="anuncio">
                <figure>
                    <img src="./img/barcelona.jpeg" alt="Piso en Barcelona">
                </figure>
                <h3>Piso en Barcelona</h3>
                <p>Fecha: 28/08/2025</p>
                <p>Ciudad: Barcelona</p>
                <p>Precio: 180.000€</p>
                <a href="detalle_anuncio.php?id=2">Ver detalle</a>
            </section>

            <section class="anuncio">
                <figure>
                    <img src="./img/sevilla.jpeg" alt="Casa en Sevilla">
                </figure>
                <h3>Casa en Sevilla</h3>
                <p>Fecha: 15/08/2025</p>
                <p>Ciudad: Sevilla</p>
                <p>Precio: 250.000€</p>
                <a href="404.php">Ver detalle</a>
            </section>

            <section class="anuncio">
                <figure>
                    <img src="./img/valencia.jpeg" alt="Apartamento en Valencia">
                </figure>
                <h3>Apartamento en Valencia</h3>
                <p>Fecha: 01/07/2025</p>
                <p>Ciudad: Valencia</p>
                <p>Precio: 220.000€</p>
                <a href="404.php">Ver detalle</a>
            </section>

            <section class="anuncio">
                <figure>
                    <img src="./img/bilbao.jpeg" alt="Estudio en Bilbao">
                </figure>
                <h3>Estudio en Bilbao</h3>
                <p>Fecha: 20/06/2025</p>
                <p>Ciudad: Bilbao</p>
                <p>Precio: 120.000€</p>
                <a href="404.php">Ver detalle</a>
            </section>
        </section>
    </main>

    <?php include('pie.php'); ?>

    <dialog class="modal" id="error-dialog">
        <p id="error-mensaje"></p>
        <button class="cerrar" id="cerrar-error">Cerrar</button>
    </dialog>

    <!-- <script src="js/index.js"></script> -->
</body>
</html>
