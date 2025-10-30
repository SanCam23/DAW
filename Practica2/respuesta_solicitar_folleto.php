<?php
$nombre = $_POST["nombre"] ?? "";
$email = $_POST["email"] ?? "";
$telefono = $_POST["telefono"] ?? "";
$calle = $_POST["calle"] ?? "";
$numero = $_POST["numero"] ?? "";
$cp = $_POST["cp"] ?? "";
$localidad = $_POST["localidad"] ?? "";
$provincia = $_POST["provincia"] ?? "";
$pais = $_POST["pais"] ?? "";
$color_portada = $_POST["color"] ?? "";
$copias = max(1, (int) ($_POST["copias"] ?? 1)); 
$resolucion = (int) ($_POST["resolucion"] ?? 150);
$anuncio = $_POST["anuncio"] ?? "";
$fecha = $_POST["fecha"] ?? "";
$impresion = $_POST["impresion_color"] ?? "bn";
$mostrar_precio = isset($_POST["mostrar_precio"]) ? "Sí" : "No";
$texto = $_POST["texto"] ?? "";


$tarifas = array(
    "envio" => 10,
    "paginas" => array("p1a4" => 2.0, "p5a10" => 1.8, "p11ymas" => 1.6),
    "color" => array("bn" => 0, "color" => 0.5),
    "resol" => array("baja" => 0, "alta" => 0.2)
);


$paginas = 8;
$fotos = 12;

$resolucion_tipo = ($resolucion > 300) ? "alta" : "baja";


function calcularCoste($pags, $fotos, $color, $resol, $t) {
    $costePaginas = 0;

    if ($pags <= 4) {
        $costePaginas = $pags * $t["paginas"]["p1a4"];
    } 

    elseif ($pags <= 10) {
        $costePaginas += 4 * $t["paginas"]["p1a4"];
        $costePaginas += ($pags - 4) * $t["paginas"]["p5a10"];
    } 

    else {
        $costePaginas += 4 * $t["paginas"]["p1a4"]; 
        $costePaginas += 6 * $t["paginas"]["p5a10"];
        $costePaginas += ($pags - 10) * $t["paginas"]["p11ymas"];
    }

    $costeColor = ($color == "color") ? $fotos * $t["color"]["color"] : 0;
    $costeResol = ($resol == "alta") ? $fotos * $t["resol"]["alta"] : 0;

    return $t["envio"] + $costePaginas + $costeColor + $costeResol;
}

$coste_unitario = calcularCoste($paginas, $fotos, $impresion, $resolucion_tipo, $tarifas);
    $coste_total = $coste_unitario * $copias; 
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
    <title>Respuesta Solicitud Folleto - VENTAPLUS</title>
    <link rel="stylesheet" href="css/general.css" title="Estilo normal">
    <link rel="stylesheet" href="css/respuesta_solicitar_folleto.css">
    <link rel="alternate stylesheet" href="css/contraste_alto.css" title="Alto contraste">
    <link rel="alternate stylesheet" href="css/letra_grande.css" title="Letra Grande">
    <link rel="alternate stylesheet" href="css/contraste_letra.css" title="Letra Grande+Alto contraste">
    <link rel="stylesheet" type="text/css" href="css/print_respuesta_solicitar_folleto.css" media="print">
    <link rel="stylesheet" href="css/fontello.css">
    <link href="https://fonts.googleapis.com/css2?family=Leckerli+One&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
</head>

<body>
    <?php 
        $zona = 'privada';
        include('cabecera.php'); 
    ?>

    <main>
        <h2>Solicitud de folleto recibida</h2>

        <p>Gracias **<?php echo $nombre; ?>**— su solicitud de folleto ha sido registrada correctamente. A continuación se muestran los datos
            introducidos y el coste calculado.</p>

        <section aria-labelledby="datos-titulo">
            <h3 id="datos-titulo">Datos del folleto y envío</h3>
            <dl>
                <dt>Nombre</dt>
                <dd><?php echo $nombre; ?></dd>

                <dt>Correo electrónico</dt>
                <dd><?php echo $email; ?></dd>

                <dt>Teléfono</dt>
                <dd><?php echo $telefono; ?></dd>

                <dt>Dirección de envío</dt>
                <dd><?php echo "$calle, $numero, $cp, $localidad, $provincia, $pais"; ?></dd>

                <dt>Anuncio seleccionado</dt>
                <dd>
                    <?php
                    $anuncio_nombre = "No especificado";
                    if ($anuncio == "1") $anuncio_nombre = "Vivienda en Madrid (ID: 1)";
                    elseif ($anuncio == "2") $anuncio_nombre = "Piso en Barcelona (ID: 2)";
                    elseif ($anuncio == "3") $anuncio_nombre = "Casa en Sevilla (ID: 3)";
                    echo $anuncio_nombre;
                    ?>
                </dd>

                <dt>Color de portada</dt>
                <dd><?php echo $color_portada; ?></dd>

                <dt>Número de copias</dt>
                <dd><?php echo $copias; ?></dd>

                <dt>Resolución elegida</dt>
                <dd><?php echo $resolucion; ?> DPI (Tipo: <?php echo $resolucion_tipo == "alta" ? "Alta" : "Baja"; ?>)</dd>

                <dt>Tipo de Impresión</dt>
                <dd><?php echo ($impresion == "color") ? "Color" : "Blanco y negro"; ?></dd>

                <dt>Mostrar precio en folleto</dt>
                <dd><?php echo $mostrar_precio; ?></dd>

                <dt>Fecha deseada de recepción</dt>
                <dd><?php echo $fecha; ?></dd>

                <dt>Texto adicional</dt>
                <dd><?php echo nl2br($texto); ?></dd>
            </dl>
        </section>
        
        <section aria-labelledby="ficticios-titulo">
            <h3 id="ficticios-titulo">Detalles Ficticios del Anuncio</h3>
            <p>
                *Para el cálculo del coste, se han usado los siguientes valores ficticios del anuncio:<br>
                **Número de páginas:** <?php echo $paginas; ?> páginas<br>
                **Número de fotos:** <?php echo $fotos; ?> fotos
            </p>
        </section>

        <section aria-labelledby="coste-titulo">
            <h3 id="coste-titulo">Coste final del folleto publicitario</h3>
            <p>
                **Coste unitario por folleto:** <?php echo number_format($coste_unitario, 2, ',', '.'); ?> €<br>
                **Número de copias:** <?php echo $copias; ?><br>
                <strong class="coste-total">Coste TOTAL (<?php echo $copias; ?> copias):</strong> <?php echo number_format($coste_total, 2, ',', '.'); ?> €
            </p>
        </section>

        <a href="solicitar_folleto.php" class="volver-formulario">Volver al formulario</a>
    </main>

    <?php include('pie.php'); ?>
</body>
</html>