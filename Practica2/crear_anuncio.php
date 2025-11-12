<?php
session_start();

// Conexión a BD para cargar datos en desplegables
require_once __DIR__ . '/db.php';
$db = conectarDB();

$paises = [];
$tipos_anuncio = [];
$tipos_vivienda = [];

if ($db) {
    // Cargar países desde la tabla PAISES
    $sql_paises = "SELECT IdPais, NomPais FROM PAISES ORDER BY NomPais ASC";
    $resultado_paises = $db->query($sql_paises);
    if ($resultado_paises) {
        $paises = $resultado_paises->fetch_all(MYSQLI_ASSOC);
        $resultado_paises->close();
    }
    
    // Cargar tipos de anuncio desde TIPOSANUNCIOS
    $sql_anuncios = "SELECT IdTAnuncio, NomTAnuncio FROM TIPOSANUNCIOS ORDER BY NomTAnuncio ASC";
    $resultado_anuncios = $db->query($sql_anuncios);
    if ($resultado_anuncios) {
        $tipos_anuncio = $resultado_anuncios->fetch_all(MYSQLI_ASSOC);
        $resultado_anuncios->close();
    }
    
    // Cargar tipos de vivienda desde TIPOSVIVIENDAS
    $sql_viviendas = "SELECT IdTVivienda, NomTVivienda FROM TIPOSVIVIENDAS ORDER BY NomTVivienda ASC";
    $resultado_viviendas = $db->query($sql_viviendas);
    if ($resultado_viviendas) {
        $tipos_vivienda = $resultado_viviendas->fetch_all(MYSQLI_ASSOC);
        $resultado_viviendas->close();
    }
    
    $db->close();
}

$zona = 'privada';
require_once 'verificar_sesion.php';
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
    <title>Crear Anuncio - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/crear_anuncio.css">
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print">
</head>

<body>
    <?php
        require('cabecera.php');
    ?>

    <main>
        <h2>Crear un nuevo anuncio</h2>

        <?php
        $titulo = $_POST["titulo"] ?? "";
        $descripcion = $_POST["descripcion"] ?? "";
        $precio = $_POST["precio"] ?? "";
        $ciudad = $_POST["ciudad"] ?? "";
        $pais = $_POST["pais"] ?? "";
        $tipo_anuncio = $_POST["tipo_anuncio"] ?? "";
        $tipo_vivienda = $_POST["tipo_vivienda"] ?? "";
        $errores = [];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (trim($titulo) == "") $errores[] = "El título es obligatorio.";
            if (trim($descripcion) == "") $errores[] = "La descripción es obligatoria.";
            if (trim($precio) == "" || !is_numeric($precio) || $precio <= 0) $errores[] = "El precio debe ser un número positivo.";
            if (trim($ciudad) == "") $errores[] = "La ciudad es obligatoria.";
            if (trim($pais) == "") $errores[] = "El país es obligatorio.";
            if (trim($tipo_anuncio) == "") $errores[] = "Debe seleccionar el tipo de anuncio.";
            if (trim($tipo_vivienda) == "") $errores[] = "Debe seleccionar el tipo de vivienda.";

            if (empty($errores)) {
                echo "<section class='confirmacion'>";
                echo "<h3>Anuncio creado correctamente</h3>";
                echo "<p>Los datos introducidos son los siguientes:</p>";
                echo "<ul>";
                echo "<li><strong>Título:</strong> $titulo</li>";
                echo "<li><strong>Descripción:</strong> $descripcion</li>";
                echo "<li><strong>Precio:</strong> $precio €</li>";
                echo "<li><strong>Ciudad:</strong> $ciudad</li>";
                echo "<li><strong>País:</strong> $pais</li>";
                echo "<li><strong>Tipo de anuncio:</strong> $tipo_anuncio</li>";
                echo "<li><strong>Tipo de vivienda:</strong> $tipo_vivienda</li>";
                echo "</ul>";
                echo "</section>";
            } else {
                echo "<section class='errores'>";
                echo "<h3>Se han encontrado errores:</h3>";
                echo "<ul>";
                foreach ($errores as $e) {
                    echo "<li>$e</li>";
                }
                echo "</ul>";
                echo "</section>";
            }
        }
        ?>

        <form action="crear_anuncio.php" method="post">
            <fieldset>
                <legend>Datos del anuncio</legend>

                <label for="titulo">Título*:</label><br>
                <input type="text" id="titulo" name="titulo" value="<?php echo $titulo; ?>"><br><br>

                <label for="descripcion">Descripción*:</label><br>
                <textarea id="descripcion" name="descripcion" rows="5" cols="40"><?php echo $descripcion; ?></textarea><br><br>

                <label for="precio">Precio (€)*:</label><br>
                <input type="number" id="precio" name="precio" min="0" value="<?php echo $precio; ?>"><br><br>

                <label for="ciudad">Ciudad*:</label><br>
                <input type="text" id="ciudad" name="ciudad" value="<?php echo $ciudad; ?>"><br><br>

                <label for="pais">País*:</label><br>
                <select id="pais" name="pais">
                    <option value="">Seleccione...</option>
                    <?php if (empty($paises)): ?>
                        <option value="" disabled>Error al cargar países</option>
                    <?php else: ?>
                        <?php foreach ($paises as $pais_item): ?>
                            <option value="<?php echo $pais_item['IdPais']; ?>" <?php if ($pais == $pais_item['IdPais']) echo "selected"; ?>>
                                <?php echo $pais_item['NomPais']; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select><br><br>

                <label for="tipo_anuncio">Tipo de anuncio*:</label><br>
                <select id="tipo_anuncio" name="tipo_anuncio">
                    <option value="">Seleccione...</option>
                    <?php if (empty($tipos_anuncio)): ?>
                        <option value="" disabled>Error al cargar tipos</option>
                    <?php else: ?>
                        <?php foreach ($tipos_anuncio as $tipo): ?>
                            <option value="<?php echo $tipo['IdTAnuncio']; ?>" <?php if ($tipo_anuncio == $tipo['IdTAnuncio']) echo "selected"; ?>>
                                <?php echo $tipo['NomTAnuncio']; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select><br><br>

                <label for="tipo_vivienda">Tipo de vivienda*:</label><br>
                <select id="tipo_vivienda" name="tipo_vivienda">
                    <option value="">Seleccione...</option>
                    <?php if (empty($tipos_vivienda)): ?>
                        <option value="" disabled>Error al cargar viviendas</option>
                    <?php else: ?>
                        <?php foreach ($tipos_vivienda as $vivienda): ?>
                            <option value="<?php echo $vivienda['IdTVivienda']; ?>" <?php if ($tipo_vivienda == $vivienda['IdTVivienda']) echo "selected"; ?>>
                                <?php echo $vivienda['NomTVivienda']; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select><br><br>

                <button type="submit">Crear anuncio</button>
                <a href="crear_anuncio.php">Limpiar</a>
            </fieldset>
        </form>
    </main>

    <?php require('pie.php'); ?>
    
</body>

</html>