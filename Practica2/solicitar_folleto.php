<?php
$tarifas = array(
    "envio" => 10,
    "paginas" => array("p1a4" => 2.0, "p5a10" => 1.8, "p11ymas" => 1.6),
    "color" => array("bn" => 0, "color" => 0.5),
    "resol" => array("baja" => 0, "alta" => 0.2)
);

function calcularCoste($pags, $fotos, $color, $resol, $t)
{
    $costePaginas = 0;

    if ($pags <= 4) {
        $costePaginas = $pags * $t["paginas"]["p1a4"];
    } elseif ($pags <= 10) {
        $costePaginas += 4 * $t["paginas"]["p1a4"];
        $costePaginas += ($pags - 4) * $t["paginas"]["p5a10"];
    } else {
        $costePaginas += 4 * $t["paginas"]["p1a4"];
        $costePaginas += 6 * $t["paginas"]["p5a10"];
        $costePaginas += ($pags - 10) * $t["paginas"]["p11ymas"];
    }

    $costeColor = ($color == "color") ? $fotos * $t["color"]["color"] : 0;
    $costeResol = ($resol == "alta") ? $fotos * $t["resol"]["alta"] : 0;

    return $t["envio"] + $costePaginas + $costeColor + $costeResol;
}

$paginas = range(1, 15);
$fotos = array();
for ($i = 0; $i < 15; $i++) {
    $fotos[] = ($i + 1) * 3;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="VENTAPLUS: portal de anuncios de venta y alquiler de viviendas. Busca tu próximo hogar fácilmente.">
    <meta name="keywords" content="viviendas, pisos, casas, alquiler, compra, venta, inmuebles">
    <meta name="author" content="Santino Campessi Lojo">
    <meta name="author" content="Mario Laguna Contreras">
    <title>Solicitar Folleto - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/solicitar_folleto.css">
    <link rel="stylesheet" type="text/css" href="css/print_solicitar_folleto.css" media="print">
</head>

<body>

    <?php
        $zona = 'privada'; 
        require('cabecera.php');
        require_once 'verificar_sesion.php'; 
    ?>

    <main>
        <h2>Solicitar folleto publicitario</h2>
        <p>
            Rellena el siguiente formulario para solicitar un folleto publicitario impreso
            de uno de tus anuncios. Los campos marcados con * son obligatorios.
        </p>

        <section>
            <h3>Tarifas</h3>
            <table border="1">
                <caption>Tarifas de impresión de folletos</caption>
                <thead>
                    <tr>
                        <th scope="col">Concepto</th>
                        <th scope="col">Condición</th>
                        <th scope="col">Precio</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Procesamiento y envío</td>
                        <td>Fijo</td>
                        <td>10 €</td>
                    </tr>
                    <tr>
                        <td rowspan="3">Número de páginas</td>
                        <td>De 1 a 4 páginas</td>
                        <td>2 € / página</td>
                    </tr>
                    <tr>
                        <td>De 5 a 10 páginas</td>
                        <td>1.8 € / página</td>
                    </tr>
                    <tr>
                        <td>Más de 10 páginas</td>
                        <td>1.6 € / página</td>
                    </tr>
                    <tr>
                        <td rowspan="2">Color / B&N</td>
                        <td>Blanco y Negro</td>
                        <td>0 €</td>
                    </tr>
                    <tr>
                        <td>Color</td>
                        <td>0.5 € / foto</td>
                    </tr>
                    <tr>
                        <td rowspan="2">Resolución</td>
                        <td>150-300 dpi</td>
                        <td>0 € / foto</td>
                    </tr>
                    <tr>
                        <td>450-900 dpi</td>
                        <td>0.2 € / foto</td>
                    </tr>
                </tbody>
            </table>
        </section>


        <section id="tabla-costes-php">
            <h3>Tabla de costes (generada con PHP)</h3>
            <table border="1">
                <thead>
                    <tr>
                        <th rowspan="2">Número de páginas</th>
                        <th rowspan="2">Número de fotos</th>
                        <th colspan="2">Blanco y negro</th>
                        <th colspan="2">Color</th>
                    </tr>
                    <tr>
                        <th>150-300 dpi</th>
                        <th>450-900 dpi</th>
                        <th>150-300 dpi</th>
                        <th>450-900 dpi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($paginas as $i => $p) {
                        echo "<tr>";
                        echo "<td>$p</td><td>{$fotos[$i]}</td>";

                        $coste = calcularCoste($p, $fotos[$i], "bn", "baja", $tarifas);
                        echo "<td>" . number_format($coste, 2, ',', '.') . " €</td>";

                        $coste = calcularCoste($p, $fotos[$i], "bn", "alta", $tarifas);
                        echo "<td>" . number_format($coste, 2, ',', '.') . " €</td>";

                        $coste = calcularCoste($p, $fotos[$i], "color", "baja", $tarifas);
                        echo "<td>" . number_format($coste, 2, ',', '.') . " €</td>";

                        $coste = calcularCoste($p, $fotos[$i], "color", "alta", $tarifas);
                        echo "<td>" . number_format($coste, 2, ',', '.') . " €</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <section>
            <form id="formularioFolleto" method="post" action="respuesta_solicitar_folleto.php">
                <fieldset>
                    <legend>Texto adicional</legend>
                    <label for="texto">Texto (máx. 4000 caracteres):</label><br>
                    <textarea id="texto" name="texto" maxlength="4000" rows="6" cols="50"></textarea>
                </fieldset>

                <fieldset>
                    <legend>Datos personales</legend>
                    <label for="nombre">Nombre completo*:</label>
                    <input type="text" id="nombre" name="nombre" maxlength="200"><br><br>

                    <label for="email">Correo electrónico*:</label>
                    <input type="text" id="email" name="email" maxlength="200"><br><br>

                    <label for="telefono">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono"><br><br>
                </fieldset>

                <fieldset>
                    <legend>Dirección de envío</legend>
                    <label for="calle">Calle*:</label>
                    <input type="text" id="calle" name="calle"><br><br>

                    <label for="numero">Número*:</label>
                    <input type="text" id="numero" name="numero"><br><br>

                    <label for="cp">Código postal*:</label>
                    <input type="text" id="cp" name="cp"><br><br>

                    <label for="localidad">Localidad*:</label>
                    <input type="text" id="localidad" name="localidad"><br><br>

                    <label for="provincia">Provincia*:</label>
                    <input type="text" id="provincia" name="provincia"><br><br>

                    <label for="pais">País*:</label>
                    <select id="pais" name="pais">
                        <option value="">Seleccione</option>
                        <option value="espana">España</option>
                        <option value="francia">Francia</option>
                        <option value="italia">Italia</option>
                        <option value="alemania">Alemania</option>
                    </select>
                </fieldset>

                <fieldset>
                    <legend>Opciones del folleto</legend>
                    <label for="color">Color de la portada:</label>
                    <input type="color" id="color" name="color" value="#000000"><br><br>

                    <label for="copias">Número de copias (1-99):</label>
                    <input type="number" id="copias" name="copias" min="1" max="99" value="1"><br><br>

                    <label for="resolucion">Resolución de las fotos (150-900 DPI):</label>
                    <input type="range" id="resolucion" name="resolucion" min="150" max="900" step="150" value="150">
                    <output for="resolucion">150 DPI</output><br><br>

                    <label for="anuncio">Anuncio a imprimir*:</label>
                    <select id="anuncio" name="anuncio">
                        <option value="">Seleccione un anuncio</option>
                        <option value="1">Vivienda en Madrid</option>
                        <option value="2">Piso en Barcelona</option>
                        <option value="3">Casa en Sevilla</option>
                    </select><br><br>

                    <label for="fecha">Fecha de recepción deseada:</label>
                    <input type="text" id="fecha" name="fecha" placeholder="dd/mm/yyyy"><br><br>

                    <label>Impresión a color:</label>
                    <input type="radio" id="color_si" name="impresion_color" value="color" checked>
                    <label for="color_si">Color</label>
                    <input type="radio" id="color_no" name="impresion_color" value="bn">
                    <label for="color_no">Blanco y negro</label><br><br>

                    <label>¿Mostrar el precio en el folleto?</label>
                    <input type="checkbox" id="precio" name="mostrar_precio" value="si">
                    <label for="precio">Sí</label>
                </fieldset>

                <br>
                <button type="submit">Enviar solicitud</button>
                <a href="solicitar_folleto.php">Limpiar</a>
            </form>
        </section>
    </main>

    <?php require('pie.php'); ?>

    <dialog class="modal" id="modalErrores">
        <h2>Errores en el formulario</h2>
        <ul id="listaErrores"></ul>
        <button class="cerrar" id="cerrarModal">Cerrar</button>
    </dialog>

    <!-- <script src="./js/solicitar_folleto.js"></script> -->
</body>

</html>