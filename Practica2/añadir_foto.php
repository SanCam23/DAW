<?php
session_start();
require_once 'verificar_sesion.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/validaciones_foto.php';
require_once __DIR__ . '/includes/gestor_imagenes.php';

$zona = 'privada';
$db = conectarDB();

// Inicializar variables
$id_anuncio = $_GET['id'] ?? $_POST['anuncio_id'] ?? 0;
$usuario_id = $_SESSION['usuario_id'];
$errores = [];
$mensaje_exito = "";

// Variables para el formulario
$titulo_foto = "";
$alt_foto = "";

// Verificar que el anuncio existe y pertenece al usuario
$anuncio_valido = false;
$titulo_anuncio = "";
$tiene_portada = false;

if ($id_anuncio) {
    // Consultar si el anuncio es del usuario y tiene foto principal
    $stmt = $db->prepare("SELECT Titulo, FPrincipal FROM ANUNCIOS WHERE IdAnuncio = ? AND Usuario = ?");
    $stmt->bind_param("ii", $id_anuncio, $usuario_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($fila = $res->fetch_assoc()) {
        $anuncio_valido = true;
        $titulo_anuncio = $fila['Titulo'];

        // Comprobar si tiene una foto principal asignada válida
        if (!empty($fila['FPrincipal']) && $fila['FPrincipal'] !== 'Pendiente de imagen') {
            $tiene_portada = true;
        }
    }
    $stmt->close();
}

// Si el anuncio no es válido, redirigir
if (!$anuncio_valido) {
    header("Location: misanuncios.php");
    exit();
}

// Procesar formulario POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo_foto = trim($_POST['titulo'] ?? '');
    $alt_foto = trim($_POST['alt'] ?? '');

    // Validar datos de texto
    $errores = validarDatosFoto($titulo_foto, $alt_foto);

    // Procesar la subida de la imagen
    $ruta_foto_bd = null;
    $resultado_subida = subirImagen($_FILES['fichero'] ?? null);

    if ($resultado_subida['error']) {
        $errores[] = $resultado_subida['error'];
    } elseif ($resultado_subida['ruta']) {
        $ruta_foto_bd = $resultado_subida['ruta'];
    } else {
        $errores[] = "Debes seleccionar un archivo de imagen.";
    }

    // Si no hay errores, procedemos a la inserción en BD
    if (empty($errores) && $ruta_foto_bd) {

        $db->begin_transaction();
        try {
            // Insertar en la tabla FOTOS
            $sql_insert = "INSERT INTO FOTOS (Titulo, Foto, Alternativo, Anuncio) VALUES (?, ?, ?, ?)";
            $stmt_ins = $db->prepare($sql_insert);
            $stmt_ins->bind_param("sssi", $titulo_foto, $ruta_foto_bd, $alt_foto, $id_anuncio);
            $stmt_ins->execute();
            $stmt_ins->close();

            // Si el anuncio no tenía portada, actualizar ANUNCIOS
            if (!$tiene_portada) {
                $sql_update = "UPDATE ANUNCIOS SET FPrincipal = ?, Alternativo = ? WHERE IdAnuncio = ?";
                $stmt_upd = $db->prepare($sql_update);
                $stmt_upd->bind_param("ssi", $ruta_foto_bd, $alt_foto, $id_anuncio);
                $stmt_upd->execute();
                $stmt_upd->close();
            }

            $db->commit();
            $mensaje_exito = "La fotografía se ha añadido correctamente a la galería.";
            
            // Limpiar campos del formulario para nueva entrada
            $titulo_foto = "";
            $alt_foto = "";
            
        } catch (Exception $e) {
            $db->rollback();
            // Si falló la BD, eliminar la foto subida para mantener consistencia
            if (file_exists($ruta_foto_bd)) {
                unlink($ruta_foto_bd);
            }
            $errores[] = "Error al guardar en la base de datos: " . $e->getMessage();
        }
    }
}

$db->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Añadir Foto - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/crear_anuncio.css">
</head>

<body>
    <?php require('cabecera.php'); ?>

    <main>
        <?php if ($mensaje_exito): ?>
            <section class="confirmacion">
                <h3><?php echo $mensaje_exito; ?></h3>
                <p>La foto se ha registrado asociada al anuncio: <strong><?php echo htmlspecialchars($titulo_anuncio); ?></strong></p>

                <a href="misanuncios.php" class="boton-crear">Volver a mis anuncios</a>
                <br><br>
                <a href="añadir_foto.php?id=<?php echo $id_anuncio; ?>">Añadir otra foto</a>
            </section>

        <?php else: ?>
            <h2>Añadir foto al anuncio</h2>
            <p style="text-align: center; margin-bottom: 20px;">Estás añadiendo una foto a: <strong><?php echo htmlspecialchars($titulo_anuncio); ?></strong></p>

            <?php if (!empty($errores)): ?>
                <div class="errores">
                    <ul>
                        <?php foreach ($errores as $e) echo "<li>$e</li>"; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="añadir_foto.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="anuncio_id" value="<?php echo $id_anuncio; ?>">

                <fieldset>
                    <legend>Datos de la fotografía</legend>

                    <label for="fichero">Archivo de imagen*:</label><br>
                    <input type="file" id="fichero" name="fichero" accept="image/*" required><br><br>

                    <label for="titulo">Título de la foto*:</label><br>
                    <input type="text" id="titulo" name="titulo"
                        value="<?php echo htmlspecialchars($titulo_foto); ?>" required><br><br>

                    <label for="alt">Texto Alternativo*:</label><br>
                    <span class="nota-campo" style="font-size: 0.9em; color: #666;">(Mínimo 10 caracteres, no empezar por 'foto' o 'imagen')</span>
                    <input type="text" id="alt" name="alt"
                        value="<?php echo htmlspecialchars($alt_foto); ?>" required><br><br>

                    <button type="submit">Añadir Foto</button>
                </fieldset>
            </form>

            <p style="text-align: center; margin-top: 15px;">
                <a href="misanuncios.php">Cancelar y volver</a>
            </p>
        <?php endif; ?>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>