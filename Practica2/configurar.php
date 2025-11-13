<?php
session_start();
require_once 'verificar_sesion.php';
require_once __DIR__ . '/db.php';

// Obtener estilos desde la BD
$db = conectarDB();
$estilos = [];
$estilo_actual = '';

if ($db && isset($_SESSION['usuario_id'])) {
    // Obtener todos los estilos disponibles
    $sql_estilos = "SELECT IdEstilo, Nombre, Descripcion, Fichero FROM ESTILOS ORDER BY IdEstilo ASC";
    $resultado_estilos = $db->query($sql_estilos);
    if ($resultado_estilos) {
        $estilos = $resultado_estilos->fetch_all(MYSQLI_ASSOC);
        $resultado_estilos->close();
    }
    
    // Obtener el estilo actual del usuario
    $sql_usuario = "SELECT e.Nombre FROM USUARIOS u 
                    LEFT JOIN ESTILOS e ON u.Estilo = e.IdEstilo 
                    WHERE u.IdUsuario = ?";
    $stmt = $db->prepare($sql_usuario);
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $resultado_usuario = $stmt->get_result();
    if ($resultado_usuario && $resultado_usuario->num_rows > 0) {
        $fila = $resultado_usuario->fetch_assoc();
        $estilo_actual = $fila['Nombre'];
    }
    $stmt->close();
    
    $db->close();
}

$zona = 'privada';
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

    <main>
        <h2>Configurar estilo visual</h2>
        
        <section class="estilo-actual">
            <h3>Estilo actual</h3>
            <p>Tu estilo actual es: <strong><?php echo $estilo_actual ?: 'Normal'; ?></strong></p>
        </section>

        <section class="lista-estilos">
            <h3>Estilos disponibles</h3>
            
            <?php if (empty($estilos)): ?>
                <p>No hay estilos disponibles en este momento.</p>
            <?php else: ?>
                <div class="estilos-grid">
                    <?php foreach ($estilos as $estilo): ?>
                        <article class="estilo-item <?php echo $estilo['Nombre'] == $estilo_actual ? 'estilo-activo' : ''; ?>">
                            <h4><?php echo $estilo['Nombre']; ?></h4>
                            <?php if (!empty($estilo['Descripcion'])): ?>
                                <p class="descripcion"><?php echo $estilo['Descripcion']; ?></p>
                            <?php endif; ?>
                            <p class="fichero"><small>Archivo: <?php echo $estilo['Fichero']; ?></small></p>
                            
                            <?php if ($estilo['Nombre'] == $estilo_actual): ?>
                                <div class="estado-activo">✓ Actualmente activo</div>
                            <?php else: ?>
                                <form action="#" method="POST" style="display: inline;">
                                    <input type="hidden" name="cambiar_estilo" value="<?php echo $estilo['IdEstilo']; ?>">
                                    <button type="submit" disabled class="btn-cambiar">Seleccionar este estilo</button>
                                </form>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        
        <p class="nota" style="text-align: center; color: #7f8c8d; font-style: italic; margin: 20px 0;">
            La selección de estilos estará disponible en la próxima práctica.
        </p>
        
        <p style="text-align: center; margin-top: 20px;">
            <a href="menu_usuario_registrado.php" class="volver">Volver al menú de usuario</a>
        </p>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>