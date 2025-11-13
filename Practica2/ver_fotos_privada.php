<?php
require_once __DIR__ . '/db.php';

// 1. Obtener y validar el ID del anuncio de la URL
$id_anuncio = $_GET['id'] ?? 0;
$id_anuncio = (int)$id_anuncio;

if ($id_anuncio <= 0) {
    header("Location: 404.php");
    exit;
}

// 2. Conectar a la BD
$db = conectarDB();
$anuncio = null;
$fotos = [];
$total_fotos = 0;

if ($db) {
    /* 3. Obtener "información básica del anuncio" */
    $sql_anuncio = "SELECT Titulo, Precio FROM ANUNCIOS WHERE IdAnuncio = ?";
    $stmt_anuncio = $db->prepare($sql_anuncio);
    $stmt_anuncio->bind_param("i", $id_anuncio);
    $stmt_anuncio->execute();
    $res_anuncio = $stmt_anuncio->get_result();

    if ($res_anuncio->num_rows > 0) {
        $anuncio = $res_anuncio->fetch_assoc();
    }
    $stmt_anuncio->close();

    /* 4. Obtener "todas las fotos" y el "número total" */
    if ($anuncio) {
        $sql_fotos = "SELECT Foto, Alternativo, Titulo FROM FOTOS WHERE Anuncio = ?";
        $stmt_fotos = $db->prepare($sql_fotos);
        $stmt_fotos->bind_param("i", $id_anuncio);
        $stmt_fotos->execute();
        $res_fotos = $stmt_fotos->get_result();

        $total_fotos = $res_fotos->num_rows; // "número total de fotos"

        if ($total_fotos > 0) {
            $fotos = $res_fotos->fetch_all(MYSQLI_ASSOC); // "Muestra todas las fotos"
        }
        $stmt_fotos->close();
    }

    $db->close();
}

// 5. Si el ID de anuncio no existe, 404
if ($anuncio === null) {
    header("Location: 404.php");
    exit;
}

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
    <title>Fotos de <?php echo htmlspecialchars($anuncio['Titulo']); ?> - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/ver_fotos.css">
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print">
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
            <p><a href="ver_anuncio.php?id=<?php echo $id_anuncio; ?>">&laquo; Volver al anuncio</a></p>
        </section>

        <section class="galeria-ver-fotos">
            <?php if (empty($fotos)): ?>
                <p style="text-align: center; width: 100%;">Este anuncio no tiene fotos en la galería.</p>
            <?php else: ?>
                <?php foreach ($fotos as $foto): ?>
                    <figure>
                        <img src="<?php echo htmlspecialchars($foto["Foto"]); ?>" alt="<?php echo htmlspecialchars($foto["Alternativo"]); ?>">
                        <?php if (!empty($foto['Titulo'])): ?>
                            <figcaption><?php echo htmlspecialchars($foto['Titulo']); ?></figcaption>
                        <?php endif; ?>
                    </figure>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>