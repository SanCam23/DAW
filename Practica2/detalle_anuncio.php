<?php
include 'anuncios.php'; // Cargamos los datos de los anuncios

// Recogemos el ID del anuncio desde la URL
$id = $_GET['id'] ?? 1; // Si no hay id, por defecto usamos 1

// Si el ID es par, mostramos el anuncio 2; si es impar, el 1
$anuncio = ($id % 2 == 0) ? $anuncios[2] : $anuncios[1];
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
    <title><?= $anuncio["titulo"]; ?> - VENTAPLUS</title>
    <link rel="stylesheet" href="css/general.css" title="Estilo normal">
    <link rel="stylesheet" href="css/anuncio.css">
    <link rel="alternate stylesheet" href="css/contraste_alto.css" title="Alto contraste">
    <link rel="alternate stylesheet" href="css/letra_grande.css" title="Letra Grande">
    <link rel="alternate stylesheet" href="css/contraste_letra.css" title="Letra Grande+Alto contraste">
    <link rel="stylesheet" href="css/fontello.css">
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print">
    <link href="https://fonts.googleapis.com/css2?family=Leckerli+One&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
</head>

<body>
    <header class="Cabecera">
        <section class="texto">
            <figure>
                <img src="logo.png" alt="Logo">
            </figure>
            <section class="titulo">
                <h1>VENTAPLUS</h1>
                <h3>¿Buscas tu próximo hogar? Empieza aquí.</h3>
            </section>
        </section>

        <nav class="menu-escritorio">
            <ul>
                <li><a href="index.php"><i class="icon-home"></i>Inicio</a></li>
                <li><a href="formulario.php"><i class="icon-search"></i>Buscar</a></li>
                <li><a href="menu_usuario_registrado.php"><i class="icon-user"></i>Mi Perfil</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <article>
            <!-- Columna izquierda -->
            <section class="col-izquierda">
                <h2><?= $anuncio["titulo"]; ?></h2>

                <figure class="foto-principal">
                    <img src="<?= $anuncio["foto_principal"]; ?>" alt="Foto principal del anuncio">
                </figure>

                <section class="galeria">
                    <h3>Galería de imágenes</h3>
                    <?php foreach ($anuncio["fotos"] as $foto): ?>
                        <figure><img src="<?= $foto; ?>" alt="Imagen del anuncio"></figure>
                    <?php endforeach; ?>
                </section>
            </section>

            <!-- Columna derecha -->
            <section class="col-derecha">
                <section class="info-general">
                    <h3>Información general</h3>
                    <p><strong><?= $anuncio["titulo"]; ?></strong></p>
                    <p>Tipo de anuncio: <?= $anuncio["tipo_anuncio"]; ?></p>
                    <p>Tipo de vivienda: <?= $anuncio["tipo_vivienda"]; ?></p>
                    <p>Fecha: <?= $anuncio["fecha"]; ?></p>
                    <p>Ciudad: <?= $anuncio["ciudad"]; ?></p>
                    <p>País: <?= $anuncio["pais"]; ?></p>
                    <p>Precio: <?= $anuncio["precio"]; ?></p>
                </section>

                <section class="descripcion">
                    <h3>Descripción</h3>
                    <p><?= $anuncio["descripcion"]; ?></p>
                </section>

                <section class="caracteristicas">
                    <h3>Características</h3>
                    <ul>
                        <?php foreach ($anuncio["caracteristicas"] as $caracteristica): ?>
                            <li><?= $caracteristica; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>

                <p><strong>Publicado por:</strong> <?= $anuncio["usuario"]; ?></p>
            </section>
        </article>

        <section class="contacto">
            <h3>Enviar mensaje</h3>
            <p><a href="enviar.html">Ir al formulario de contacto</a></p>
        </section>
    </main>

    <?php include('pie.php'); ?>
</body>
</html>
