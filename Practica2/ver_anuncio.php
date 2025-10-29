<?php
// Datos simulados de todos los anuncios
$anuncios = [
    1 => [
        'titulo' => 'Apartamento en Barcelona',
        'tipo_anuncio' => 'Venta',
        'tipo_vivienda' => 'Apartamento',
        'precio' => '180.000 €',
        'fecha' => '28/08/2025',
        'ciudad' => 'Barcelona',
        'pais' => 'España',
        'descripcion' => 'Acogedor apartamento de 60 m² en el centro de Barcelona. Dispone de 2 habitaciones, 1 baño, salón y cocina equipada.',
        'imagen_principal' => 'img/barcelona.jpeg',
        'galeria' => ['img/salon.jpg', 'img/balcon.jpg'],
        'caracteristicas' => ['2 habitaciones', '1 baño', 'Cocina equipada', 'Salón']
    ],
    2 => [
        'titulo' => 'Vivienda en Madrid',
        'tipo_anuncio' => 'Venta',
        'tipo_vivienda' => 'Piso',
        'precio' => '350.000 €',
        'fecha' => '23/09/2025',
        'ciudad' => 'Madrid',
        'pais' => 'España',
        'descripcion' => 'Piso moderno de 90 m² en el centro de Madrid con 2 habitaciones, 2 baños, salón luminoso y cocina equipada.',
        'imagen_principal' => 'img/completo.jpg',
        'galeria' => ['img/salon.jpg', 'img/balcon.jpg'],
        'caracteristicas' => ['2 habitaciones', '2 baños', 'Cocina equipada', 'Balcón', 'Salón']
    ]
];

// Obtener el id por GET
$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Comprobar que el anuncio existe
if (!isset($anuncios[$id])) {
    echo "Anuncio no encontrado.";
    exit;
}

$anuncio = $anuncios[$id];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Listado de anuncios del usuario en VENTAPLUS.">
    <meta name="keywords" content="mis anuncios, viviendas, pisos, venta, alquiler">
    <meta name="author" content="Tu nombre">
    <title>Mis anuncios - VENTAPLUS</title>
    <link rel="stylesheet" href="css/general.css" title="Estilo normal">
    <link rel="stylesheet" href="css/ver_anuncio.css">
    <link rel="alternate stylesheet" href="css/contraste_alto.css" title="Alto contraste">
    <link rel="alternate stylesheet" href="css/letra_grande.css" title="Letra Grande">
    <link rel="alternate stylesheet" href="css/contraste_letra.css" title="Letra Grande+Alto contraste">
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print">
    <link rel="stylesheet" href="css/fontello.css">
    <link href="https://fonts.googleapis.com/css2?family=Leckerli+One&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
</head>
<body>
    <?php include('cabecera.php'); ?>

<main>
    <article>
        <section class="col-izquierda">
            <h2><?php echo htmlspecialchars($anuncio['titulo']); ?></h2>
            <figure class="foto-principal">
                <img src="<?php echo htmlspecialchars($anuncio['imagen_principal']); ?>" alt="Foto principal del anuncio">
            </figure>

            <section class="galeria">
                <h3>Galería de imágenes</h3>
                <?php foreach ($anuncio['galeria'] as $img): ?>
                    <figure>
                        <img src="<?php echo htmlspecialchars($img); ?>" alt="Imagen del anuncio">
                    </figure>
                <?php endforeach; ?>
            </section>
        </section>

        <section class="col-derecha">
            <section class="info-general">
                <h3>Información general</h3>
                <p>Tipo de anuncio: <?php echo htmlspecialchars($anuncio['tipo_anuncio']); ?></p>
                <p>Tipo de vivienda: <?php echo htmlspecialchars($anuncio['tipo_vivienda']); ?></p>
                <p>Fecha: <?php echo htmlspecialchars($anuncio['fecha']); ?></p>
                <p>Ciudad: <?php echo htmlspecialchars($anuncio['ciudad']); ?></p>
                <p>País: <?php echo htmlspecialchars($anuncio['pais']); ?></p>
                <p>Precio: <?php echo htmlspecialchars($anuncio['precio']); ?></p>
            </section>

            <section class="descripcion">
                <h3>Descripción</h3>
                <p><?php echo htmlspecialchars($anuncio['descripcion']); ?></p>
            </section>

            <section class="caracteristicas">
                <h3>Características</h3>
                <ul>
                    <?php foreach ($anuncio['caracteristicas'] as $caracteristica): ?>
                        <li><?php echo htmlspecialchars($caracteristica); ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <p><a href="añadir_foto.php?id=<?php echo $id; ?>">Añadir foto a este anuncio</a></p>
        </section>
    </article>
</main>

    <?php include('pie.php'); ?>
</body>
</html>
