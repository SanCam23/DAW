<?php
// -------------------------
// Cálculo tabla de costes
// -------------------------
$tarifas = array(
    "envio" => 10,
    "paginas" => array("menos5" => 2, "entre5y10" => 1.8, "mas10" => 1.6),
    "color" => array("bn" => 0, "color" => 0.5),
    "resol" => array("baja" => 0, "alta" => 0.2)
);

function calcularCoste($pags, $fotos, $color, $resol, $t)
{
    $costePaginas = 0;

    if ($pags < 5) {
        $costePaginas = $pags * $t["paginas"]["menos5"];
    } elseif ($pags <= 10) {
        $costePaginas = 5 * $t["paginas"]["menos5"];
        $costePaginas += ($pags - 5) * $t["paginas"]["entre5y10"];
    } else {
        $costePaginas = 5 * $t["paginas"]["menos5"];
        $costePaginas += 5 * $t["paginas"]["entre5y10"];
        $costePaginas += ($pags - 10) * $t["paginas"]["mas10"];
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
    <link rel="stylesheet" href="css/general.css" title="Estilo normal">
    <link rel="stylesheet" href="css/solicitar_folleto.css">
    <link rel="alternate stylesheet" href="css/contraste_alto.css" title="Alto contraste">
    <link rel="alternate stylesheet" href="css/letra_grande.css" title="Letra Grande">
    <link rel="alternate stylesheet" href="css/contraste_letra.css" title="Letra Grande+Alto contraste">
    <link rel="stylesheet" type="text/css" href="css/print_solicitar_folleto.css" media="print">
    <link rel="stylesheet" href="css/fontello.css">
    <link href="https://fonts.googleapis.com/css2?family=Leckerli+One&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Cabecera -->
    <header class="Cabecera">
        <section class="texto">
            <figure><img src="logo.png" alt="Logo"></figure>
            <section class="titulo">
                <h1>VENTAPLUS</h1>
                <h3>¿Buscas tu próximo hogar? Empieza aquí.</h3>
            </section>
        </section>

        <nav class="menu-escritorio">
            <ul>
                <li><a href="index.html"><i class="icon-home"></i>Inicio</a></li>
                <li><a href="formulario.html"><i class="icon-search"></i>Buscar</a></li>
                <li><a href="menu_usuario_registrado.html"><i class="icon-user"></i>Mi Perfil</a></li>
            </ul>
        </nav>

        <nav class="menu-movil">
            <ul>
                <li><a href="index.html"><i class="icon-home"></i></a></li>
                <li></i><a href="formulario.html"><i class="icon-search"></i></a></li>
                <li><a href="menu_usuario_registrado.html"><i class="icon-user"></i></a></li>
            </ul>
        </nav>
    </header>

    <!-- Contenido principal -->
    <main>
        <h2>Solicitar folleto publicitario</h2>
        <p>
            Rellena el siguiente formulario para solicitar un folleto publicitario impreso
            de uno de tus anuncios. Los campos marcados con * son obligatorios.
        </p>

        <!-- Tabla de tarifas -->
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
                        <td>&lt; 5 páginas</td>
                        <td>2 € / página</td>
                    </tr>
                    <tr>
                        <td>Entre 5 y 10 páginas</td>
                        <td>1.8 € / página</td>
                    </tr>
                    <tr>
                        <td>&gt; 10 páginas</td>
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
                        <td>≤ 300 dpi</td>
                        <td>0 € / foto</td>
                    </tr>
                    <tr>
                        <td>&gt; 300 dpi</td>
                        <td>0.2 € / foto</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Tabla de costes generada en PHP -->
        <section>
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
                        echo "<td>" . number_format($coste, 2) . " €</td>";

                        $coste = calcularCoste($p, $fotos[$i], "bn", "alta", $tarifas);
                        echo "<td>" . number_format($coste, 2) . " €</td>";

                        $coste = calcularCoste($p, $fotos[$i], "color", "baja", $tarifas);
                        echo "<td>" . number_format($coste, 2) . " €</td>";

                        $coste = calcularCoste($p, $fotos[$i], "color", "alta", $tarifas);
                        echo "<td>" . number_format($coste, 2) . " €</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <!-- Formulario -->
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
                    <input type="text" id="fecha" name="fecha"><br><br>

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
                <button type="reset">Borrar</button>
            </form>
        </section>
    </main>

    <footer>
        <p>2025 VENTAPLUS | Proyecto DAW | <a href="accesibilidad.html">Accesibilidad</a>.</p>
    </footer>

    <dialog class="modal" id="modalErrores">
        <h2>Errores en el formulario</h2>
        <ul id="listaErrores"></ul>
        <button class="cerrar" id="cerrarModal">Cerrar</button>
    </dialog>

    <script src="./js/solicitar_folleto.js"></script>
</body>

</html>