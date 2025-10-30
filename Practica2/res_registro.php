<?php
$usuario = $_POST["usuario"] ?? "";
$password = $_POST["password"] ?? "";
$confirm_password = $_POST["confirm_password"] ?? "";
$email = $_POST["email"] ?? "";
$sexo = $_POST["sexo"] ?? "";
$fecha_nacimiento = $_POST["fecha_nacimiento"] ?? "";
$ciudad = $_POST["ciudad"] ?? "";
$pais = $_POST["pais"] ?? "";

$errores = [];

// --- Validaciones ---
if (trim($usuario) === "" || trim($password) === "" || trim($confirm_password) === "") {
    $errores[] = "Debe completar los campos obligatorios: usuario, contraseña y repetir contraseña.";
}

if ($password !== $confirm_password) {
    $errores[] = "Las contraseñas no coinciden.";
}

if ($sexo === "") {
    $errores[] = "Debe seleccionar un sexo.";
}

if ($fecha_nacimiento === "") {
    $errores[] = "Debe indicar una fecha de nacimiento.";
} else {
    $partes = explode("/", $fecha_nacimiento);
    if (count($partes) === 3) {
        $dia = (int)$partes[0];
        $mes = (int)$partes[1];
        $anio = (int)$partes[2];
        if (checkdate($mes, $dia, $anio)) {
            $nacimiento = new DateTime("$anio-$mes-$dia");
            $hoy = new DateTime();
            $edad = $hoy->diff($nacimiento)->y;
            if ($edad < 18) $errores[] = "Debe ser mayor de 18 años.";
        } else {
            $errores[] = "La fecha de nacimiento no es válida.";
        }
    } else {
        $errores[] = "Formato de fecha incorrecto (use dd/mm/yyyy).";
    }
}

if (!empty($errores)) {
    $mensaje = urlencode(implode(" ", $errores));
    header("Location: error_registro.php?error=$mensaje");
    exit();
}

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
    <title>Respuesta Registro - VENTAPLUS</title>
    <link rel="stylesheet" href="css/general.css" title="Estilo normal">
    <link rel="stylesheet" href="css/res_registro.css">
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

    <main>
        <h2>Registro completado correctamente</h2>
        <p>Gracias por registrarte, <?php echo $usuario; ?>.</p>

        <section id="datos-registro">
            <dl>
                <dt>Usuario:</dt>
                <dd><?php echo $usuario; ?></dd>
                <dt>Correo:</dt>
                <dd><?php echo $email; ?></dd>
                <dt>Sexo:</dt>
                <dd><?php echo $sexo; ?></dd>
                <dt>Fecha de nacimiento:</dt>
                <dd><?php echo $fecha_nacimiento; ?></dd>
                <dt>Ciudad:</dt>
                <dd><?php echo $ciudad; ?></dd>
                <dt>País:</dt>
                <dd><?php echo $pais; ?></dd>
                <dt>Foto:</dt>
                <dd>No se muestra por seguridad (implementación posterior).</dd>
            </dl>
        </section>

        <a href="index.php" class="volver">Volver al inicio</a>
    </main>

    <?php include('pie.php'); ?>
</body>

</html>