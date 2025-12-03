<?php
session_start();
require_once 'verificar_sesion.php';
require_once __DIR__ . '/db.php';

$zona = 'privada';
$db = conectarDB();

// Validar ID
$id_foto = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
$usuario_id = $_SESSION['usuario_id'];

// Consultar datos de la foto y verificar propiedad
$sql = "SELECT f.IdFoto, f.Foto, f.Titulo, f.Anuncio, a.Titulo as TituloAnuncio, a.FPrincipal 
        FROM FOTOS f 
        JOIN ANUNCIOS a ON f.Anuncio = a.IdAnuncio 
        WHERE f.IdFoto = ? AND a.Usuario = ?";

$stmt = $db->prepare($sql);
$stmt->bind_param("ii", $id_foto, $usuario_id);
$stmt->execute();
$res = $stmt->get_result();
$foto = $res->fetch_assoc();
$stmt->close();

// Si la foto no existe o no es del usuario
if (!$foto) {
    header("Location: misanuncios.php");
    exit;
}

$error = "";

// Procesar borrado tras confirmación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {

    $db->begin_transaction();
    try {
        // Comprobar si la foto a borrar es la foto principal del anuncio
        if ($foto['Foto'] === $foto['FPrincipal']) {
            // Actualizar anuncio para quitar referencia a la foto borrada
            $sql_update = "UPDATE ANUNCIOS SET FPrincipal = NULL, Alternativo = 'Pendiente de imagen' WHERE IdAnuncio = ?";
            $stmt_upd = $db->prepare($sql_update);
            $stmt_upd->bind_param("i", $foto['Anuncio']);
            $stmt_upd->execute();
            $stmt_upd->close();
        }

        // --- Borrado físico del archivo ---
        // Verificamos que la ruta no esté vacía y que el archivo exista antes de intentar borrarlo
        if (!empty($foto['Foto']) && file_exists($foto['Foto'])) {
            if (!unlink($foto['Foto'])) {
                // Si falla el borrado físico, lanzamos excepción para deshacer los cambios en BD
                throw new Exception("No se pudo eliminar el archivo del servidor.");
            }
        }
        // ----------------------------------

        // Borrar de la tabla FOTOS
        $stmt_del = $db->prepare("DELETE FROM FOTOS WHERE IdFoto = ?");
        $stmt_del->bind_param("i", $id_foto);
        $stmt_del->execute();
        $stmt_del->close();

        $db->commit();

        // Redirigimos al detalle del anuncio al que pertenecía la foto
        header("Location: ver_anuncio.php?id=" . $foto['Anuncio']);
        exit;
    } catch (Exception $e) {
        $db->rollback();
        $error = "Error al eliminar: " . $e->getMessage();
    }
}

$db->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Eliminar Foto - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/eliminar_foto.css">
</head>

<body>
    <?php require('cabecera.php'); ?>

    <main class="confirmacion-foto">
        <h2>Eliminar Foto</h2>

        <p>Estás a punto de eliminar la foto <strong>"<?php echo htmlspecialchars($foto['Titulo']); ?>"</strong> del anuncio:</p>
        <p><em><?php echo htmlspecialchars($foto['TituloAnuncio']); ?></em></p>

        <div class="marco-foto">
            <img src="<?php echo htmlspecialchars($foto['Foto']); ?>" class="img-preview" alt="Foto a eliminar">
        </div>

        <p>¿Estás seguro? Esta acción no se puede deshacer.</p>

        <?php if ($foto['Foto'] === $foto['FPrincipal']): ?>
            <p style="color: #e67e22; font-size: 0.9em;">
                <strong>Nota:</strong> Esta es la foto de portada actual. Al borrarla, el anuncio se quedará sin imagen principal.
            </p>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <p style="color: red; font-weight: bold;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="post" class="acciones">
            <input type="hidden" name="confirmar" value="1">
            <a href="ver_fotos_privada.php?id=<?php echo $foto['Anuncio']; ?>" class="btn btn-cancelar">Cancelar</a>
            <button type="submit" class="btn btn-peligro">Sí, eliminar foto</button>
        </form>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>