<?php
session_start();
require_once 'verificar_sesion.php';
require_once __DIR__ . '/db.php';

// Obtener información del nuevo estilo
$nuevo_estilo_id = $_GET['estilo'] ?? '';
$nuevo_estilo_nombre = 'Normal';
$nuevo_estilo_descripcion = '';

if (!empty($nuevo_estilo_id)) {
    $db = conectarDB();
    $sql_estilo = "SELECT Nombre, Descripcion FROM ESTILOS WHERE IdEstilo = ?";
    $stmt = $db->prepare($sql_estilo);
    $stmt->bind_param("i", $nuevo_estilo_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado && $resultado->num_rows > 0) {
        $estilo_info = $resultado->fetch_assoc();
        $nuevo_estilo_nombre = $estilo_info['Nombre'];
        $nuevo_estilo_descripcion = $estilo_info['Descripcion'];
    }
    $stmt->close();
    $db->close();
}

$zona = 'privada';
$clase_contraste = ($nuevo_estilo_nombre === 'Alto Contraste') ? 'inicio' : '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Estilo configurado - VENTAPLUS">
    <meta name="keywords" content="estilo, configurado, confirmación, VENTAPLUS">
    <meta name="author" content="Santino Campessi Lojo">
    <meta name="author" content="Mario Laguna Contreras">
    <title>Estilo Configurado - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/res_configurar.css">
</head>

<body>
    <?php require('cabecera.php'); ?>

    <main class="<?php echo $clase_contraste; ?>">
        <section class="confirmacion-cambio">
            <h2>✅ Estilo configurado correctamente</h2>
            <p>Tu estilo visual ha sido actualizado exitosamente.</p>
        </section>

        <section class="nuevo-estilo-info">
            <h3>Nuevo estilo aplicado:</h3>
            <div class="estilo-detalle">
                <div class="estilo-nombre">
                    <strong>Estilo:</strong> <?php echo htmlspecialchars($nuevo_estilo_nombre); ?>
                </div>
                <?php if (!empty($nuevo_estilo_descripcion)): ?>
                <div class="estilo-descripcion">
                    <strong>Descripción:</strong> <?php echo htmlspecialchars($nuevo_estilo_descripcion); ?>
                </div>
                <?php endif; ?>
                <div class="estilo-aplicacion">
                    <strong>Se aplica en:</strong> Todas las páginas del sitio web
                </div>
            </div>
        </section>

        <section class="demo-estilo">
            <h3>Vista previa del estilo</h3>
            <div class="elementos-demo">
                <div class="demo-item texto">Este es un texto de ejemplo con el nuevo estilo</div>
                <div class="demo-item boton">Botón de ejemplo</div>
                <div class="demo-item enlace"><a href="#">Enlace de ejemplo</a></div>
                <div class="demo-item caja">Caja de contenido de ejemplo</div>
            </div>
        </section>

        <section class="acciones">
            <a href="configurar.php" class="btn">Cambiar otro estilo</a>
            <a href="menu_usuario_registrado.php" class="btn volver">Volver al menú principal</a>
        </section>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>