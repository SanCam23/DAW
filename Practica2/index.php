<?php
session_start();

require_once __DIR__ . '/db.php';

if (!isset($_SESSION['estilo_css']) && isset($_COOKIE['estilo_css'])) {
    $_SESSION['estilo_css'] = $_COOKIE['estilo_css'];
} elseif (!isset($_SESSION['estilo_css'])) {
    $_SESSION['estilo_css'] = 'normal';
}

// Conectar a la BD y preparar datos.
$db = conectarDB();
$anuncios = [];

if ($db) {
    // Obtener resumen de los 5 últimos anuncios.
    $sql = "SELECT a.IdAnuncio, a.Titulo, a.FPrincipal, a.Alternativo, a.Ciudad, 
                   a.Precio, a.FRegistro, p.NomPais
            FROM ANUNCIOS a
            LEFT JOIN PAISES p ON a.Pais = p.IdPais
            ORDER BY a.FRegistro DESC
            LIMIT 5";

    // Ejecutamos la consulta
    $resultado = $db->query($sql);

    // Si la consulta fue exitosa y trajo filas, las guardamos
    if ($resultado && $resultado->num_rows > 0) {
        $anuncios = $resultado->fetch_all(MYSQLI_ASSOC);
        $resultado->close();
    }

    // Cerramos la conexión
    $db->close();
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
    <title>Inicio - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print">
</head>

<body class="inicio">

    <?php
    if (!isset($_SESSION['usuario_autenticado']) && isset($_COOKIE['recordarme_token'])) {
        require_once 'verificar_cookie_recordarme.php';
    }

    require('cabecera.php');

    if (isset($_SESSION['usuario_autenticado']) && $_SESSION['usuario_autenticado'] === true) {
        $zona = 'privada';
    } else {
        $zona = 'publica';
    }
    ?>

    <?php if (!isset($_SESSION['usuario_autenticado']) || $_SESSION['usuario_autenticado'] !== true): ?>
        <section id="login-popup">
            <h2>Iniciar Sesión</h2>
            <?php if (isset($_SESSION['error_login'])): ?>
                <div class="mensaje-error" id="mensaje-error">
                    <p><?php echo $_SESSION['error_login']; ?></p>
                </div>
                <?php unset($_SESSION['error_login']); ?>
                <script>
                    setTimeout(function() {
                        document.getElementById('mensaje-error').style.display = 'none';
                    }, 5000);
                </script>
            <?php endif; ?>
            <form id="login" action="acceso.php" method="post">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario"
                    value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password">
                <label class="recordarme-label">
                    Recordarme en este equipo
                    <input type="checkbox" name="recordarme" value="1" id="recordarme">
                </label>
                <button type="submit">Entrar</button>
            </form>
        </section>
    <?php endif; ?>

    <main>
        <?php if (isset($_SESSION['visita_para_mostrar'])): ?>
            <p id="visita">Su última visita fue el <?php echo $_SESSION['visita_para_mostrar']; ?></p>
        <?php endif; ?>

        <section id="busqueda-rapida">
            <form action="resultados.php" method="get">
                <input type="text" id="ciudad" name="q" placeholder="Búsqueda rápida (ej: piso alquiler madrid)...">
                <button type="submit"><b><i class="icon-search"></i>Buscar</b></button>
            </form>
        </section>


        <section id="ultimos-anuncios">
            <h2>Últimos anuncios</h2>

            <?php
            // Mostrar los resultados de la BD
            // Comprobamos si el array $anuncios tiene contenido.
            if (!empty($anuncios)) {

                /* Bucle para mostrar anuncios */
                foreach ($anuncios as $anuncio) {

                    // Formateamos la fecha de registro para que sea legible
                    $fecha_formateada = date("d/m/Y", strtotime($anuncio['FRegistro']));
            ?>
                    <section class="anuncio">
                        <figure>
                            <img src="<?php echo htmlspecialchars($anuncio['FPrincipal']); ?>" alt="<?php echo htmlspecialchars($anuncio['Alternativo']); ?>">
                        </figure>
                        <h3><?php echo htmlspecialchars($anuncio['Titulo']); ?></h3>
                        <p>Fecha: <?php echo $fecha_formateada; ?></p>
                        <p>Ciudad: <?php echo htmlspecialchars($anuncio['Ciudad']); ?> (<?php echo htmlspecialchars($anuncio['NomPais']); ?>)</p>
                        <p>Precio: <?php echo number_format($anuncio['Precio'], 0, ',', '.'); ?>€</p>

                        <a href="detalle_anuncio.php?id=<?php echo $anuncio['IdAnuncio']; ?>">Ver detalle</a>
                    </section>

            <?php
                }
            } else {
                // Si la BD no devolvió nada (pero la conexión fue bien)
                echo "<p>No hay anuncios disponibles en este momento.</p>";
            }
            ?>

        </section>

        <?php require_once 'panel_visitados.php'; ?>
    </main>

    <?php require('pie.php'); ?>

    <dialog class="modal" id="error-dialog">
        <p id="error-mensaje"></p>
        <button class="cerrar" id="cerrar-error">Cerrar</button>
    </dialog>

</body>

</html>