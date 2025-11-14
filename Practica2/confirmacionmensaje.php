<?php
require_once __DIR__ . '/db.php';
session_start(); // Para saber quién envía el mensaje

// 1️. Recibir datos del formulario
$tipo_id = $_POST['tipo'] ?? '';
$mensaje = trim($_POST['mensaje'] ?? '');
$id_anuncio_destino = (int)($_POST['id_anuncio_destino'] ?? 0);

$errores = [];

// 2️. Validaciones
if ($tipo_id === '') {
    $errores[] = "Tipo de mensaje no válido.";
}
if ($mensaje === '') {
    $errores[] = "El campo del mensaje no puede estar vacío.";
}
if (strlen($mensaje) > 4000) {
    $errores[] = "El mensaje no puede superar los 4000 caracteres.";
}
if ($id_anuncio_destino <= 0) {
    $errores[] = "ID de anuncio no válido.";
}

// 3️. Determinar usuario que envía
$id_usuario_origen = $_SESSION['usuario_id'] ?? null; // visitante logueado
if ($id_usuario_origen === null) {
    $id_usuario_origen = 4; // ID de invitado para pruebas
}

// 4️. Conectar a la BD y obtener dueño del anuncio
$id_usuario_destino = null;
$nombre_tipo_mensaje = "Desconocido";

if (empty($errores)) {
    $db = conectarDB();
    if ($db) {
        // Buscar dueño del anuncio
        $sql_dueno = "SELECT Usuario FROM ANUNCIOS WHERE IdAnuncio = ?";
        $stmt_dueno = $db->prepare($sql_dueno);
        $stmt_dueno->bind_param("i", $id_anuncio_destino);
        $stmt_dueno->execute();
        $res_dueno = $stmt_dueno->get_result();
        if ($res_dueno && $res_dueno->num_rows > 0) {
            $id_usuario_destino = $res_dueno->fetch_assoc()['Usuario'];
        } else {
            $errores[] = "El anuncio no existe o no tiene dueño.";
        }
        $stmt_dueno->close();

        // Insertar mensaje si no hay errores
        if (empty($errores)) {
            $sql_insert = "INSERT INTO MENSAJES (TMensaje, Texto, Anuncio, UsuOrigen, UsuDestino, FRegistro)
                           VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt_insert = $db->prepare($sql_insert);
            $stmt_insert->bind_param("isiii", $tipo_id, $mensaje, $id_anuncio_destino, $id_usuario_origen, $id_usuario_destino);
            if (!$stmt_insert->execute()) {
                $errores[] = "Error al guardar el mensaje en la base de datos: " . $stmt_insert->error;
            }
            $stmt_insert->close();
        }

        // Obtener nombre del tipo de mensaje para mostrar en la confirmación
        if (empty($errores)) {
            $sql_tipo = "SELECT NomTMensaje FROM TIPOSMENSAJES WHERE IdTMensaje = ?";
            $stmt_tipo = $db->prepare($sql_tipo);
            $stmt_tipo->bind_param("i", $tipo_id);
            $stmt_tipo->execute();
            $res_tipo = $stmt_tipo->get_result();
            if ($res_tipo && $res_tipo->num_rows > 0) {
                $nombre_tipo_mensaje = $res_tipo->fetch_assoc()['NomTMensaje'];
            }
            $stmt_tipo->close();
        }

        $db->close();
    } else {
        $errores[] = "Error al conectar con la base de datos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación Mensaje - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/confirmacionmensaje.css">
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print">
</head>

<body>
    <?php
    $zona = 'publica';
    require('cabecera.php');
    ?>

    <main>
        <article>
            <?php if (empty($errores)): ?>
                <section>
                    <h2>Mensaje enviado correctamente</h2>
                    <p>Tu mensaje ha sido enviado y registrado correctamente.</p>
                </section>

                <section>
                    <h3>Datos enviados</h3>
                    <p><strong>Tipo de mensaje:</strong> <?php echo htmlspecialchars($nombre_tipo_mensaje); ?></p>
                    <p><strong>Mensaje:</strong> <?php echo nl2br(htmlspecialchars($mensaje)); ?></p>
                </section>
            <?php else: ?>
                <section>
                    <h2>Error al enviar el mensaje</h2>
                    <ul style="color: red;">
                        <?php foreach ($errores as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <br>
                    <p><a href="enviar.php">Volver al formulario</a></p>
                </section>
            <?php endif; ?>
        </article>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>