<?php
session_start();

// Verificar que el usuario está logueado
require_once 'verificar_sesion.php';

// Conexión a BD para cargar anuncios del usuario
require_once __DIR__ . '/db.php';
$db = conectarDB();

$anuncios = [];
$total_anuncios = 0;

if ($db && isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    
    // Consulta para obtener anuncios del usuario
    $sql = "SELECT a.IdAnuncio, a.Titulo, a.FPrincipal, a.Alternativo, a.Ciudad, 
                   a.Precio, a.FRegistro, p.NomPais
            FROM ANUNCIOS a
            LEFT JOIN PAISES p ON a.Pais = p.IdPais
            WHERE a.Usuario = ?
            ORDER BY a.FRegistro DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado) {
        $anuncios = $resultado->fetch_all(MYSQLI_ASSOC);
        $total_anuncios = count($anuncios);
        $resultado->close();
    }
    $stmt->close();
    $db->close();
}

$zona = 'privada';
?>
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
    require('cabecera.php');
    ?>

    <main>
        <section id="ultimos-anuncios">
            <h2>Mis anuncios (<?php echo $total_anuncios; ?>)</h2>

            <?php
            if ($total_anuncios > 0) {
                foreach ($anuncios as $anuncio) {
                    $fecha_formateada = date("d/m/Y", strtotime($anuncio['FRegistro']));
            ?>
                    <article class="anuncio">
                        <figure>
                            <img src="<?php echo $anuncio['FPrincipal']; ?>" alt="<?php echo $anuncio['Alternativo']; ?>">
                        </figure>
                        <h3><?php echo $anuncio['Titulo']; ?></h3>
                        <p><strong>Ciudad:</strong> <?php echo $anuncio['Ciudad']; ?></p>
                        <p><strong>País:</strong> <?php echo $anuncio['NomPais']; ?></p>
                        <p><strong>Precio:</strong> <?php echo number_format($anuncio['Precio'], 0, ',', '.'); ?> €</p>
                        <p><strong>Fecha publicación:</strong> <?php echo $fecha_formateada; ?></p>
                        <a href="ver_anuncio.php?id=<?php echo $anuncio['IdAnuncio']; ?>">Ver anuncio</a>
                    </article>
            <?php
                }
            } else {
                echo "<p style='text-align: center; width: 100%;'>No tienes anuncios publicados.</p>";
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