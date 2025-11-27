<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/funciones_fotos.php';

$id_anuncio = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_anuncio <= 0) {
    header("Location: 404.php");
    exit;
}

$db = conectarDB();
$datos = null;

if ($db) {
    // Obtener datos de la galería
    $datos = obtenerDatosGaleria($db, $id_anuncio);
    $db->close();
}

if ($datos === null) {
    header("Location: 404.php");
    exit;
}

$anuncio = $datos['anuncio'];
$fotos = $datos['fotos'];
$total_fotos = $datos['total'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Fotos de <?php echo htmlspecialchars($anuncio['Titulo']); ?> - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/ver_fotos.css">
</head>

<body>
    <?php
    $zona = 'publica';
    require('cabecera.php');
    ?>

    <main>
        <section class="info-basica">
            <h2><?php echo htmlspecialchars($anuncio['Titulo']); ?></h2>
            <p>Precio: <?php echo number_format($anuncio['Precio'], 0, ',', '.'); ?> €</p>
            <p><strong>Mostrando <?php echo $total_fotos; ?> fotos.</strong></p>
            <br>
            <p><a href="detalle_anuncio.php?id=<?php echo $id_anuncio; ?>">&laquo; Volver al anuncio</a></p>
        </section>

        <?php
        // Renderizar galería en modo NO edición
        renderizarGaleria($fotos, false);
        ?>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>