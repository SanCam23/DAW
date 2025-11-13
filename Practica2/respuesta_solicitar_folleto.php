<?php
session_start();
require_once __DIR__ . '/db.php';
require_once 'verificar_sesion.php';

// 1. Recoger TODOS los datos del formulario
$nombre = $_POST["nombre"] ?? "";
$email = $_POST["email"] ?? "";
$telefono = $_POST["telefono"] ?? "";
$calle = $_POST["calle"] ?? "";
$numero = $_POST["numero"] ?? "";
$cp = $_POST["cp"] ?? "";
$localidad = $_POST["localidad"] ?? "";
$provincia = $_POST["provincia"] ?? "";
$pais = $_POST["pais"] ?? "";
$color_portada = $_POST["color"] ?? "#000000";
$copias = max(1, (int) ($_POST["copias"] ?? 1));
$resolucion = (int) ($_POST["resolucion"] ?? 150);
$id_anuncio = $_POST["anuncio"] ?? "";
$fecha_deseada = $_POST["fecha"] ?? "";
$impresion = $_POST["impresion_color"] ?? "bn";
$mostrar_precio_raw = isset($_POST["mostrar_precio"]) ? 1 : 0;
$mostrar_precio_txt = ($mostrar_precio_raw == 1) ? "Sí" : "No";
$texto_adicional = $_POST["texto"] ?? "";

$direccion_completa = "$calle, $numero, $cp, $localidad, $provincia, $pais";

// 2. Lógica de cálculo de costes
$tarifas = array(
    "envio" => 10,
    "paginas" => array("p1a4" => 2.0, "p5a10" => 1.8, "p11ymas" => 1.6),
    "color" => array("bn" => 0, "color" => 0.5),
    "resol" => array("baja" => 0, "alta" => 0.2)
);
$paginas_ficticias = 8;
$fotos_ficticias = 12;
$resolucion_tipo = ($resolucion > 300) ? "alta" : "baja";

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

$coste_unitario = calcularCoste($paginas_ficticias, $fotos_ficticias, $impresion, $resolucion_tipo, $tarifas);
$coste_total = $coste_unitario * $copias;

// 3. Convertir fecha deseada a formato SQL (Y-m-d)
$fecha_sql = null;
if (preg_match("/^(\d{2})\/(\d{2})\/(\d{4})$/", $fecha_deseada, $partes)) {
    // Formato dd/mm/yyyy -> yyyy-mm-dd
    $fecha_sql = "{$partes[3]}-{$partes[2]}-{$partes[1]}";
}

// 4. Lógica de INSERT en la BD
$db = conectarDB();
$errores = [];
$anuncio_nombre = "No especificado";

if (!$db) {
    $errores[] = "Error al conectar con la base de datos.";
} else {
    // Primero, obtenemos el nombre del anuncio para mostrarlo
    if (!empty($id_anuncio)) {
        $sql_nombre = "SELECT Titulo FROM ANUNCIOS WHERE IdAnuncio = ?";
        $stmt_nombre = $db->prepare($sql_nombre);
        $stmt_nombre->bind_param("i", $id_anuncio);
        $stmt_nombre->execute();
        $res_nombre = $stmt_nombre->get_result();
        if ($res_nombre->num_rows > 0) {
            $anuncio_nombre = $res_nombre->fetch_assoc()['Titulo'];
        }
        $stmt_nombre->close();
    } else {
        $errores[] = "No se seleccionó un anuncio válido.";
    }


    /* Insertar en la tabla SOLICITUDES */
    $sql_insert = "INSERT INTO SOLICITUDES 
                   (Anuncio, Texto, Nombre, Email, Direccion, Telefono, Color, Copias, 
                    Resolucion, Fecha, IColor, IPrecio, Coste, FRegistro)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt_insert = $db->prepare($sql_insert);

    $es_color = ($impresion == "color") ? 1 : 0;

    $stmt_insert->bind_param(
        "issssssiisiid",
        $id_anuncio,
        $texto_adicional,
        $nombre,
        $email,
        $direccion_completa,
        $telefono,
        $color_portada,
        $copias,
        $resolucion,
        $fecha_sql,
        $es_color,
        $mostrar_precio_raw,
        $coste_total
    );

    if (empty($errores) && !$stmt_insert->execute()) {
        $errores[] = "Error al guardar la solicitud: " . $stmt_insert->error;
    }

    if ($stmt_insert) {
        $stmt_insert->close();
    }
    $db->close();
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respuesta Solicitud Folleto - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/respuesta_solicitar_folleto.css">
    <link rel="stylesheet" type="text/css" href="css/print_respuesta_solicitar_folleto.css" media="print">
</head>

<body>
    <?php
    $zona = 'privada';
    require('cabecera.php');
    ?>

    <main>

        <?php if (empty($errores)): ?>

            <h2>Solicitud de folleto recibida</h2>
            <p>Gracias **<?php echo htmlspecialchars($nombre); ?>**— su solicitud de folleto ha sido registrada correctamente. A continuación se muestran los datos
                introducidos y el coste calculado.</p>

            <section aria-labelledby="datos-titulo">
                <h3 id="datos-titulo">Datos del folleto y envío</h3>
                <dl>
                    <dt>Nombre</dt>
                    <dd><?php echo htmlspecialchars($nombre); ?></dd>
                    <dt>Correo electrónico</dt>
                    <dd><?php echo htmlspecialchars($email); ?></dd>
                    <dt>Teléfono</dt>
                    <dd><?php echo htmlspecialchars($telefono); ?></dd>
                    <dt>Dirección de envío</dt>
                    <dd><?php echo htmlspecialchars($direccion_completa); ?></dd>
                    <dt>Anuncio seleccionado</dt>
                    <dd><?php echo htmlspecialchars($anuncio_nombre); ?> (ID: <?php echo $id_anuncio; ?>)</dd>
                    <dt>Color de portada</dt>
                    <dd><?php echo htmlspecialchars($color_portada); ?></dd>
                    <dt>Número de copias</dt>
                    <dd><?php echo $copias; ?></dd>
                    <dt>Resolución elegida</dt>
                    <dd><?php echo $resolucion; ?> DPI (Tipo: <?php echo $resolucion_tipo == "alta" ? "Alta" : "Baja"; ?>)</dd>
                    <dt>Tipo de Impresión</dt>
                    <dd><?php echo ($impresion == "color") ? "Color" : "Blanco y negro"; ?></dd>
                    <dt>Mostrar precio en folleto</dt>
                    <dd><?php echo $mostrar_precio_txt; ?></dd>
                    <dt>Fecha deseada de recepción</dt>
                    <dd><?php echo htmlspecialchars($fecha_deseada); ?></dd>
                    <dt>Texto adicional</dt>
                    <dd><?php echo nl2br(htmlspecialchars($texto_adicional)); ?></dd>
                </dl>
            </section>

            <section aria-labelledby="ficticios-titulo">
                <h3 id="ficticios-titulo">Detalles Ficticios del Anuncio</h3>
                <p>
                    *Para el cálculo del coste, se han usado los siguientes valores ficticios del anuncio:<br>
                    **Número de páginas:** <?php echo $paginas_ficticias; ?> páginas<br>
                    **Número de fotos:** <?php echo $fotos_ficticias; ?> fotos
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

        <?php else: ?>
            <h2>Error al procesar la solicitud</h2>
            <p>Se han producido los siguientes errores:</p>
            <ul style="color: red; margin: 20px 40px;">
                <?php foreach ($errores as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <a href="solicitar_folleto.php" class="volver-formulario">Volver al formulario</a>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>