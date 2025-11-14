<?php

require_once __DIR__ . '/db.php';

$db = conectarDB();

// Preparamos arrays para guardar los datos
$paises = [];
$tipos_anuncio = [];
$tipos_vivienda = [];

if ($db) {

    /* 1. Consultar Países */
    $sql_paises = "SELECT IdPais, NomPais FROM PAISES ORDER BY NomPais ASC";
    $res_paises = $db->query($sql_paises);
    if ($res_paises) {
        $paises = $res_paises->fetch_all(MYSQLI_ASSOC);
        $res_paises->close();
    }

    /* 2. Consultar Tipos de Anuncio */
    $sql_anuncios = "SELECT IdTAnuncio, NomTAnuncio FROM TIPOSANUNCIOS ORDER BY NomTAnuncio ASC";
    $res_anuncios = $db->query($sql_anuncios);
    if ($res_anuncios) {
        $tipos_anuncio = $res_anuncios->fetch_all(MYSQLI_ASSOC);
        $res_anuncios->close();
    }

    /* 3. Consultar Tipos de Vivienda */
    $sql_viviendas = "SELECT IdTVivienda, NomTVivienda FROM TIPOSVIVIENDAS ORDER BY NomTVivienda ASC";
    $res_viviendas = $db->query($sql_viviendas);
    if ($res_viviendas) {
        $tipos_vivienda = $res_viviendas->fetch_all(MYSQLI_ASSOC);
        $res_viviendas->close();
    }

    // Cerramos la conexión, ya tenemos los datos
    $db->close();
}

/* Lógica de "sticky form" para que los valores de búsqueda se queden seleccionados */
$tipo_anuncio  = $_GET["tipo_anuncio"] ?? "";
$tipo_vivienda = $_GET["tipo_vivienda"] ?? "";
$ciudad        = $_GET["ciudad"] ?? "";
$pais          = $_GET["pais"] ?? "";
$precio_min    = $_GET["precio_min"] ?? "";
$precio_max    = $_GET["precio_max"] ?? "";
$fecha         = $_GET["fecha"] ?? "";

?>
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
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/formulario.css">
    <link rel="stylesheet" type="text/css" href="css/print_formulario.css" media="print">
</head>

<body>
    <?php
    $zona = 'publica';
    require('cabecera.php');
    ?>

    <main>

        <form action="resultados.php" method="get">

            <section>
                <h3>Tipo de anuncio</h3>
                <label for="tipo_anuncio">Tipo:</label>
                <select id="tipo_anuncio" name="tipo_anuncio">
                    <option value="">-- Seleccione tipo --</option>
                    <?php if (empty($tipos_anuncio)): ?>
                        <option value="" disabled>Error al cargar tipos</option>
                    <?php else: ?>
                        <?php foreach ($tipos_anuncio as $tipo): ?>
                            <option value="<?php echo $tipo['IdTAnuncio']; ?>" <?php if ($tipo_anuncio == $tipo['IdTAnuncio']) echo "selected"; ?>>
                                <?php echo htmlspecialchars($tipo['NomTAnuncio']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </section>

            <section>
                <h3>Tipo de vivienda</h3>
                <label for="tipo_vivienda">Vivienda:</label>
                <select id="tipo_vivienda" name="tipo_vivienda">
                    <option value="">-- Seleccione vivienda --</option>
                    <?php if (empty($tipos_vivienda)): ?>
                        <option value="" disabled>Error al cargar viviendas</option>
                    <?php else: ?>
                        <?php foreach ($tipos_vivienda as $vivienda): ?>
                            <option value="<?php echo $vivienda['IdTVivienda']; ?>" <?php if ($tipo_vivienda == $vivienda['IdTVivienda']) echo "selected"; ?>>
                                <?php echo htmlspecialchars($vivienda['NomTVivienda']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </section>

            <section>
                <h3>Ubicación</h3>
                <label for="ciudad">Ciudad:</label>
                <input type="text" id="ciudad" name="ciudad" value="<?php echo htmlspecialchars($ciudad); ?>">

                <label for="pais">País:</label>
                <select id="pais" name="pais">
                    <option value="">-- Todos los países --</option>
                    <?php if (empty($paises)): ?>
                        <option value="" disabled>Error al cargar países</option>
                    <?php else: ?>
                        <?php foreach ($paises as $pais_item): ?>
                            <option value="<?php echo $pais_item['IdPais']; ?>" <?php if ($pais == $pais_item['IdPais']) echo "selected"; ?>>
                                <?php echo htmlspecialchars($pais_item['NomPais']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </section>

            <section>
                <h3>Precio (€)</h3>
                <label for="precio_min">Mínimo:</label>
                <input type="number" id="precio_min" name="precio_min" min="0" step="1000" value="<?php echo htmlspecialchars($precio_min); ?>">
                <label for="precio_max">Máximo:</label>
                <input type="number" id="precio_max" name="precio_max" min="0" step="1000" value="<?php echo htmlspecialchars($precio_max); ?>">
            </section>

            <section>
                <h3>Fecha de publicación</h3>
                <label for="fecha">Desde (dd/mm/yyyy):</label>
                <input type="text" id="fecha" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>">
            </section>

            <a href="formulario.php">Limpiar</a>
            <button type="submit">Buscar</button>
        </form>
        <?php require_once 'panel_visitados.php'; ?>

    </main>

    <?php require('pie.php'); ?>
</body>

</html>