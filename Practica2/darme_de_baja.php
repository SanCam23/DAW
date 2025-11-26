<?php
session_start();
require_once 'verificar_sesion.php';
require_once __DIR__ . '/db.php';

// Inicializar variables
$db = conectarDB();
$usuario = null;
$anuncios_usuario = [];
$total_fotos = 0;
$total_anuncios = 0;
$errores = [];

// Obtener datos del usuario y sus anuncios
if ($db && isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];

    // Obtener datos del usuario
    $sql_usuario = "SELECT u.*, p.NomPais 
                    FROM USUARIOS u 
                    LEFT JOIN PAISES p ON u.Pais = p.IdPais 
                    WHERE u.IdUsuario = ?";
    $stmt = $db->prepare($sql_usuario);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado && $resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
    }
    $stmt->close();

    // Obtener anuncios del usuario con conteo de fotos
    $sql_anuncios = "SELECT a.IdAnuncio, a.Titulo, COUNT(f.IdFoto) as num_fotos 
                     FROM ANUNCIOS a 
                     LEFT JOIN FOTOS f ON a.IdAnuncio = f.Anuncio 
                     WHERE a.Usuario = ? 
                     GROUP BY a.IdAnuncio, a.Titulo";
    $stmt_anuncios = $db->prepare($sql_anuncios);
    $stmt_anuncios->bind_param("i", $usuario_id);
    $stmt_anuncios->execute();
    $resultado_anuncios = $stmt_anuncios->get_result();
    
    if ($resultado_anuncios) {
        $anuncios_usuario = $resultado_anuncios->fetch_all(MYSQLI_ASSOC);
        $total_anuncios = count($anuncios_usuario);
        
        // Calcular total de fotos
        foreach ($anuncios_usuario as $anuncio) {
            $total_fotos += $anuncio['num_fotos'];
        }
    }
    $stmt_anuncios->close();
    $db->close();
}

// Procesar baja si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password_actual = $_POST['password_actual'] ?? '';
    $confirmar_baja = isset($_POST['confirmar_baja']);
    
    if (empty($password_actual)) {
        $errores[] = "Debe introducir su contraseña actual para confirmar la baja.";
    } else {
        // Verificar contraseña actual
        $db = conectarDB();
        $sql_verificar = "SELECT Clave FROM USUARIOS WHERE IdUsuario = ?";
        $stmt_verificar = $db->prepare($sql_verificar);
        $stmt_verificar->bind_param("i", $_SESSION['usuario_id']);
        $stmt_verificar->execute();
        $resultado_verificar = $stmt_verificar->get_result();
        
        if ($fila = $resultado_verificar->fetch_assoc()) {
            if (!password_verify($password_actual, $fila['Clave'])) {
                $errores[] = "La contraseña actual es incorrecta.";
            } else if ($confirmar_baja) {
                // CONFIRMACIÓN - ELIMINAR USUARIO Y DATOS
                $db->begin_transaction();
                
                try {
                    $usuario_id = $_SESSION['usuario_id'];
                    
                    // 1. Obtener IDs de anuncios del usuario
                    $sql_anuncios_ids = "SELECT IdAnuncio FROM ANUNCIOS WHERE Usuario = ?";
                    $stmt_anuncios_ids = $db->prepare($sql_anuncios_ids);
                    $stmt_anuncios_ids->bind_param("i", $usuario_id);
                    $stmt_anuncios_ids->execute();
                    $resultado_anuncios_ids = $stmt_anuncios_ids->get_result();
                    
                    $anuncios_ids = [];
                    while ($fila_anuncio = $resultado_anuncios_ids->fetch_assoc()) {
                        $anuncios_ids[] = $fila_anuncio['IdAnuncio'];
                    }
                    $stmt_anuncios_ids->close();
                    
                    // 2. Eliminar mensajes
                    if (!empty($anuncios_ids)) {
                        $placeholders = str_repeat('?,', count($anuncios_ids) - 1) . '?';
                        $sql_mensajes = "DELETE FROM MENSAJES WHERE Anuncio IN ($placeholders)";
                        $stmt_mensajes = $db->prepare($sql_mensajes);
                        $stmt_mensajes->bind_param(str_repeat('i', count($anuncios_ids)), ...$anuncios_ids);
                        $stmt_mensajes->execute();
                        $stmt_mensajes->close();
                    }
                    
                    // También eliminar mensajes donde el usuario es origen o destino
                    $sql_mensajes_usuario = "DELETE FROM MENSAJES WHERE UsuOrigen = ? OR UsuDestino = ?";
                    $stmt_mensajes_usuario = $db->prepare($sql_mensajes_usuario);
                    $stmt_mensajes_usuario->bind_param("ii", $usuario_id, $usuario_id);
                    $stmt_mensajes_usuario->execute();
                    $stmt_mensajes_usuario->close();
                    
                    // 3. Eliminar fotos de los anuncios
                    if (!empty($anuncios_ids)) {
                        $placeholders = str_repeat('?,', count($anuncios_ids) - 1) . '?';
                        $sql_fotos = "DELETE FROM FOTOS WHERE Anuncio IN ($placeholders)";
                        $stmt_fotos = $db->prepare($sql_fotos);
                        $stmt_fotos->bind_param(str_repeat('i', count($anuncios_ids)), ...$anuncios_ids);
                        $stmt_fotos->execute();
                        $stmt_fotos->close();
                    }
                    
                    // 4. Eliminar solicitudes de los anuncios
                    if (!empty($anuncios_ids)) {
                        $placeholders = str_repeat('?,', count($anuncios_ids) - 1) . '?';
                        $sql_solicitudes = "DELETE FROM SOLICITUDES WHERE Anuncio IN ($placeholders)";
                        $stmt_solicitudes = $db->prepare($sql_solicitudes);
                        $stmt_solicitudes->bind_param(str_repeat('i', count($anuncios_ids)), ...$anuncios_ids);
                        $stmt_solicitudes->execute();
                        $stmt_solicitudes->close();
                    }
                    
                    // 5. Eliminar anuncios del usuario
                    $sql_eliminar_anuncios = "DELETE FROM ANUNCIOS WHERE Usuario = ?";
                    $stmt_anuncios = $db->prepare($sql_eliminar_anuncios);
                    $stmt_anuncios->bind_param("i", $usuario_id);
                    $stmt_anuncios->execute();
                    $stmt_anuncios->close();
                    
                    // 6. Eliminar el usuario
                    $sql_eliminar_usuario = "DELETE FROM USUARIOS WHERE IdUsuario = ?";
                    $stmt_usuario = $db->prepare($sql_eliminar_usuario);
                    $stmt_usuario->bind_param("i", $usuario_id);
                    $stmt_usuario->execute();
                    $stmt_usuario->close();
                    
                    // Confirmar transacción
                    $db->commit();
                    
                    // Limpiar sesión y cookies
                    session_destroy();
                    $cookies_a_limpiar = ['recordarme_token', 'ultima_visita_timestamp', 'estilo_css'];
                    foreach ($cookies_a_limpiar as $cookie_name) {
                        if (isset($_COOKIE[$cookie_name])) {
                            setcookie($cookie_name, '', time() - 3600, '/');
                        }
                    }
                    
                    // Limpiar tokens
                    if (file_exists('tokens.txt')) {
                        $tokens = file('tokens.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                        $nuevos_tokens = [];
                        foreach ($tokens as $linea) {
                            list($token_usuario_id, $token_guardado, $expiracion_guardada) = explode(':', $linea);
                            if ($token_usuario_id != $usuario_id) {
                                $nuevos_tokens[] = $linea;
                            }
                        }
                        file_put_contents('tokens.txt', implode("\n", $nuevos_tokens) . "\n");
                    }
                    
                    // Redirigir a página de baja completada
                    header("Location: baja_completada.php");
                    exit();
                    
                } catch (Exception $e) {
                    $db->rollback();
                    $errores[] = "Error al eliminar la cuenta: " . $e->getMessage();
                }
            }
        }
        $stmt_verificar->close();
        $db->close();
    }
}

