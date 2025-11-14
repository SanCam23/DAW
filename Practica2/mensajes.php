<?php
session_start();
require_once __DIR__ . '/db.php';
require_once 'verificar_sesion.php'; // Protegemos la página

// 1. Obtenemos el ID del usuario logueado
$id_usuario_logueado = $_SESSION['usuario_id'];

// 2. Preparamos variables
$db = conectarDB();
$mensajes_enviados = [];
$mensajes_recibidos = [];
$total_enviados = 0;
$total_recibidos = 0;

if ($db) {
    /* Consulta para mensajes ENVIADOS */
    $sql_enviados = "SELECT m.Texto, m.FRegistro, tm.NomTMensaje, u_dest.NomUsuario AS Destinatario
                     FROM MENSAJES m
                     JOIN TIPOSMENSAJES tm ON m.TMensaje = tm.IdTMensaje
                     LEFT JOIN USUARIOS u_dest ON m.UsuDestino = u_dest.IdUsuario
                     WHERE m.UsuOrigen = ?
                     ORDER BY m.FRegistro DESC";

    $stmt_enviados = $db->prepare($sql_enviados);
    $stmt_enviados->bind_param("i", $id_usuario_logueado);
    $stmt_enviados->execute();
    $res_enviados = $stmt_enviados->get_result();

    if ($res_enviados) {
        $total_enviados = $res_enviados->num_rows; // número total de mensajes enviados
        $mensajes_enviados = $res_enviados->fetch_all(MYSQLI_ASSOC);
        $res_enviados->close();
    }
    $stmt_enviados->close();

    /* Consulta para mensajes RECIBIDOS */
    $sql_recibidos = "SELECT m.Texto, m.FRegistro, tm.NomTMensaje, u_orig.NomUsuario AS Emisor
                      FROM MENSAJES m
                      JOIN TIPOSMENSAJES tm ON m.TMensaje = tm.IdTMensaje
                      LEFT JOIN USUARIOS u_orig ON m.UsuOrigen = u_orig.IdUsuario
                      WHERE m.UsuDestino = ?
                      ORDER BY m.FRegistro DESC";

    $stmt_recibidos = $db->prepare($sql_recibidos);
    $stmt_recibidos->bind_param("i", $id_usuario_logueado);
    $stmt_recibidos->execute();
    $res_recibidos = $stmt_recibidos->get_result();

    if ($res_recibidos) {
        $total_recibidos = $res_recibidos->num_rows; // número total de mensajes recibidos
        $mensajes_recibidos = $res_recibidos->fetch_all(MYSQLI_ASSOC);
        $res_recibidos->close();
    }
    $stmt_recibidos->close();

    $db->close();
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis mensajes - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/mensajes.css">
    <link rel="stylesheet" type="text/css" href="css/print_mensajes.css" media="print">
</head>

<body>

    <?php
    $zona = 'privada';
    require('cabecera.php');
    ?>

    <main>
        <article>
            <h2>Mis mensajes</h2>

            <section>
                <h3>Mensajes enviados (<?php echo $total_enviados; ?>)</h3>

                <?php if (empty($mensajes_enviados)): ?>
                    <p>No has enviado ningún mensaje.</p>
                <?php else: ?>
                    <?php foreach ($mensajes_enviados as $msg): ?>
                        <article id="enviados">
                            <p><strong>Tipo de mensaje:</strong> <?php echo htmlspecialchars($msg['NomTMensaje']); ?></p>
                            <p><strong>Texto:</strong> <?php echo nl2br(htmlspecialchars($msg['Texto'])); ?></p>
                            <p><strong>Fecha:</strong> <?php echo date("d/m/Y H:i", strtotime($msg['FRegistro'])); ?></p>
                            <p><strong>Receptor:</strong> <?php echo htmlspecialchars($msg['Destinatario']); ?></p>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>

            </section>

            <section>
                <h3>Mensajes recibidos (<?php echo $total_recibidos; ?>)</h3>

                <?php if (empty($mensajes_recibidos)): ?>
                    <p>No tienes mensajes recibidos.</p>
                <?php else: ?>
                    <?php foreach ($mensajes_recibidos as $msg): ?>
                        <article id="recibidos">
                            <p><strong>Tipo de mensaje:</strong> <?php echo htmlspecialchars($msg['NomTMensaje']); ?></p>
                            <p><strong>Texto:</strong> <?php echo nl2br(htmlspecialchars($msg['Texto'])); ?></p>
                            <p><strong>Fecha:</strong> <?php echo date("d/m/Y H:i", strtotime($msg['FRegistro'])); ?></p>
                            <p><strong>Emisor:</strong> Usuario: <?php echo htmlspecialchars($msg['Emisor']); ?></p>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>

            </section>
        </article>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>