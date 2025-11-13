<?php
session_start();

// Conexión a BD para cargar países
require_once __DIR__ . '/db.php';
$db = conectarDB();

$paises = [];
$estilos = [];

if ($db) {
    // Cargar países desde la tabla PAISES
    $sql_paises = "SELECT IdPais, NomPais FROM PAISES ORDER BY NomPais ASC";
    $resultado_paises = $db->query($sql_paises);
    if ($resultado_paises) {
        $paises = $resultado_paises->fetch_all(MYSQLI_ASSOC);
        $resultado_paises->close();
    }
    
    // Cargar estilos desde la tabla ESTILOS (para posible selección o valor por defecto)
    $sql_estilos = "SELECT IdEstilo, Nombre FROM ESTILOS ORDER BY IdEstilo ASC";
    $resultado_estilos = $db->query($sql_estilos);
    if ($resultado_estilos) {
        $estilos = $resultado_estilos->fetch_all(MYSQLI_ASSOC);
        $resultado_estilos->close();
    }
    
    $db->close();
}

// Valor por defecto para el estilo (primer estilo de la tabla)
$estilo_por_defecto = !empty($estilos) ? $estilos[0]['IdEstilo'] : 1;

$zona = 'publica';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="VENTAPLUS: portal de anuncios de venta y alquiler de viviendas.">
    <meta name="keywords" content="viviendas, pisos, casas, alquiler, compra, venta, inmuebles">
    <meta name="author" content="Santino Campessi Lojo">
    <meta name="author" content="Mario Laguna Contreras">
    <title>Registro - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/registro.css">
    <link rel="stylesheet" type="text/css" href="css/print_formulario.css" media="print">
</head>

<body>
    
    <?php require('cabecera.php'); ?>

    <main>
        <?php
        // Flashdata para errores
        $redireccionar = false;
        
        if (isset($_SESSION['error_registro'])) {
            $redireccionar = true;
            echo "<p class='mensaje-error'>" . $_SESSION['error_registro'] . "</p>";
            unset($_SESSION['error_registro']);
        }

        if (isset($_SESSION['motivo_registro'])) {
            $redireccionar = true;
            echo "<p class='mensaje-error'>" . $_SESSION['motivo_registro'] . "</p>";
            unset($_SESSION['motivo_registro']);
        }

        if ($redireccionar) {
            $host = $_SERVER['HTTP_HOST'];
            $uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            $registro_page = 'registro.php';
            $url_limpia = "http://$host$uri/$registro_page"; 

            echo '<meta http-equiv="refresh" content="7;url=' . $url_limpia . '">';
        }
        ?>

        <form id="registro-form" action="res_registro.php" method="POST" enctype="multipart/form-data">
            <label for="usuario">Nombre de usuario:</label>
            <input type="text" id="usuario" name="usuario" placeholder="Nombre de usuario">

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" placeholder="Contraseña">

            <label for="confirm_password">Confirmar contraseña:</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Repetir contraseña">

            <label for="email">Correo electrónico:</label>
            <input type="text" id="email" name="email" placeholder="tu@email.com">

            <label for="sexo">Sexo:</label>
            <select id="sexo" name="sexo">
                <option value="">Selecciona tu sexo</option>
                <option value="0">Masculino</option>
                <option value="1">Femenino</option>
                <option value="2">Otro</option>
            </select>

            <label for="fecha_nacimiento">Fecha de nacimiento:</label>
            <input type="text" id="fecha_nacimiento" name="fecha_nacimiento" placeholder="dd/mm/yyyy">

            <label for="ciudad">Ciudad:</label>
            <input type="text" id="ciudad" name="ciudad">

            <label for="pais">País:</label>
            <select id="pais" name="pais">
                <option value="">-- Seleccione un país --</option>
                <?php if (empty($paises)): ?>
                    <option value="" disabled>Error al cargar países</option>
                <?php else: ?>
                    <?php foreach ($paises as $pais_item): ?>
                        <option value="<?php echo $pais_item['IdPais']; ?>">
                            <?php echo htmlspecialchars($pais_item['NomPais']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <label for="foto">Foto de perfil:</label>
            <input type="file" id="foto" name="foto" accept="image/*">

            <input type="hidden" name="estilo" value="<?php echo $estilo_por_defecto; ?>">

            <button type="submit">Registrarse</button>
        </form>
        <p>¿Ya tienes cuenta? <a href="./index.php">Iniciar Sesión</a></p>
    </main>

    <?php require('pie.php'); ?>

    <dialog class="modal" id="error-dialog">
        <p id="error-mensaje"></p>
        <button class="cerrar" id="cerrar-error">Cerrar</button>
    </dialog>

</body>

</html>