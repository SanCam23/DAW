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
    <title>Buscar - VENTAPLUS</title>
    <link rel="stylesheet" href="css/general.css" title="Estilo normal">
    <link rel="stylesheet" href="css/formulario.css">
    <link rel="alternate stylesheet" href="css/contraste_alto.css" title="Alto contraste">
    <link rel="alternate stylesheet" href="css/letra_grande.css" title="Letra Grande">
    <link rel="alternate stylesheet" href="css/contraste_letra.css" title="Letra Grande+Alto contraste">
    <link rel="stylesheet" type="text/css" href="css/print_formulario.css" media="print">
    <link rel="stylesheet" href="css/fontello.css">
    <link href="https://fonts.googleapis.com/css2?family=Leckerli+One&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php include('cabecera.php'); ?>


    <form action="resultados.php" method="get">
        <section>
            <h3>Tipo de anuncio</h3>
            <label for="venta">Venta</label>
            <input type="radio" id="venta" name="tipo_anuncio" value="venta">
            <label for="alquiler">Alquiler</label>
            <input type="radio" id="alquiler" name="tipo_anuncio" value="alquiler">
        </section>

        <section>
            <h3>Tipo de vivienda</h3>
            <label for="obra_nueva">Obra nueva</label>
            <input type="radio" id="obra_nueva" name="tipo_vivienda" value="obra_nueva">
            <label for="vivienda">Vivienda</label>
            <input type="radio" id="vivienda" name="tipo_vivienda" value="vivienda">
            <label for="oficina">Oficina</label>
            <input type="radio" id="oficina" name="tipo_vivienda" value="oficina">
            <label for="local">Local</label>
            <input type="radio" id="local" name="tipo_vivienda" value="local">
            <label for="garaje">Garaje</label>
            <input type="radio" id="garaje" name="tipo_vivienda" value="garaje">
        </section>

        <section>
            <h3>Ubicación</h3>
            <label for="ciudad">Ciudad:</label>
            <input type="text" id="ciudad" name="ciudad">
            <label for="pais">País:</label>
            <input type="text" id="pais" name="pais">
        </section>

        <section>
            <h3>Precio (€)</h3>
            <label for="precio_min">Mínimo:</label>
            <input type="number" id="precio_min" name="precio_min" min="0" step="1000">
            <label for="precio_max">Máximo:</label>
            <input type="number" id="precio_max" name="precio_max" min="0" step="1000">
        </section>

        <section>
            <h3>Fecha de publicación</h3>
            <label for="fecha">Desde:</label>
            <input type="text" id="fecha" name="fecha">
        </section>

        <button type="reset">Limpiar</button>
        <button type="submit">Buscar</button>
    </form>

    <?php include('pie.php'); ?>

    <dialog class="modal" id="error-dialog">
        <p id="error-mensaje"></p>
        <button class="cerrar" id="cerrar-error">Cerrar</button>
    </dialog>

    <!-- <script src="js/formulario.js"></script> -->
</body>

</html>