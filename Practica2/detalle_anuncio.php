<?php

require_once __DIR__ . '/db.php';


/* Lógica de la cookie (se mantiene) */
$cookie_name = 'ultimos_visitados';

// Obtenemos el ID del anuncio de la URL
$id_actual = $_GET['id'] ?? 0;
$id_actual = (int)$id_actual;

// Si el ID no es válido (0 o no numérico), no continuamos
if ($id_actual <= 0) {
    header("Location: 404.php");
    exit;
}

$visitados_cookie = $_COOKIE[$cookie_name] ?? '[]';
$visitados_array = json_decode($visitados_cookie, true);
if (!is_array($visitados_array)) {
    $visitados_array = [];
}
$visitados_array = array_diff($visitados_array, [$id_actual]);
$visitados_array[] = $id_actual;
while (count($visitados_array) > 4) {
    array_shift($visitados_array);
}
$json_visitados = json_encode($visitados_array);
$expiracion = time() + (7 * 24 * 60 * 60); // 1 semana
$path = '/';
setcookie(
    $cookie_name,
    $json_visitados,
    [
        'expires' => $expiracion,
        'path' => $path,
        'httponly' => true
    ]
);


/* Obtener datos del anuncio de la BD */
$anuncio = null;
$fotos = [];
$db = conectarDB();

if ($db) {
    /*
     * Requisito PDF: Usamos sentencias preparadas para evitar Inyección SQL
     * El '?' será reemplazado por $id_actual.
     */

    // 1. Consulta principal del anuncio
    $sql = "SELECT a.*, p.NomPais, u.NomUsuario, ta.NomTAnuncio, tv.NomTVivienda
            FROM ANUNCIOS a
            LEFT JOIN PAISES p ON a.Pais = p.IdPais
            LEFT JOIN USUARIOS u ON a.Usuario = u.IdUsuario
            LEFT JOIN TIPOSANUNCIOS ta ON a.TAnuncio = ta.IdTAnuncio
            LEFT JOIN TIPOSVIVIENDAS tv ON a.TVivienda = tv.IdTVivienda
            WHERE a.IdAnuncio = ?";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $id_actual); // "i" significa que $id_actual es un entero
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows > 0) {
        $anuncio = $resultado->fetch_assoc();
    }
    $stmt->close();

    // 2. Consulta de la galería de fotos
    if ($anuncio) {
        $sql_fotos = "SELECT Foto, Alternativo, Titulo FROM FOTOS WHERE Anuncio = ?";
        $stmt_fotos = $db->prepare($sql_fotos);
        $stmt_fotos->bind_param("i", $id_actual);
        $stmt_fotos->execute();
        $res_fotos = $stmt_fotos->get_result();
        if ($res_fotos->num_rows > 0) {
            $fotos = $res_fotos->fetch_all(MYSQLI_ASSOC);
        }
        $stmt_fotos->close();
    }

    $db->close();
}

// Si el ID no existe, redirigimos a la página 404
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
    <title><?php echo htmlspecialchars($anuncio["Titulo"]); ?> - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/anuncio.css">
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print">
</head>

<body>
    <?php
    $zona = 'publica';
    require('cabecera.php');
    ?>

    <main>
        <article>
            <section class="col-izquierda">
                <h2><?php echo htmlspecialchars($anuncio["Titulo"]); ?></h2>

                <figure class="foto-principal">
                    <img src="<?php echo htmlspecialchars($anuncio["FPrincipal"]); ?>" alt="<?php echo htmlspecialchars($anuncio["Alternativo"]); ?>">
                </figure>

                <section class="galeria">
                    <h3>Galería de imágenes</h3>
                    <?php if (empty($fotos)): ?>
                        <p>No hay más fotos en la galería.</p>
                    <?php else: ?>
                        <?php foreach ($fotos as $foto): ?>
                            <figure>
                                <img src="<?php echo htmlspecialchars($foto["Foto"]); ?>" alt="<?php echo htmlspecialchars($foto["Alternativo"]); ?>">
                            </figure>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </section> <?php if (!empty($fotos)): ?>
                    <p>
                        <a href="ver_fotos.php?id=<?php echo $anuncio['IdAnuncio']; ?>">
                            Ver todas las fotos (<?php echo count($fotos); ?>)
                        </a>
                    </p>
                <?php endif; ?>
            </section>

            <section class="col-derecha">
                <section class="info-general">
                    <h3>Información general</h3>
                    <p><strong><?php echo htmlspecialchars($anuncio["Titulo"]); ?></strong></p>
                    <p>Tipo de anuncio: <?php echo htmlspecialchars($anuncio["NomTAnuncio"]); ?></p>
                    <p>Tipo de vivienda: <?php echo htmlspecialchars($anuncio["NomTVivienda"]); ?></p>
                    <p>Fecha: <?php echo date("d/m/Y", strtotime($anuncio["FRegistro"])); ?></p>
                    <p>Ciudad: <?php echo htmlspecialchars($anuncio["Ciudad"]); ?></p>
                    <p>País: <?php echo htmlspecialchars($anuncio["NomPais"]); ?></p>
                    <p>Precio: <?php echo number_format($anuncio["Precio"], 0, ',', '.'); ?> €</p>
                </section>

                <section class="descripcion">
                    <h3>Descripción</h3>
                    <p><?php echo nl2br(htmlspecialchars($anuncio["Texto"])); ?></p>
                </section>

                <section class="caracteristicas">
                    <h3>Características</h3>
                    <ul>
                        <li>Habitaciones: <?php echo $anuncio["NHabitaciones"]; ?></li>
                        <li>Baños: <?php echo $anuncio["NBanyos"]; ?></li>
                        <li>Superficie: <?php echo $anuncio["Superficie"]; ?> m²</li>

                        <?php if ($anuncio["Planta"]): ?>
                            <li>Planta: <?php echo $anuncio["Planta"]; ?></li>
                        <?php endif; ?>

                        <?php if ($anuncio["Anyo"]): ?>
                            <li>Año: <?php echo $anuncio["Anyo"]; ?></li>
                        <?php endif; ?>
                    </ul>
                </section>

                <p class="publicado">
                    <strong>Publicado por:</strong>
                    <a href="perfil_usuario.php?id=<?php echo $anuncio['Usuario']; ?>">
                        <?php echo htmlspecialchars($anuncio["NomUsuario"]); ?>
                    </a>
                </p>
            </section>
        </article>

        <section class="contacto">
            <h3>Enviar mensaje</h3>
            <p><a href="enviar.php">Ir al formulario de contacto</a></p>
        </section>
    </main>

    <?php require_once 'panel_visitados.php'; ?>
    <?php require('pie.php'); ?>
</body>

</html>