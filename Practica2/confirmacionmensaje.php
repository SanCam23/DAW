<?php
/*
 * MODIFICADO: Tarea 4 (Persona 2)
 * Incluimos el conector a la BD
 */
require_once __DIR__ . '/db.php';
session_start(); // Necesitamos la sesión para saber QUIÉN envía el mensaje

/*
 * MODIFICADO: Tarea 4 (Persona 2)
 * Lógica de validación e INSERT en la BD
 */

// 1. Validar y recibir datos del formulario
$tipo_id = $_POST['tipo'] ?? '';
$mensaje = trim($_POST['mensaje'] ?? '');

$errores = [];

// Validación simple
if ($tipo_id === '') {
    $errores[] = "Tipo de mensaje no válido.";
}
if ($mensaje === '') {
    $errores[] = "El campo del mensaje no puede estar vacío.";
}
if (strlen($mensaje) > 4000) {
    $errores[] = "El mensaje no puede superar los 4000 caracteres.";
}

$id_usuario_origen = $_SESSION['usuario_id'] ?? null; // Asumimos que la Persona 1 guardará el ID aquí
$id_anuncio_destino = 1; // ID Ficticio del anuncio (Anuncio 1: Madrid)
$id_usuario_destino = 2; // ID Ficticio del dueño del anuncio (Usuario 2: mario)

/*
 * NOTA: Para una implementación completa, necesitaríamos pasar
 * el ID del anuncio y el ID del dueño del anuncio (UsuDestino)
 * como campos <input type="hidden"> desde 'detalle_anuncio.php'
 * hasta 'enviar.php' y finalmente aquí.
 *
 * Como la Persona 2 (tú) no tiene control sobre el login (Persona 1),
 * dejaremos el UsuOrigen como NULL (o un ID ficticio) si el usuario no está logueado.
 */
if ($id_usuario_origen === null) {
    // Si el visitante no está logueado, lo dejamos como NULL (o un ID "invitado" si tu BD lo permite)
    // Por ahora, para probar, usaremos un ID ficticio
    $id_usuario_origen = 4; // ID 4 = "test"
}


// 2. Si no hay errores, conectamos e insertamos
$nombre_tipo_mensaje = "Desconocido"; // Para mostrar en la confirmación

if (empty($errores)) {
    $db = conectarDB();
    if ($db) {

        /*
         * Requisito PDF: Insertar el mensaje en la tabla MENSAJES
         * Usamos sentencias preparadas para seguridad
         */
        $sql = "INSERT INTO MENSAJES (TMensaje, Texto, Anuncio, UsuOrigen, UsuDestino, FRegistro)
                VALUES (?, ?, ?, ?, ?, NOW())";

        $stmt = $db->prepare($sql);

        // s = string, i = integer
        $stmt->bind_param(
            "isiii",
            $tipo_id,
            $mensaje,
            $id_anuncio_destino,
            $id_usuario_origen,
            $id_usuario_destino
        );

        if (!$stmt->execute()) {
            $errores[] = "Error al guardar el mensaje en la base de datos: " . $stmt->error;
        }
        $stmt->close();

        // 3. (Extra) Obtenemos el nombre del tipo de mensaje para mostrarlo
        if (empty($errores)) {
            $sql_nombre = "SELECT NomTMensaje FROM TIPOSMENSAJES WHERE IdTMensaje = ?";
            $stmt_nombre = $db->prepare($sql_nombre);
            $stmt_nombre->bind_param("i", $tipo_id);
            $stmt_nombre->execute();
            $res_nombre = $stmt_nombre->get_result();
            if ($res_nombre->num_rows > 0) {
                $nombre_tipo_mensaje = $res_nombre->fetch_assoc()['NomTMensaje'];
            }
            $stmt_nombre->close();
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
            <?php
            /*
             * MODIFICADO: Tarea 4 (Persona 2)
             * La lógica de mostrar éxito o error ahora depende
             * del array $errores que llenamos arriba.
             */

            if (empty($errores)):
            ?>
                <section>
                    <h2>Mensaje enviado correctamente</h2>
                    <p>Tu mensaje ha sido enviado y registrado correctamente.</p>
                </section>

                <section>
                    <h3>Datos enviados</h3>
                    <p><strong>Tipo de mensaje:</strong> <?php echo htmlspecialchars($nombre_tipo_mensaje); ?></p>
                    <p><strong>Mensaje:</strong> <?php echo nl2br(htmlspecialchars($mensaje)); ?></p>
                </section>

            <?php
            else:
                // Si hubo errores
            ?>
                <section>
                    <h2>Error al enviar el mensaje</h2>
                    <ul style="color: red;">
                        <?php
                        foreach ($errores as $error) {
                            echo "<li>" . htmlspecialchars($error) . "</li>";
                        }
                        ?>
                    </ul>
                    <br>
                    <p><a href="javascript:history.back()">Volver al formulario</a></p>
                </section>
            <?php
            endif;
            ?>
        </article>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>