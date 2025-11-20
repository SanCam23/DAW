<?php
session_start();
require_once 'verificar_sesion.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/validaciones_foto.php';

$zona = 'privada';
$db = conectarDB();

// Inicializar variables
$id_anuncio = $_GET['id'] ?? $_POST['anuncio_id'] ?? 0;
$usuario_id = $_SESSION['usuario_id'];
$errores = [];
$mensaje_exito = "";
$nombre_fichero_manual = ""; // Para el mensaje de aviso

// Variables para el formulario
$titulo_foto = "";
$alt_foto = "";

// 1. Verificar que el anuncio existe y pertenece al usuario logueado
$anuncio_valido = false;
$titulo_anuncio = "";
$tiene_portada = false;

if ($id_anuncio) {
    // Consultamos si el anuncio es del usuario y si ya tiene foto principal
    $stmt = $db->prepare("SELECT Titulo, FPrincipal FROM ANUNCIOS WHERE IdAnuncio = ? AND Usuario = ?");
    $stmt->bind_param("ii", $id_anuncio, $usuario_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($fila = $res->fetch_assoc()) {
        $anuncio_valido = true;
        $titulo_anuncio = $fila['Titulo'];

        // Comprobamos si tiene una foto principal real asignada
        // (Ignoramos si es el texto provisional 'Pendiente de imagen')
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

// 2. Procesar el formulario POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo_foto = trim($_POST['titulo'] ?? '');
    $alt_foto = trim($_POST['alt'] ?? '');

    // A. Validar datos de texto (función externa)
    $errores = validarDatosFoto($titulo_foto, $alt_foto);

    // B. Validar que se ha seleccionado un archivo
    // Aunque no lo guardemos físicamente, necesitamos su nombre para la BD.
    if (!isset($_FILES['fichero']) || $_FILES['fichero']['error'] === UPLOAD_ERR_NO_FILE) {
        $errores[] = "Debes seleccionar un archivo de imagen.";
    } elseif ($_FILES['fichero']['error'] !== UPLOAD_ERR_OK) {
        $errores[] = "Error en la subida del archivo. Código: " . $_FILES['fichero']['error'];
    }

    // Si no hay errores, procedemos a la inserción en BD
    if (empty($errores)) {

        // Obtenemos solo el nombre del archivo (sin moverlo, cumpliendo el PDF)
        $nombre_archivo = basename($_FILES['fichero']['name']);

        // Construimos la ruta que se guardará en la BD
        // NOTA: Usamos time() para evitar duplicados de nombre en la BD, 
        // aunque el fichero físico tendrás que renombrarlo tú al subirlo a mano.
        $nombre_unico = time() . "_" . $nombre_archivo;
        $ruta_bd = "img/" . $nombre_unico;
        $nombre_fichero_manual = $nombre_unico; // Para mostrarselo al usuario

        // Inserción en base de datos
        $db->begin_transaction();
        try {
            // 1. Insertar en la tabla FOTOS
            $sql_insert = "INSERT INTO FOTOS (Titulo, Foto, Alternativo, Anuncio) VALUES (?, ?, ?, ?)";
            $stmt_ins = $db->prepare($sql_insert);
            $stmt_ins->bind_param("sssi", $titulo_foto, $ruta_bd, $alt_foto, $id_anuncio);
            $stmt_ins->execute();
            $stmt_ins->close();

            // 2. Si el anuncio no tenía portada, actualizamos la tabla ANUNCIOS
            if (!$tiene_portada) {
                $sql_update = "UPDATE ANUNCIOS SET FPrincipal = ?, Alternativo = ? WHERE IdAnuncio = ?";
                $stmt_upd = $db->prepare($sql_update);
                $stmt_upd->bind_param("ssi", $ruta_bd, $alt_foto, $id_anuncio);
                $stmt_upd->execute();
                $stmt_upd->close();
            }

            $db->commit();
            $mensaje_exito = "Inserción realizada en la base de datos.";
        } catch (Exception $e) {
            $db->rollback();
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

                <div style="background-color: #fff3cd; border: 1px solid #ffeeba; padding: 15px; margin: 15px 0; border-radius: 5px; color: #856404;">
                    <strong>⚠️ RECORDATORIO IMPORTANTE (Práctica 10):</strong><br>
                    Como indica el enunciado, el almacenamiento automático no está implementado.
                    <br><br>
                    Debes copiar <strong>manualmente</strong> tu archivo de imagen a la carpeta <code>Practica2/img/</code> con el siguiente nombre para que se vea correctamente:
                    <br><br>
                    <code style="background: #fff; padding: 5px; border: 1px solid #ccc; font-weight: bold;"><?php echo htmlspecialchars($nombre_fichero_manual); ?></code>
                </div>

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