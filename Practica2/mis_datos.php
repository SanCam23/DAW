<?php
session_start();
require_once 'verificar_sesion.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/validaciones.php';

// Inicializar variables
$db = conectarDB();
$usuario = null;
$paises = [];
$errores = [];
$mensaje_exito = '';

// Procesar formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    
    // Recoger datos del formulario
    $nombre_usuario = $_POST['nombre_usuario'] ?? '';
    $email = $_POST['email'] ?? '';
    $sexo = $_POST['sexo'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $ciudad = $_POST['ciudad'] ?? '';
    $pais_form = $_POST['pais'] ?? '';
    $password_actual = $_POST['password_actual'] ?? '';
    $nueva_password = $_POST['nueva_password'] ?? '';
    $confirmar_password = $_POST['confirmar_password'] ?? '';
    
    // 1. VERIFICAR CONTRASEÑA ACTUAL (requerida según PDF)
    if (empty($password_actual)) {
        $errores['password_actual'] = "Debe introducir su contraseña actual para confirmar los cambios.";
    } else {
        // Verificar contraseña actual en la BD
        $sql_verificar = "SELECT Clave, NomUsuario FROM USUARIOS WHERE IdUsuario = ?";
        $stmt_verificar = $db->prepare($sql_verificar);
        $stmt_verificar->bind_param("i", $usuario_id);
        $stmt_verificar->execute();
        $resultado_verificar = $stmt_verificar->get_result();
        
        if ($fila = $resultado_verificar->fetch_assoc()) {
            if (!password_verify($password_actual, $fila['Clave'])) {
                $errores['password_actual'] = "La contraseña actual es incorrecta.";
            }
        }
        $stmt_verificar->close();
    }
    
    // 2. VALIDAR DATOS (usando nuestro fichero de validaciones)
    if (empty($errores)) {
        $datos_formulario = [
            'usuario' => $nombre_usuario, // Usar el nuevo nombre de usuario del formulario
            'email' => $email,
            'sexo' => $sexo,
            'fecha_nacimiento' => $fecha_nacimiento,
            'ciudad' => $ciudad
        ];
        
        // Solo validar nueva contraseña si se proporciona
        if (!empty($nueva_password)) {
            $datos_formulario['password'] = $nueva_password;
            $datos_formulario['confirm_password'] = $confirmar_password;
        }
        
        $resultado_validacion = validarFormularioUsuario($datos_formulario, true);
        
        if ($resultado_validacion !== true) {
            $errores = array_merge($errores, $resultado_validacion);
        }
    }
    
    // 3. VERIFICAR SI EL NUEVO NOMBRE DE USUARIO YA EXISTE (si se cambió)
    if (empty($errores)) {
        // Obtener el nombre de usuario actual de la BD
        $sql_nombre_actual = "SELECT NomUsuario FROM USUARIOS WHERE IdUsuario = ?";
        $stmt_nombre_actual = $db->prepare($sql_nombre_actual);
        $stmt_nombre_actual->bind_param("i", $usuario_id);
        $stmt_nombre_actual->execute();
        $resultado_nombre_actual = $stmt_nombre_actual->get_result();
        $nombre_usuario_actual = '';
        if ($fila_nombre = $resultado_nombre_actual->fetch_assoc()) {
            $nombre_usuario_actual = $fila_nombre['NomUsuario'];
        }
        $stmt_nombre_actual->close();
        
        // Solo verificar si el nombre de usuario es diferente al actual
        if ($nombre_usuario !== $nombre_usuario_actual) {
            $sql_check_usuario = "SELECT IdUsuario FROM USUARIOS WHERE NomUsuario = ? AND IdUsuario != ?";
            $stmt_check = $db->prepare($sql_check_usuario);
            $stmt_check->bind_param("si", $nombre_usuario, $usuario_id);
            $stmt_check->execute();
            $resultado_check = $stmt_check->get_result();
            
            if ($resultado_check && $resultado_check->num_rows > 0) {
                $errores['nombre_usuario'] = "El nombre de usuario ya está en uso por otro usuario.";
            }
            $stmt_check->close();
        }
    }
    
    // 4. ACTUALIZAR EN BD SI NO HAY ERRORES
    if (empty($errores)) {
        // Preparar datos para UPDATE
        $fecha_mysql = DateTime::createFromFormat('d/m/Y', $fecha_nacimiento)->format('Y-m-d');
        $ciudad = empty(trim($ciudad)) ? null : trim($ciudad);
        $pais_form = empty($pais_form) ? null : $pais_form;
        
        // Construir consulta UPDATE dinámica
        $sql_update = "UPDATE USUARIOS SET NomUsuario = ?, Email = ?, Sexo = ?, FNacimiento = ?, Ciudad = ?, Pais = ?";
        $tipos = "sssssi";
        $parametros = [$nombre_usuario, $email, $sexo, $fecha_mysql, $ciudad, $pais_form];
        
        // Agregar nueva contraseña si se proporcionó
        if (!empty($nueva_password)) {
            $sql_update .= ", Clave = ?";
            $tipos .= "s";
            $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
            $parametros[] = $password_hash;
        }
        
        $sql_update .= " WHERE IdUsuario = ?";
        $tipos .= "i";
        $parametros[] = $usuario_id;
        
        // Ejecutar UPDATE
        $stmt_update = $db->prepare($sql_update);
        // Ejecutar UPDATE
    $stmt_update = $db->prepare($sql_update);
    if ($stmt_update) {
        $stmt_update->bind_param($tipos, ...$parametros);
        
        // En la sección de actualización exitosa, REEMPLAZA todo desde:
if ($stmt_update->execute()) {
    $mensaje_exito = "Datos actualizados correctamente.";
    
    // Actualizar variables de sesión con los nuevos datos
    $_SESSION['nombre_usuario'] = $nombre_usuario;
    
    // Solo limpiar cookies si cambió usuario o contraseña
    
    // Obtener el nombre de usuario actual de la BD para comparar
    $sql_nombre_actual = "SELECT NomUsuario FROM USUARIOS WHERE IdUsuario = ?";
    $stmt_nombre_actual = $db->prepare($sql_nombre_actual);
    $stmt_nombre_actual->bind_param("i", $usuario_id);
    $stmt_nombre_actual->execute();
    $resultado_nombre_actual = $stmt_nombre_actual->get_result();
    $nombre_usuario_actual_bd = '';
    if ($fila_nombre = $resultado_nombre_actual->fetch_assoc()) {
        $nombre_usuario_actual_bd = $fila_nombre['NomUsuario'];
    }
    $stmt_nombre_actual->close();
    
    // Determinar si cambió usuario o contraseña
    $cambio_usuario = ($nombre_usuario !== $nombre_usuario_actual_bd);
    $cambio_password = !empty($nueva_password);
    
    // Solo limpiar cookies si cambió usuario O contraseña
    if ($cambio_usuario || $cambio_password) {
        // Limpiar tokens del archivo
        if (file_exists('tokens.txt')) {
            $tokens = file('tokens.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $nuevos_tokens = [];
            
            foreach ($tokens as $linea) {
                list($token_usuario_id, $token_guardado, $expiracion_guardada) = explode(':', $linea);
                // Mantener solo tokens de otros usuarios
                if ($token_usuario_id != $usuario_id) {
                    $nuevos_tokens[] = $linea;
                }
            }
            
            file_put_contents('tokens.txt', implode("\n", $nuevos_tokens) . "\n");
        }

        // Limpiar también las cookies relacionadas
        $cookies_a_limpiar = ['recordarme_token', 'ultima_visita_timestamp', 'estilo_css'];
        foreach ($cookies_a_limpiar as $cookie_name) {
            if (isset($_COOKIE[$cookie_name])) {
                setcookie($cookie_name, '', [
                    'expires' => time() - 3600,
                    'path' => '/',
                    'httponly' => ($cookie_name !== 'estilo_css')
                ]);
            }
        }
        
        // Redirigir con mensaje específico
        header("Location: res_mis_datos.php?mensaje=" . urlencode("Datos actualizados correctamente. Debido a cambios en sus credenciales, deberá volver a iniciar sesión."));
        exit();
    } else {
        // Solo cambió otros datos, mantener cookies
        header("Location: res_mis_datos.php?mensaje=" . urlencode("Datos actualizados correctamente."));
        exit();
    }
    
} else {
    $errores[] = "Error al actualizar los datos en la base de datos: " . $stmt_update->error;
}
            $stmt_update->close();
        } else {
            $errores[] = "Error al preparar la consulta de actualización.";
        }
    }
}

// Obtener datos actualizados del usuario desde la BD
if ($db && isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];

    // Obtener datos del usuario
    $sql_usuario = "SELECT u.*, p.NomPais, e.Nombre as NombreEstilo 
                    FROM USUARIOS u 
                    LEFT JOIN PAISES p ON u.Pais = p.IdPais 
                    LEFT JOIN ESTILOS e ON u.Estilo = e.IdEstilo 
                    WHERE u.IdUsuario = ?";

    $stmt = $db->prepare($sql_usuario);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
    }
    $stmt->close();

    // Cargar países para el desplegable
    $sql_paises = "SELECT IdPais, NomPais FROM PAISES ORDER BY NomPais ASC";
    $resultado_paises = $db->query($sql_paises);
    if ($resultado_paises) {
        $paises = $resultado_paises->fetch_all(MYSQLI_ASSOC);
        $resultado_paises->close();
    }

    $db->close();
}

