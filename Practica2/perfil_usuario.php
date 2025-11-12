<?php
/*
 * NUEVO: Tarea 5 (Persona 2)
 * Página de Perfil Público de Usuario
 */

require_once __DIR__ . '/db.php';

// 1. Obtener y validar el ID del usuario de la URL
$id_usuario = $_GET['id'] ?? 0;
$id_usuario = (int)$id_usuario;

if ($id_usuario <= 0) {
    header("Location: 404.php");
    exit;
}

// 2. Conectar a la BD
$db = conectarDB();
$usuario = null;
$anuncios = [];

if ($db) {
    /*
     * 3. Requisito PDF: Obtener perfil público (nombre, foto, fecha)
     * Usamos sentencias preparadas para seguridad
     */
    $sql_usuario = "SELECT NomUsuario, Foto, FRegistro FROM USUARIOS WHERE IdUsuario = ?";
    $stmt_usuario = $db->prepare($sql_usuario);
    $stmt_usuario->bind_param("i", $id_usuario);
    $stmt_usuario->execute();
    $res_usuario = $stmt_usuario->get_result();

    if ($res_usuario->num_rows > 0) {
        $usuario = $res_usuario->fetch_assoc();
    }
    $stmt_usuario->close();

    /*
     * 4. Requisito PDF: Obtener listado simplificado de sus anuncios
     */
    if ($usuario) {
        $sql_anuncios = "SELECT a.IdAnuncio, a.Titulo, a.FPrincipal, a.Alternativo, 
                                a.Ciudad, a.Precio, a.FRegistro, p.NomPais
                         FROM ANUNCIOS a
                         LEFT JOIN PAISES p ON a.Pais = p.IdPais
                         WHERE a.Usuario = ?
                         ORDER BY a.FRegistro DESC";

        $stmt_anuncios = $db->prepare($sql_anuncios);
        $stmt_anuncios->bind_param("i", $id_usuario);
        $stmt_anuncios->execute();
        $res_anuncios = $stmt_anuncios->get_result();

        if ($res_anuncios->num_rows > 0) {
            $anuncios = $res_anuncios->fetch_all(MYSQLI_ASSOC);
        }
        $stmt_anuncios->close();
    }

    $db->close();
}

// 5. Si el ID de usuario no existe, 404
if ($usuario === null) {
    header("Location: 404.php");
    exit;
}

// Formateamos la fecha de registro del usuario
$fecha_registro = date("d/m/Y", strtotime($usuario['FRegistro']));

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($usuario['NomUsuario']); ?> - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/resultados.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print">
    <style>
        /* Estilo simple para la info del perfil */
        .perfil-info {
            background-color: #f7fdfc;
            border: 1px solid #cce3dd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 35px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .perfil-info img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #1b9986;
        }

        .perfil-info h2 {
            margin-bottom: 10px;
        }

        .perfil-info p {
            color: #555;
        }
    </style>
</head>

<body>
    <?php
    $zona = 'publica';
    require('cabecera.php');
    ?>

    <main>
        <section class="perfil-info">
            <?php if (!empty($usuario['Foto'])): ?>
                <figure>
                    <img src="<?php echo htmlspecialchars($usuario['Foto']); ?>" alt="Foto de perfil de <?php echo htmlspecialchars($usuario['NomUsuario']); ?>">
                </figure>
            <?php endif; ?>

            <div>
                <h2><?php echo htmlspecialchars($usuario['NomUsuario']); ?></h2>
                <p>Miembro desde: <?php echo $fecha_registro; ?></p>
            </div>
        </section>

        <section id="resultados">
            <h3>Anuncios de <?php echo htmlspecialchars($usuario['NomUsuario']); ?> (<?php echo count($anuncios); ?>)</h3>

            <?php
            if (empty($anuncios)) {
                echo "<p style='text-align: center; width: 100%;'>Este usuario todavía no tiene anuncios publicados.</p>";
            } else {
                // Reutilizamos la misma plantilla de 'resultados.php'
                foreach ($anuncios as $anuncio) {
                    $fecha_formateada = date("d/m/Y", strtotime($anuncio['FRegistro']));
            ?>
                    <article class="destacado">
                        <figure>
                            <img src="<?php echo htmlspecialchars($anuncio['FPrincipal']); ?>" alt="<?php echo htmlspecialchars($anuncio['Alternativo']); ?>">
                        </figure>
                        <h4><?php echo htmlspecialchars($anuncio['Titulo']); ?></h4>
                        <p>Fecha: <?php echo $fecha_formateada; ?></p>
                        <p>Ciudad: <?php echo htmlspecialchars($anuncio['Ciudad']); ?> (<?php echo htmlspecialchars($anuncio['NomPais']); ?>)</p>
                        <p>Precio: <?php echo number_format($anuncio['Precio'], 0, ',', '.'); ?> €</p>
                        <a href="detalle_anuncio.php?id=<?php echo $anuncio['IdAnuncio']; ?>">Ver detalle</a>
                    </article>
            <?php
                } // Fin del foreach
            } // Fin del else
            ?>
        </section>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>