<?php
session_start();
require_once 'verificar_sesion.php';
require_once __DIR__ . '/db.php';

$db = conectarDB();
$anuncios = [];
$total_anuncios = 0;

if ($db && isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];

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
    <title>Mis anuncios - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
    <?php require('cabecera.php'); ?>

    <main>
        <?php if (isset($_GET['borrado']) && $_GET['borrado'] == 'exito'): ?>
            <div
                style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; border: 1px solid #c3e6cb;">
                Anuncio eliminado correctamente.
            </div>
        <?php endif; ?>
        <section id="ultimos-anuncios">
            <h2>Mis anuncios (<?php echo $total_anuncios; ?>)</h2>

            <?php if ($total_anuncios > 0): ?>
                <?php foreach ($anuncios as $anuncio):
                    $fecha = date("d/m/Y", strtotime($anuncio['FRegistro']));
                    ?>
                    <article class="anuncio">
                        <figure>
                            <img src="<?php echo !empty($anuncio['FPrincipal']) ? htmlspecialchars($anuncio['FPrincipal']) : 'img/sin_foto.png'; ?>"
                                alt="<?php echo htmlspecialchars($anuncio['Alternativo']); ?>">
                        </figure>
                        <h3><?php echo htmlspecialchars($anuncio['Titulo']); ?></h3>
                        <p><strong>Ciudad:</strong> <?php echo htmlspecialchars($anuncio['Ciudad']); ?></p>
                        <p><strong>Precio:</strong> <?php echo number_format($anuncio['Precio'], 0, ',', '.'); ?> €</p>
                        <p><strong>Fecha:</strong> <?php echo $fecha; ?></p>

                        <div class="acciones-anuncio">
                            <a href="ver_anuncio.php?id=<?php echo $anuncio['IdAnuncio']; ?>">Ver</a>
                            <a href="modificar_anuncio.php?id=<?php echo $anuncio['IdAnuncio']; ?>">Modificar</a>
                            <a href="añadir_foto.php?id=<?php echo $anuncio['IdAnuncio']; ?>">+ Foto</a>
                            <a href="eliminar_anuncio.php?id=<?php echo $anuncio['IdAnuncio']; ?>"
                                style="background-color: #d9534f;">Eliminar</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="centrado">No tienes anuncios publicados.</p>
            <?php endif; ?>
        </section>

        <p class="centrado">
            <a href="crear_anuncio.php" class="nuevo">+ Crear nuevo anuncio</a>
        </p>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>