// Si no se encontró el usuario
if ($usuario === null) {
    echo "Error: No se pudieron cargar los datos del usuario.";
    exit;
}

$zona = 'privada';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Darme de baja - VENTAPLUS">
    <meta name="keywords" content="baja, eliminar cuenta, usuario, VENTAPLUS">
    <meta name="author" content="Santino Campessi Lojo">
    <meta name="author" content="Mario Laguna Contreras">
    <title>Darme de baja - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/darme_de_baja.css">
</head>

<body>
    <?php require('cabecera.php'); ?>

    <main>
        <h2>🗑️ Darme de baja</h2>

        <?php if (!empty($errores)): ?>
            <div class="mensaje-error">
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Resumen de datos según PDF -->
        <section class="resumen-datos">
            <h3>Resumen de sus datos</h3>
            <div class="advertencia">
                <strong>⚠️ Atención:</strong> Esta acción es irreversible. Se eliminarán todos sus datos.
            </div>

            <div class="estadisticas">
                <div class="estadistica">
                    <span class="numero"><?php echo $total_anuncios; ?></span>
                    <span class="texto">Anuncios publicados</span>
                </div>
                <div class="estadistica">
                    <span class="numero"><?php echo $total_fotos; ?></span>
                    <span class="texto">Fotos subidas</span>
                </div>
            </div>

            <?php if (!empty($anuncios_usuario)): ?>
            <div class="lista-anuncios">
                <h4>Sus anuncios:</h4>
                <?php foreach ($anuncios_usuario as $anuncio): ?>
                    <div class="anuncio-item">
                        <strong><?php echo htmlspecialchars($anuncio['Titulo']); ?></strong>
                        - <?php echo $anuncio['num_fotos']; ?> foto(s)
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>

        <!-- Formulario de confirmación según PDF -->
        <section class="confirmacion-baja">
            <h3>Confirmar baja</h3>
            <form action="darme_de_baja.php" method="POST">
                <label for="password_actual">Contraseña actual:</label>
                <input type="password" id="password_actual" name="password_actual" 
                       placeholder="Introduzca su contraseña para confirmar" required>
                
                <div class="acciones">
                    <button type="submit" name="confirmar_baja" value="1" class="btn-eliminar">
                        Confirmar baja definitiva
                    </button>
                    <a href="menu_usuario_registrado.php" class="btn-cancelar">Cancelar</a>
                </div>
            </form>
        </section>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>