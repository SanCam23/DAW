<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="VENTAPLUS: portal de anuncios de venta y alquiler de viviendas. Busca tu próximo hogar fácilmente.">
    <meta name="keywords" content="viviendas, pisos, casas, alquiler, compra, venta, inmuebles">
    <meta name="author" content="Santino Campessi Lojo">
    <meta name="author" content="Mario Laguna Contreras">
    <title>Registro - VENTAPLUS</title>
    <link rel="stylesheet" href="css/general.css" title="Estilo normal">
    <link rel="stylesheet" href="css/registro.css">
    <link rel="alternate stylesheet" href="css/contraste_alto.css" title="Alto contraste">
    <link rel="alternate stylesheet" href="css/letra_grande.css" title="Letra Grande">
    <link rel="alternate stylesheet" href="css/contraste_letra.css" title="Letra Grande+Alto contraste">
    <link rel="stylesheet" type="text/css" href="css/print_formulario.css" media="print">
    <link rel="stylesheet" href="css/fontello.css">
    <link href="https://fonts.googleapis.com/css2?family=Leckerli+One&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
</head>

<body>
    
    <?php
    session_start();
    $zona = 'publica';
    require('cabecera.php'); 
    ?>

    <main>
        <?php
        // Flashdata para errores en lugar de parámetros URL
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

        <form id="registro-form" action="res_registro.php" method="POST">
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
                <option value="masculino">Masculino</option>
                <option value="femenino">Femenino</option>
                <option value="otro">Otro</option>
            </select>

            <label for="fecha_nacimiento">Fecha de nacimiento:</label>
            <input type="text" id="fecha_nacimiento" name="fecha_nacimiento" placeholder="dd/mm/yyyy">

            <label for="ciudad">Ciudad:</label>
            <input type="text" id="ciudad" name="ciudad">

            <label for="pais">País:</label>
            <input type="text" id="pais" name="pais">

            <label for="foto">Foto de perfil:</label>
            <input type="file" id="foto" name="foto" accept="image/*">

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