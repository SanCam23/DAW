<?php
session_start();
require_once 'verificar_sesion.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/validaciones.php';
// Gestor de imágenes de usuario
require_once __DIR__ . '/includes/gestor_imagenes.php';

// Inicialización de variables
$db = conectarDB();
$usuario = null;
$paises = [];
$errores = [];
$mensaje_exito = '';

// Procesar formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];

    // Recogida de datos del formulario
    $nombre_usuario = $_POST['nombre_usuario'] ?? '';
    $email = $_POST['email'] ?? '';
    $sexo = $_POST['sexo'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $ciudad = $_POST['ciudad'] ?? '';
    $pais_form = $_POST['pais'] ?? '';
    $password_actual = $_POST['password_actual'] ?? '';
    $nueva_password = $_POST['nueva_password'] ?? '';
    $confirmar_password = $_POST['confirmar_password'] ?? '';

    // Verificar contraseña actual y obtener foto actual
    $foto_actual_bd = null; // Guarda la ruta de la foto antes de actualizar

    if (empty($password_actual)) {
        $errores['password_actual'] = "Debe introducir su contraseña actual para confirmar los cambios.";
    } else {
        // Verificar contraseña actual y obtener foto en la misma consulta
        $sql_verificar = "SELECT Clave, NomUsuario, Foto FROM USUARIOS WHERE IdUsuario = ?";
        $stmt_verificar = $db->prepare($sql_verificar);
        $stmt_verificar->bind_param("i", $usuario_id);
        $stmt_verificar->execute();
        $resultado_verificar = $stmt_verificar->get_result();

        if ($fila = $resultado_verificar->fetch_assoc()) {
            if (!password_verify($password_actual, $fila['Clave'])) {
                $errores['password_actual'] = "La contraseña actual es incorrecta.";
            } else {
                // Guardar datos actuales necesarios
                $nombre_usuario_actual_bd = $fila['NomUsuario'];
                $foto_actual_bd = $fila['Foto']; // Guardamos la ruta vieja
            }
        }
        $stmt_verificar->close();
    }

    // Validación de datos de texto
    if (empty($errores)) {
        $datos_formulario = [
            'usuario' => $nombre_usuario,
            'email' => $email,
            'sexo' => $sexo,
            'fecha_nacimiento' => $fecha_nacimiento,
            'ciudad' => $ciudad
        ];

        if (!empty($nueva_password)) {
            $datos_formulario['password'] = $nueva_password;
            $datos_formulario['confirmar_password'] = $confirmar_password;
        }

        $resultado_validacion = validarFormularioUsuario($datos_formulario, true);

        if ($resultado_validacion !== true) {
            $errores = array_merge($errores, $resultado_validacion);
        }
    }

    // Comprobación de duplicados de nombre de usuario
    if (empty($errores)) {
        if ($nombre_usuario !== $nombre_usuario_actual_bd) {
            $sql_check_usuario = "SELECT IdUsuario FROM USUARIOS WHERE NomUsuario = ? AND IdUsuario != ?";
            $stmt_check = $db->prepare($sql_check_usuario);
            $stmt_check->bind_param("si", $nombre_usuario, $usuario_id);
            $stmt_check->execute();
            $resultado_check = $stmt_check->get_result();

            if ($resultado_check && $resultado_check->num_rows > 0) {
                $errores['nombre_usuario'] = "El nombre de usuario ya está en uso.";
            }
            $stmt_check->close();
        }
    }

    // Lógica de gestión de foto de perfil
    $nueva_ruta_foto = $foto_actual_bd; // Por defecto se mantiene la foto actual
    $borrar_foto_fisica = false;        // Bandera para borrar el archivo anterior

    if (empty($errores)) {
        // Subida de nueva foto (tiene prioridad sobre borrar)
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $resultado_subida = subirImagen($_FILES['foto'], RUTA_FOTOS_PERFIL);

            if ($resultado_subida['error']) {
                $errores['foto'] = $resultado_subida['error'];
            } elseif ($resultado_subida['ruta']) {
                $nueva_ruta_foto = $resultado_subida['ruta'];
                $borrar_foto_fisica = true; // Marcar para borrar la vieja si todo sale bien
            }
        }
        // Borrar foto actual (solo si no se sube una nueva)
        elseif (isset($_POST['borrar_foto']) && $_POST['borrar_foto'] == '1') {
            $nueva_ruta_foto = null;
            $borrar_foto_fisica = true;
        }
    }

    // Actualización de datos en la base de datos
    if (empty($errores)) {
        $fecha_mysql = DateTime::createFromFormat('d/m/Y', $fecha_nacimiento)->format('Y-m-d');
        $ciudad = empty(trim($ciudad)) ? null : trim($ciudad);
        $pais_form = empty($pais_form) ? null : $pais_form;

        // Construcción de la consulta dinámica para actualizar usuario
        $sql_update = "UPDATE USUARIOS SET NomUsuario = ?, Email = ?, Sexo = ?, FNacimiento = ?, Ciudad = ?, Pais = ?, Foto = ?";
        $tipos = "sssssis";
        $parametros = [$nombre_usuario, $email, $sexo, $fecha_mysql, $ciudad, $pais_form, $nueva_ruta_foto];

        if (!empty($nueva_password)) {
            $sql_update .= ", Clave = ?";
            $tipos .= "s";
            $parametros[] = password_hash($nueva_password, PASSWORD_DEFAULT);
        }

        $sql_update .= " WHERE IdUsuario = ?";
        $tipos .= "i";
        $parametros[] = $usuario_id;

        $stmt_update = $db->prepare($sql_update);
        if ($stmt_update) {
            $stmt_update->bind_param($tipos, ...$parametros);

            if ($stmt_update->execute()) {
                // Si se debe borrar la foto anterior y existe, se elimina físicamente
                if ($borrar_foto_fisica && !empty($foto_actual_bd) && $foto_actual_bd !== $nueva_ruta_foto) {
                    if (file_exists($foto_actual_bd)) {
                        unlink($foto_actual_bd); // Borrado físico
                    }
                }

                $_SESSION['nombre_usuario'] = $nombre_usuario;

                // Lógica de cookies (sesión/recordarme) si cambian credenciales
                $cambio_usuario = ($nombre_usuario !== $nombre_usuario_actual_bd);
                $cambio_password = !empty($nueva_password);

                if ($cambio_usuario || $cambio_password) {
                    // Limpieza de tokens y cookies
                    if (file_exists('tokens.txt')) {
                        $tokens = file('tokens.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                        $nuevos_tokens = [];
                        foreach ($tokens as $linea) {
                            list($token_uid, , ) = explode(':', $linea);
                            if ($token_uid != $usuario_id)
                                $nuevos_tokens[] = $linea;
                        }
                        file_put_contents('tokens.txt', implode("\n", $nuevos_tokens) . "\n");
                    }
                    $cookies = ['recordarme_token', 'ultima_visita_timestamp', 'estilo_css'];
                    foreach ($cookies as $c) {
                        if (isset($_COOKIE[$c]))
                            setcookie($c, '', time() - 3600, '/', "", false, ($c !== 'estilo_css'));
                    }
                    header("Location: res_mis_datos.php?mensaje=" . urlencode("Datos y foto actualizados. Credenciales cambiadas, vuelva a iniciar sesión si es necesario."));
                } else {
                    header("Location: res_mis_datos.php?mensaje=" . urlencode("Datos actualizados correctamente."));
                }
                exit();

            } else {
                $errores[] = "Error al actualizar BD: " . $stmt_update->error;
            }
            $stmt_update->close();
        } else {
            $errores[] = "Error al preparar actualización.";
        }
    }
}

