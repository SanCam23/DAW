<?php
session_start();
require_once 'verificar_sesion.php';
require_once __DIR__ . '/db.php';

// Inicializar variables
$db = conectarDB();
$estilos = [];
$estilo_actual = '';
$estilo_actual_id = null;
$mensaje_exito = '';
$errores = [];

// Procesar cambio de estilo si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_estilo'])) {
    $nuevo_estilo_id = $_POST['cambiar_estilo'];
    
    // Validar que el estilo existe
    $db = conectarDB();
    $sql_verificar = "SELECT IdEstilo, Fichero FROM ESTILOS WHERE IdEstilo = ?";
    $stmt_verificar = $db->prepare($sql_verificar);
    $stmt_verificar->bind_param("i", $nuevo_estilo_id);
    $stmt_verificar->execute();
    $resultado_verificar = $stmt_verificar->get_result();
    
    if ($resultado_verificar && $resultado_verificar->num_rows > 0) {
        $estilo_valido = $resultado_verificar->fetch_assoc();
        
        // Actualizar estilo en la BD
        $sql_update = "UPDATE USUARIOS SET Estilo = ? WHERE IdUsuario = ?";
        $stmt_update = $db->prepare($sql_update);
        $stmt_update->bind_param("ii", $nuevo_estilo_id, $_SESSION['usuario_id']);
        
        if ($stmt_update->execute()) {
            // Actualizar sesión con el nuevo estilo
            $nuevo_fichero_estilo = pathinfo($estilo_valido['Fichero'], PATHINFO_FILENAME);
            $_SESSION['estilo_css'] = $nuevo_fichero_estilo;
            
            // Actualizar cookie si existe
            if (isset($_COOKIE['estilo_css'])) {
                setcookie('estilo_css', $nuevo_fichero_estilo, [
                    'expires' => time() + (90 * 24 * 60 * 60),
                    'path' => '/',
                    'httponly' => false,
                    'samesite' => 'Lax'
                ]);
            }
            
            // Redirigir a página de respuesta
            header("Location: res_configurar.php?estilo=" . urlencode($nuevo_estilo_id));
            exit();
            
        } else {
            $errores[] = "Error al actualizar el estilo en la base de datos.";
        }
        $stmt_update->close();
    } else {
        $errores[] = "El estilo seleccionado no es válido.";
    }
    
    $stmt_verificar->close();
    $db->close();
}

// Obtener estilos desde la BD
$db = conectarDB();
if ($db && isset($_SESSION['usuario_id'])) {
    // Obtener todos los estilos disponibles
    $sql_estilos = "SELECT IdEstilo, Nombre, Descripcion, Fichero FROM ESTILOS ORDER BY IdEstilo ASC";
    $resultado_estilos = $db->query($sql_estilos);
    if ($resultado_estilos) {
        $estilos = $resultado_estilos->fetch_all(MYSQLI_ASSOC);
        $resultado_estilos->close();
    }
    
    // Obtener el estilo actual del usuario
    $sql_usuario = "SELECT u.Estilo, e.Nombre, e.Fichero 
                    FROM USUARIOS u 
                    LEFT JOIN ESTILOS e ON u.Estilo = e.IdEstilo 
                    WHERE u.IdUsuario = ?";
    $stmt = $db->prepare($sql_usuario);
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $resultado_usuario = $stmt->get_result();
    if ($resultado_usuario && $resultado_usuario->num_rows > 0) {
        $fila = $resultado_usuario->fetch_assoc();
        $estilo_actual = $fila['Nombre'];
        $estilo_actual_id = $fila['Estilo'];
    }
    $stmt->close();
    
    $db->close();
}

$zona = 'privada';
$clase_contraste = ($estilo_actual === 'Alto Contraste') ? 'inicio' : '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Configurar estilo visual en VENTAPLUS.">
    <meta name="keywords" content="estilos, configuración, preferencias, VENTAPLUS">
    <meta name="author" content="Santino Campessi Lojo">
    <meta name="author" content="Mario Laguna Contreras">
    <title>Configurar - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/configurar.css">
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print">
</head>

<body>
    <?php require('cabecera.php'); ?>

    <main class="<?php echo $clase_contraste; ?>">
        <h2>Configurar estilo visual</h2>
        
        <?php if (!empty($errores)): ?>
            <div class="mensaje-error">
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <section class="estilo-actual">
            <h3>Estilo actual</h3>
            <p>Tu estilo actual es: <strong><?php echo $estilo_actual ?: 'Normal'; ?></strong></p>
        </section>

        <section class="lista-estilos">
            <h3>Selecciona un nuevo estilo</h3>
            <p class="nota">El nuevo estilo se aplicará inmediatamente a todas las páginas.</p>
            
            <?php if (empty($estilos)): ?>
                <p>No hay estilos disponibles en este momento.</p>
            <?php else: ?>
                <div class="estilos-grid">
                    <?php foreach ($estilos as $estilo): ?>
                        <article class="estilo-item anuncio <?php echo $estilo['IdEstilo'] == $estilo_actual_id ? 'estilo-activo' : ''; ?>">
                            <h4><?php echo $estilo['Nombre']; ?></h4>
                            <?php if (!empty($estilo['Descripcion'])): ?>
                                <p class="descripcion"><?php echo $estilo['Descripcion']; ?></p>
                            <?php endif; ?>
                            <p class="fichero">Archivo: <?php echo $estilo['Fichero']; ?></p>
                            
                            <?php if ($estilo['IdEstilo'] == $estilo_actual_id): ?>
                                <div class="estado-activo"> Actualmente activo</div>
                            <?php else: ?>
                                <form action="configurar.php" method="POST">
                                    <input type="hidden" name="cambiar_estilo" value="<?php echo $estilo['IdEstilo']; ?>">
                                    <button type="submit" class="btn-cambiar">Seleccionar este estilo</button>
                                </form>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        
        <p style="text-align: center; margin-top: 20px;">
            <a href="menu_usuario_registrado.php" class="volver">Volver al menú de usuario</a>
        </p>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>