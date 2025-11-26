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

    // Consulta del anuncio
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

    // Obtener fotos de la galería
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
    <title><?php echo htmlspecialchars($anuncio['Titulo']); ?> - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/ver_anuncio.css">
</head>

<body>
    <?php require('cabecera.php'); ?>

    <main>
        <section class="col-izquierda">
            <h2><?php echo htmlspecialchars($anuncio['Titulo']); ?></h2>
            
            <figure class="foto-principal">
                <img src="<?php echo htmlspecialchars($anuncio['FPrincipal']); ?>" 
                     alt="<?php echo htmlspecialchars($anuncio['Alternativo']); ?>">
            </figure>

            <section class="galeria">
                <h3>Galería de imágenes</h3>
                <?php if (empty($fotos)): ?>
                    <p>No hay más fotos en la galería.</p>
                <?php else: ?>
                    <?php 
                    $fotos_mostradas = 0;
                    foreach ($fotos as $foto): 
                        // FILTRO: Si la foto de la galería es la misma que la principal, NO la mostramos aquí
                        if ($foto['Foto'] === $anuncio['FPrincipal']) {
                            continue; 
                        }
                        $fotos_mostradas++;
                    ?>
                        <figure>
                            <img src="<?php echo htmlspecialchars($foto['Foto']); ?>" 
                                 alt="<?php echo htmlspecialchars($foto['Alternativo']); ?>">
                        </figure>
                    <?php endforeach; ?>

                    <?php if ($fotos_mostradas === 0): ?>
                        <p>No hay más fotos adicionales.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </section>

            <p>
                <a href="ver_fotos_privada.php?id=<?php echo $anuncio['IdAnuncio']; ?>">
                    Gestionar fotos / Ver todas
                </a>
            </p>
        </section>

        <section class="col-derecha">
            <section class="info-general">
                <h3>Información general</h3>
                <p><strong><?php echo htmlspecialchars($anuncio['Titulo']); ?></strong></p>
                <p>Tipo: <?php echo htmlspecialchars($anuncio['NomTAnuncio']); ?> de <?php echo htmlspecialchars($anuncio['NomTVivienda']); ?></p>
                <p>Fecha: <?php echo date("d/m/Y", strtotime($anuncio['FRegistro'])); ?></p>
                <p>Ubicación: <?php echo htmlspecialchars($anuncio['Ciudad']); ?> (<?php echo htmlspecialchars($anuncio['NomPais']); ?>)</p>
                <p>Precio: <?php echo number_format($anuncio['Precio'], 0, ',', '.'); ?> €</p>
            </section>

            <section class="descripcion">
                <h3>Descripción</h3>
                <p><?php echo nl2br(htmlspecialchars($anuncio['Texto'])); ?></p>
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

            <p><a href="mensajes_anuncio.php?id=<?php echo $anuncio['IdAnuncio']; ?>">Ver mensajes recibidos</a></p>
            <p><a href="añadir_foto.php?id=<?php echo $anuncio['IdAnuncio']; ?>">Añadir foto a este anuncio</a></p>
        </section>
    </main>

    <?php require('pie.php'); ?>
</body>
</html>