// Carga de datos para mostrar el formulario
if ($db && isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $sql_usuario = "SELECT u.*, p.NomPais, e.Nombre as NombreEstilo FROM USUARIOS u LEFT JOIN PAISES p ON u.Pais = p.IdPais LEFT JOIN ESTILOS e ON u.Estilo = e.IdEstilo WHERE u.IdUsuario = ?";
    $stmt = $db->prepare($sql_usuario);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0)
        $usuario = $res->fetch_assoc();
    $stmt->close();

    $res_paises = $db->query("SELECT IdPais, NomPais FROM PAISES ORDER BY NomPais ASC");
    if ($res_paises) {
        $paises = $res_paises->fetch_all(MYSQLI_ASSOC);
        $res_paises->close();
    }
    $db->close();
}

if ($usuario === null) {
    echo "Error cargando usuario.";
    exit;
}

// Formateo de datos para mostrar en el formulario
$fecha_nac = $usuario['FNacimiento'] ? date("d/m/Y", strtotime($usuario['FNacimiento'])) : '';
$fecha_reg = $usuario['FRegistro'] ? date("d/m/Y", strtotime($usuario['FRegistro'])) : '';
$sexo_txt = ($usuario['Sexo'] == 1) ? 'masculino' : (($usuario['Sexo'] == 2) ? 'femenino' : 'No especificado');
$zona = 'privada';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis datos - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/mis_datos.css">
    <link rel="stylesheet" type="text/css" href="css/print_formulario.css" media="print">
</head>

