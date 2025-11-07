<?php
session_start();
$zona = 'privada';
require('cabecera.php');

// LUEGO: verificar_sesion.php (después de session_start)
require_once 'verificar_sesion.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="VENTAPLUS: portal de anuncios de venta y alquiler de viviendas. Menú de usuario registrado.">
    <meta name="keywords" content="usuario, perfil, anuncios, mensajes, VENTAPLUS">
    <meta name="author" content="Santino Campessi Lojo">
    <meta name="author" content="Mario Laguna Contreras">
    <title>Menú Usuario Registrado - VENTAPLUS</title>
    
    <?php
    // Cargará general.css y luego contraste_alto.css u otro (si aplica)
    require('estilos.php'); 
    ?>
    
    <link rel="stylesheet" href="css/menu_usuario_registrado.css">
    
    <link rel="stylesheet" type="text/css" href="css/print_menu_usuario_registrado.css" media="print">
    
</head>

<body>
    <main>
        <article>
            <h2>Menú de usuario registrado</h2>
            <section>
                <ul>
                    <li><a id="opcion" href="404.php">Modificar mis datos</a></li>
                    <li><a id="opcion" href="404.php">Darme de baja</a></li>
                    <li><a id="opcion" href="misanuncios.php">Visualizar mis anuncios</a></li>
                    <li><a id="opcion" href="crear_anuncio.php">Crear un anuncio nuevo</a></li>
                    <li><a id="opcion" href="mensajes.php">Mis mensajes</a></li>
                    <li><a id="opcion" href="solicitar_folleto.php">Solicitar folleto publicitario impreso</a></li>
                    <li><a id="opcion" href="salir.php">Salir</a></li>
                </ul>
            </section>
        </article>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>