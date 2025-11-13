<?php
session_start();
require_once 'verificar_sesion.php';
require_once __DIR__ . '/db.php';

// Obtener ID del anuncio desde la URL
$id_anuncio = $_GET['id'] ?? 0;
$id_anuncio = (int)$id_anuncio;

if ($id_anuncio <= 0) {
    echo "Anuncio no encontrado.";
    exit;
}

// Conectar a la BD y obtener datos del anuncio
$db = conectarDB();
$anuncio = null;
$fotos = [];

if ($db && isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];

    // Consulta que verifica que el anuncio pertenece al usuario logueado
    $sql = "SELECT a.*, p.NomPais, u.NomUsuario, ta.NomTAnuncio, tv.NomTVivienda
            FROM ANUNCIOS a
            LEFT JOIN PAISES p ON a.Pais = p.IdPais
            LEFT JOIN USUARIOS u ON a.Usuario = u.IdUsuario
            LEFT JOIN TIPOSANUNCIOS ta ON a.TAnuncio = ta.IdTAnuncio
            LEFT JOIN TIPOSVIVIENDAS tv ON a.TVivienda = tv.IdTVivienda
            WHERE a.IdAnuncio = ? AND a.Usuario = ?";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("ii", $id_anuncio, $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows > 0) {
        $anuncio = $resultado->fetch_assoc();
    }
    $stmt->close();

    // Obtener fotos de la galería si el anuncio existe y pertenece al usuario
    if ($anuncio) {
        $sql_fotos = "SELECT Foto, Alternativo, Titulo FROM FOTOS WHERE Anuncio = ?";
        $stmt_fotos = $db->prepare($sql_fotos);
        $stmt_fotos->bind_param("i", $id_anuncio);
        $stmt_fotos->execute();
        $res_fotos = $stmt_fotos->get_result();
        if ($res_fotos->num_rows > 0) {
            $fotos = $res_fotos->fetch_all(MYSQLI_ASSOC);
        }
        $stmt_fotos->close();
    }

    $db->close();
}

// Si el anuncio no existe o no pertenece al usuario
if ($anuncio === null) {
    echo "Anuncio no encontrado o no tienes permisos para verlo.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Detalle del anuncio del usuario en VENTAPLUS.">
    <meta name="keywords" content="mis anuncios, viviendas, pisos, venta, alquiler">
    <meta name="author" content="Santino Campessi Lojo">
    <meta name="author" content="Mario Laguna Contreras">
    <title><?php echo $anuncio['Titulo']; ?> - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/ver_anuncio.css">
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print">
</head>

<body>
    <?php
    $zona = 'privada';
    require('cabecera.php');
    ?>

    <main>
        <section class="col-izquierda">
            <h2><?php echo $anuncio['Titulo']; ?></h2>
            <figure class="foto-principal">
                <img src="<?php echo $anuncio['FPrincipal']; ?>" alt="<?php echo $anuncio['Alternativo']; ?>">
            </figure>

            <section class="galeria">
                <h3>Galería de imágenes</h3>
                <?php if (empty($fotos)): ?>
                    <p>No hay más fotos en la galería.</p>
                <?php else: ?>
                    <?php foreach ($fotos as $foto): ?>
                        <figure>
                            <img src="<?php echo $foto['Foto']; ?>" alt="<?php echo $foto['Alternativo']; ?>">
                        </figure>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>

            <?php if (!empty($fotos)): ?>
                <p>
                    <a href="ver_fotos_privada.php?id=<?php echo $anuncio['IdAnuncio']; ?>">
                        Ver todas las fotos (<?php echo count($fotos); ?>)
                    </a>
                </p>
            <?php endif; ?>
        </section>

        <section class="col-derecha">
            <section class="info-general">
                <h3>Información general</h3>
                <p><strong><?php echo $anuncio['Titulo']; ?></strong></p>
                <p>Tipo de anuncio: <?php echo $anuncio['NomTAnuncio']; ?></p>
                <p>Tipo de vivienda: <?php echo $anuncio['NomTVivienda']; ?></p>
                <p>Fecha: <?php echo date("d/m/Y", strtotime($anuncio['FRegistro'])); ?></p>
                <p>Ciudad: <?php echo $anuncio['Ciudad']; ?></p>
                <p>País: <?php echo $anuncio['NomPais']; ?></p>
                <p>Precio: <?php echo number_format($anuncio['Precio'], 0, ',', '.'); ?> €</p>
            </section>

            <section class="descripcion">
                <h3>Descripción</h3>
                <p><?php echo nl2br($anuncio['Texto']); ?></p>
            </section>

            <section class="caracteristicas">
                <h3>Características</h3>
                <ul>
                    <li>Habitaciones: <?php echo $anuncio['NHabitaciones']; ?></li>
                    <li>Baños: <?php echo $anuncio['NBanyos']; ?></li>
                    <li>Superficie: <?php echo $anuncio['Superficie']; ?> m²</li>
                    <?php if ($anuncio['Planta']): ?>
                        <li>Planta: <?php echo $anuncio['Planta']; ?></li>
                    <?php endif; ?>
                    <?php if ($anuncio['Anyo']): ?>
                        <li>Año: <?php echo $anuncio['Anyo']; ?></li>
                    <?php endif; ?>
                </ul>
            </section>

            <p>
                <a href="mensajes_anuncio.php?id=<?php echo $anuncio['IdAnuncio']; ?>">
                    Ver mensajes recibidos
                </a>
            </p>

            <p><a href="añadir_foto.php?id=<?php echo $anuncio['IdAnuncio']; ?>">Añadir foto a este anuncio</a></p>
        </section>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>