<body>
    <?php require('cabecera.php'); ?>

    <main>
        <h2>Mis datos</h2>

        <?php if (!empty($mensaje_exito)): ?>
            <div class="mensaje-exito"><?php echo $mensaje_exito; ?></div>
        <?php endif; ?>

        <?php if (!empty($errores)): ?>
            <div class="mensaje-error">
                <ul><?php foreach ($errores as $e)
                    echo "<li>$e</li>"; ?></ul>
            </div>
        <?php endif; ?>

        <section class="info-actual">
            <h3>Mis datos actuales</h3>
            <div class="foto-perfil">
                <img src="<?php echo !empty($usuario['Foto']) ? htmlspecialchars($usuario['Foto']) : 'img/sin_fto.webp'; ?>"
                    alt="Foto de perfil">
            </div>
            <div class="datos-actuales">
                <p><strong>Usuario:</strong> <?php echo htmlspecialchars($usuario['NomUsuario']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['Email']); ?></p>
                <p><strong>Sexo:</strong> <?php echo $sexo_txt; ?></p>
                <p><strong>Fecha nacimiento:</strong> <?php echo $fecha_nac; ?></p>
                <p><strong>Ciudad:</strong> <?php echo htmlspecialchars($usuario['Ciudad'] ?: 'No especificada'); ?></p>
                <p><strong>País:</strong> <?php echo htmlspecialchars($usuario['NomPais'] ?: 'No especificado'); ?></p>
                <p><strong>Estilo:</strong> <?php echo htmlspecialchars($usuario['NombreEstilo'] ?: 'Normal'); ?></p>
                <p><strong>Miembro desde:</strong> <?php echo $fecha_reg; ?></p>
            </div>
        </section>

        <form id="form-mis-datos" action="mis_datos.php" method="POST" enctype="multipart/form-data">
            <h3>Modificar mis datos</h3>

            <label for="nombre_usuario">Nombre de usuario:</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario"
                value="<?php echo htmlspecialchars($usuario['NomUsuario']); ?>">

            <label for="email">Correo electrónico:</label>
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($usuario['Email']); ?>">

            <label for="sexo">Sexo:</label>
            <select id="sexo" name="sexo">
                <option value="">Selecciona tu sexo</option>
                <option value="1" <?php echo $usuario['Sexo'] == 1 ? 'selected' : ''; ?>>Masculino</option>
                <option value="2" <?php echo $usuario['Sexo'] == 2 ? 'selected' : ''; ?>>Femenino</option>
            </select>

            <label for="fecha_nacimiento">Fecha de nacimiento:</label>
            <input type="text" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo $fecha_nac; ?>"
                placeholder="dd/mm/yyyy">

            <label for="ciudad">Ciudad:</label>
            <input type="text" id="ciudad" name="ciudad"
                value="<?php echo htmlspecialchars($usuario['Ciudad'] ?? ''); ?>">

            <label for="pais">País:</label>
            <select id="pais" name="pais">
                <option value="">-- Seleccione un país --</option>
                <?php foreach ($paises as $p): ?>
                    <option value="<?php echo $p['IdPais']; ?>" <?php echo ($usuario['Pais'] == $p['IdPais']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($p['NomPais']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <h4>Cambiar contraseña (opcional)</h4>
            <label for="nueva_password">Nueva contraseña:</label>
            <input type="password" id="nueva_password" name="nueva_password" placeholder="Dejar vacío para no cambiar">

            <label for="confirmar_password">Repetir nueva contraseña:</label>
            <input type="password" id="confirmar_password" name="confirmar_password"
                placeholder="Repetir nueva contraseña">

            <h4>Foto de Perfil</h4>
            <label for="foto">Subir nueva foto:</label>
            <input type="file" id="foto" name="foto" accept="image/*">

            <?php if (!empty($usuario['Foto'])): ?>
                <div
                    style="margin-top: 10px; padding: 10px; background: #fff3cd; border: 1px solid #ffeeba; border-radius: 5px;">
                    <label style="display:inline; font-weight:normal; color: #856404;">
                        <input type="checkbox" name="borrar_foto" value="1" style="width:auto; margin-right:5px;">
                        Eliminar foto de perfil actual
                    </label>
                </div>
            <?php endif; ?>

            <h4>Confirmación de seguridad</h4>
            <label for="password_actual">Contraseña actual (Obligatoria):</label>
            <input type="password" id="password_actual" name="password_actual"
                placeholder="Introduce tu contraseña actual" required>

            <button type="submit">Guardar cambios</button>
        </form>

        <p style="text-align: center; margin-top: 20px;">
            <a href="menu_usuario_registrado.php" class="volver">Volver al menú de usuario</a>
        </p>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>