<?php
session_start();
require_once 'verificar_sesion.php';
require_once __DIR__ . '/db.php';

$zona = 'privada';
$db = conectarDB();

// Validar ID y propiedad del anuncio
$id_anuncio = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
$usuario_id = $_SESSION['usuario_id'];

// Consultar si existe y es del usuario
$stmt = $db->prepare("SELECT Titulo FROM ANUNCIOS WHERE IdAnuncio = ? AND Usuario = ?");
$stmt->bind_param("ii", $id_anuncio, $usuario_id);
$stmt->execute();
$res = $stmt->get_result();
$anuncio = $res->fetch_assoc();
$stmt->close();

if (!$anuncio) {
    // Redirigir si no existe o no es suyo
    header("Location: misanuncios.php");
    exit;
}

$mensaje_error = "";

// Procesar borrado tras confirmación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {

    // Iniciar transacción para borrado en cascada
    $db->begin_transaction();
    try {
        // Borrar solicitudes de folleto asociadas
        $db->query("DELETE FROM SOLICITUDES WHERE Anuncio = $id_anuncio");

        // Borrar mensajes asociados
        $db->query("DELETE FROM MENSAJES WHERE Anuncio = $id_anuncio");

        // Borrar fotos asociadas
        $db->query("DELETE FROM FOTOS WHERE Anuncio = $id_anuncio");

        // Borrar el anuncio
        $stmt_del = $db->prepare("DELETE FROM ANUNCIOS WHERE IdAnuncio = ?");
        $stmt_del->bind_param("i", $id_anuncio);
        $stmt_del->execute();
        $stmt_del->close();

        $db->commit();

        // Redirigir con éxito
        header("Location: misanuncios.php?borrado=exito");
        exit;
    } catch (Exception $e) {
        $db->rollback();
        $mensaje_error = "Error al eliminar: " . $e->getMessage();
    }
}
$db->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Eliminar Anuncio - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/eliminar_anuncio.css">
</head>

<body>
    <?php require('cabecera.php'); ?>

    <main>
        <section class="alerta-borrado">
            <h2>Confirmar Eliminación</h2>
            <p>¿Estás seguro de que deseas eliminar el anuncio:</p>
            <p><strong>"<?php echo htmlspecialchars($anuncio['Titulo']); ?>"</strong>?</p>
            <br>
            <p>Esta acción borrará también todas las fotos, mensajes y solicitudes asociadas.<br>
                <strong>Esta operación no se puede deshacer.</strong>
            </p>

            <?php if ($mensaje_error): ?>
                <p class="mensaje-error"><?php echo htmlspecialchars($mensaje_error); ?></p>
            <?php endif; ?>

            <form method="post" class="botones">
                <input type="hidden" name="confirmar" value="1">
                <a href="misanuncios.php" class="btn-cancelar">Cancelar</a>
                <button type="submit" class="btn-peligro">Sí, eliminar definitivamente</button>
            </form>
        </section>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>