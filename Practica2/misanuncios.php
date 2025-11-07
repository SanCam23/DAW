<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Listado de anuncios del usuario en VENTAPLUS.">
    <meta name="keywords" content="mis anuncios, viviendas, pisos, venta, alquiler">
    <meta name="author" content="Santino Campessi Lojo">
    <meta name="author" content="Mario Laguna Contreras">
    <title>Mis anuncios - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print">
</head>

<body>
    <?php
    $zona = 'privada';
    require('cabecera.php');
    require_once 'verificar_sesion.php';
    ?>

    <main>
        <section id="ultimos-anuncios">
            <h2>Mis anuncios</h2>

            <?php
            // Datos simulados del usuario
            $anuncios = [
                1 => [
                    'titulo' => 'Apartamento en Barcelona',
                    'ciudad' => 'Barcelona',
                    'pais' => 'España',
                    'precio' => '180.000 €',
                    'imagen' => 'img/barcelona.jpeg'
                ],
                2 => [
                    'titulo' => 'Vivienda en Madrid',
                    'ciudad' => 'Madrid',
                    'pais' => 'España',
                    'precio' => '350.000 €',
                    'imagen' => 'img/completo.jpg'
                ],
                3 => [
                    'titulo' => 'Casa en Sevilla',
                    'ciudad' => 'Sevilla',
                    'pais' => 'España',
                    'precio' => '250.000 €',
                    'imagen' => 'img/sevilla.jpeg'
                ]
            ];

            foreach ($anuncios as $id => $a) {
                echo "<article class='anuncio'>";
                echo "<figure><img src='" . $a['imagen'] . "' alt='Foto del anuncio'></figure>";
                echo "<h3>" . $a['titulo'] . "</h3>";
                echo "<p><strong>Ciudad:</strong> " . $a['ciudad'] . "</p>";
                echo "<p><strong>País:</strong> " . $a['pais'] . "</p>";
                echo "<p><strong>Precio:</strong> " . $a['precio'] . "</p>";
                echo "<a href='ver_anuncio.php?id=" . $id . "'>Ver anuncio</a>";
                echo "</article>";
            }

            ?>
        </section>

        <p class="centrado">
            <a href="crear_anuncio.php" class="nuevo">+ Crear nuevo anuncio</a>
        </p>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>