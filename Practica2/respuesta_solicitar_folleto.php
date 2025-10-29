<?php
// -------------------------
// Recibir los datos del formulario
// -------------------------
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
$copias = (int) ($_POST["copias"] ?? 1);
$resolucion = (int) ($_POST["resolucion"] ?? 150);
$anuncio = $_POST["anuncio"] ?? "";
$fecha = $_POST["fecha"] ?? "";
$impresion = $_POST["impresion_color"] ?? "bn";
$mostrar_precio = isset($_POST["mostrar_precio"]) ? "Sí" : "No";
$texto = $_POST["texto"] ?? "";

// -------------------------
// Calcular coste del folleto (según práctica 6)
// -------------------------
$tarifas = array(
    "envio" => 10,
    "paginas" => array("menos5" => 2, "entre5y10" => 1.8, "mas10" => 1.6),
    "color" => array("bn" => 0, "color" => 0.5),
    "resol" => array("baja" => 0, "alta" => 0.2)
);

// Usamos valores genéricos de ejemplo: 8 páginas, 12 fotos
$paginas = 8;
$fotos = 12;

// Determinar si resolución es baja o alta
$resolucion_tipo = ($resolucion > 300) ? "alta" : "baja";

// Calcular coste
function calcularCoste($pags, $fotos, $color, $resol, $t) {
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
    <?php include('cabecera.php'); ?>

    <main>
        <h2>Solicitud de folleto recibida</h2>

        <p>Gracias — su solicitud de folleto ha sido registrada correctamente. A continuación se muestran los datos
            introducidos y el coste calculado.</p>

        <section aria-labelledby="datos-titulo">
            <h3 id="datos-titulo">Datos recibidos</h3>
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
                    if ($anuncio == "1") echo "Vivienda en Madrid (ID: 1)";
                    elseif ($anuncio == "2") echo "Piso en Barcelona (ID: 2)";
                    elseif ($anuncio == "3") echo "Casa en Sevilla (ID: 3)";
                    else echo "No especificado";
                    ?>
                </dd>

                <dt>Color de portada</dt>
                <dd><?php echo $color_portada; ?></dd>

                <dt>Número de copias</dt>
                <dd><?php echo $copias; ?></dd>

                <dt>Resolución elegida</dt>
                <dd><?php echo $resolucion; ?> DPI</dd>

                <dt>Impresión</dt>
                <dd><?php echo ($impresion == "color") ? "Color" : "Blanco y negro"; ?></dd>

                <dt>Mostrar precio en folleto</dt>
                <dd><?php echo $mostrar_precio; ?></dd>

                <dt>Fecha deseada de recepción</dt>
                <dd><?php echo $fecha; ?></dd>

                <dt>Texto adicional</dt>
                <dd><?php echo nl2br($texto); ?></dd>
            </dl>
        </section>

        <section aria-labelledby="coste-titulo">
            <h3 id="coste-titulo">Coste del folleto</h3>
            <p>
                <strong>Coste por folleto:</strong> <?php echo number_format($coste_unitario, 2); ?> €<br>
                <strong>Número de copias:</strong> <?php echo $copias; ?><br>
                <strong>Coste total:</strong> <?php echo number_format($coste_total, 2); ?> €
            </p>
        </section>

        <a href="solicitar_folleto.php">Volver al formulario</a>
    </main>

    <?php include('pie.php'); ?>
</body>
</html>
