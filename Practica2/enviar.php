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
    <title>Enviar mensaje - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/enviar.css">
    <link rel="stylesheet" type="text/css" href="css/print_enviar.css" media="print">
</head>

<body>

    <?php 
        $zona = 'publica';
        require('cabecera.php'); 
    ?>

    <main>
        <form action="confirmacionmensaje.php" method="post">
            <section>
                <h2>Tipo de mensaje</h2>
                <label>
                    <input type="radio" name="tipo" value="informacion">
                    Más información
                </label><br>
                <label>
                    <input type="radio" name="tipo" value="cita">
                    Solicitar una cita
                </label><br>
                <label>
                    <input type="radio" name="tipo" value="oferta">
                    Comunicar una oferta
                </label>
            </section>

            <section>
                <h2>Escribe tu mensaje</h2>
                <label for="mensaje">Mensaje:</label><br>
                <textarea id="mensaje" name="mensaje" rows="6" cols="50"></textarea>
            </section>

            <button type="submit">Enviar mensaje</button>
        </form>
    </main>

    <?php require('pie.php'); ?>

    <dialog class="modal" id="modalErrores">
        <h2>Errores en el formulario</h2>
        <ul id="listaErrores"></ul>
        <button class="cerrar" id="cerrarModal">Cerrar</button>
    </dialog>

    <!-- <script src="./js/enviar.js"></script> -->
</body>

</html>