// Si no se encontró el usuario
if ($usuario === null) {
    echo "Error: No se pudieron cargar los datos del usuario.";
    exit;
}

// Formatear fecha para mostrar
$fecha_nacimiento_formateada = $usuario['FNacimiento'] ? date("d/m/Y", strtotime($usuario['FNacimiento'])) : '';
$fecha_registro_formateada = $usuario['FRegistro'] ? date("d/m/Y", strtotime($usuario['FRegistro'])) : '';

// Mapear sexo a texto 
$sexo_texto = '';
switch ($usuario['Sexo']) {
    case 1:
        $sexo_texto = 'masculino';
        break;
    case 2:
        $sexo_texto = 'femenino';
        break;
    default:
        $sexo_texto = 'No especificado';
}

$zona = 'privada';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Mis datos de usuario en VENTAPLUS.">
    <meta name="keywords" content="usuario, perfil, datos personales, VENTAPLUS">
    <meta name="author" content="Santino Campessi Lojo">
    <meta name="author" content="Mario Laguna Contreras">
    <title>Mis datos - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/mis_datos.css">
    <link rel="stylesheet" type="text/css" href="css/print_formulario.css" media="print">
</head>

<body>
    <?php require('cabecera.php'); ?>

    <main>
        <h2>Mis datos</h2>

        <!-- Mostrar mensajes -->
        <?php if (!empty($mensaje_exito)): ?>
            <div class="mensaje-exito"><?php echo $mensaje_exito; ?></div>
        <?php endif; ?>

        <?php if (!empty($errores)): ?>
            <div class="mensaje-error">
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Información actual del usuario -->
        <section class="info-actual">
            <h3>Mis datos actuales</h3>

            <div class="foto-perfil">
                <img src="<?php echo $usuario['Foto'] ?: 'img/default-profile.png'; ?>" alt="Foto de perfil">
            </div>

            <div class="datos-actuales">
                <p><strong>Usuario:</strong> <?php echo $usuario['NomUsuario']; ?></p>
                <p><strong>Email:</strong> <?php echo $usuario['Email']; ?></p>
                <p><strong>Sexo:</strong> <?php echo $sexo_texto; ?></p>
                <p><strong>Fecha nacimiento:</strong> <?php echo $fecha_nacimiento_formateada; ?></p>
                <p><strong>Ciudad:</strong> <?php echo $usuario['Ciudad'] ?: 'No especificada'; ?></p>
                <p><strong>País:</strong> <?php echo $usuario['NomPais'] ?: 'No especificado'; ?></p>
                <p><strong>Estilo:</strong> <?php echo $usuario['NombreEstilo'] ?: 'Normal'; ?></p>
                <p><strong>Miembro desde:</strong> <?php echo $fecha_registro_formateada; ?></p>
            </div>
        </section>

        <!-- Formulario HABILITADO para modificar datos -->
        <form id="form-mis-datos" action="mis_datos.php" method="POST" enctype="multipart/form-data">
            <h3>Modificar mis datos</h3>

            <!-- Campos principales -->
            <label for="nombre_usuario">Nombre de usuario:</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" value="<?php echo htmlspecialchars($usuario['NomUsuario']); ?>">
            <?php if (isset($errores['usuario'])): ?>
                <span class="error"><?php echo $errores['usuario']; ?></span>
            <?php endif; ?>
            <?php if (isset($errores['nombre_usuario'])): ?>
                <span class="error"><?php echo $errores['nombre_usuario']; ?></span>
            <?php endif; ?>

            <label for="email">Correo electrónico:</label>
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($usuario['Email']); ?>">
            <?php if (isset($errores['email'])): ?>
                <span class="error"><?php echo $errores['email']; ?></span>
            <?php endif; ?>

            <label for="sexo">Sexo:</label>
            <select id="sexo" name="sexo">
                <option value="">Selecciona tu sexo</option>
                <option value="1" <?php echo $usuario['Sexo'] == 1 ? 'selected' : ''; ?>>Masculino</option>
                <option value="2" <?php echo $usuario['Sexo'] == 2 ? 'selected' : ''; ?>>Femenino</option>
            </select>
            <?php if (isset($errores['sexo'])): ?>
                <span class="error"><?php echo $errores['sexo']; ?></span>
            <?php endif; ?>

            <label for="fecha_nacimiento">Fecha de nacimiento:</label>
            <input type="text" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo $fecha_nacimiento_formateada; ?>" placeholder="dd/mm/yyyy">
            <?php if (isset($errores['fecha_nacimiento'])): ?>
                <span class="error"><?php echo $errores['fecha_nacimiento']; ?></span>
            <?php endif; ?>

            <label for="ciudad">Ciudad:</label>
            <input type="text" id="ciudad" name="ciudad" value="<?php echo htmlspecialchars($usuario['Ciudad'] ?? ''); ?>">
            <?php if (isset($errores['ciudad'])): ?>
                <span class="error"><?php echo $errores['ciudad']; ?></span>
            <?php endif; ?>

            <label for="pais">País:</label>
            <select id="pais" name="pais">
                <option value="">-- Seleccione un país --</option>
                <?php if (empty($paises)): ?>
                    <option value="" disabled>Error al cargar países</option>
                <?php else: ?>
                    <?php foreach ($paises as $pais_item): ?>
                        <option value="<?php echo $pais_item['IdPais']; ?>"
                            <?php echo ($usuario['Pais'] == $pais_item['IdPais']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($pais_item['NomPais']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <!-- Campos de contraseña (opcionales según PDF) -->
            <h4>Cambiar contraseña (opcional)</h4>
            
            <label for="nueva_password">Nueva contraseña:</label>
            <input type="password" id="nueva_password" name="nueva_password" placeholder="Dejar vacío para no cambiar">
            <?php if (isset($errores['password'])): ?>
                <span class="error"><?php echo $errores['password']; ?></span>
            <?php endif; ?>

            <label for="confirmar_password">Repetir nueva contraseña:</label>
            <input type="password" id="confirmar_password" name="confirmar_password" placeholder="Repetir nueva contraseña">
            <?php if (isset($errores['confirm_password'])): ?>
                <span class="error"><?php echo $errores['confirm_password']; ?></span>
            <?php endif; ?>

            <!-- Contraseña actual (OBLIGATORIA según PDF) -->
            <h4>Confirmación de seguridad</h4>
            
            <label for="password_actual">Contraseña actual:</label>
            <input type="password" id="password_actual" name="password_actual" placeholder="Debe introducir su contraseña actual">
            <?php if (isset($errores['password_actual'])): ?>
                <span class="error"><?php echo $errores['password_actual']; ?></span>
            <?php endif; ?>

            <label for="foto">Foto de perfil:</label>
            <input type="file" id="foto" name="foto" accept="image/*">

            <button type="submit">Guardar cambios</button>
        </form>

        <p style="text-align: center; margin-top: 20px;">
            <a href="menu_usuario_registrado.php" class="volver">Volver al menú de usuario</a>
        </p>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>