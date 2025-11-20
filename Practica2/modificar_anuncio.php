<?php
session_start();
require_once 'verificar_sesion.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/validaciones_anuncio.php';

$zona = 'privada';
$db = conectarDB();

$id_anuncio = $_GET['id'] ?? 0;
$id_usuario = $_SESSION['usuario_id'];
$errores = [];
$exito = false;

// Cargar desplegables
$paises = $db->query("SELECT IdPais, NomPais FROM PAISES ORDER BY NomPais")->fetch_all(MYSQLI_ASSOC);
$tipos_anuncio = $db->query("SELECT IdTAnuncio, NomTAnuncio FROM TIPOSANUNCIOS ORDER BY NomTAnuncio")->fetch_all(MYSQLI_ASSOC);
$tipos_vivienda = $db->query("SELECT IdTVivienda, NomTVivienda FROM TIPOSVIVIENDAS ORDER BY NomTVivienda")->fetch_all(MYSQLI_ASSOC);

// Variables iniciales
$titulo = $texto = $precio = $ciudad = $pais = $tipo_anuncio = $tipo_vivienda = "";

// 1. CARGAR DATOS ACTUALES
$stmt_load = $db->prepare("SELECT * FROM ANUNCIOS WHERE IdAnuncio = ? AND Usuario = ?");
$stmt_load->bind_param("ii", $id_anuncio, $id_usuario);
$stmt_load->execute();
$res = $stmt_load->get_result();

if ($fila = $res->fetch_assoc()) {
    // Rellenar variables para el formulario
    $titulo = $fila['Titulo'];
    $texto = $fila['Texto']; // En BD es 'Texto', en form 'descripcion'
    $precio = $fila['Precio'];
    $ciudad = $fila['Ciudad'];
    $pais = $fila['Pais'];
    $tipo_anuncio = $fila['TAnuncio'];
    $tipo_vivienda = $fila['TVivienda'];
} else {
    echo "Error: Anuncio no encontrado o no tienes permiso para editarlo.";
    exit;
}
$stmt_load->close();

// 2. PROCESAR ACTUALIZACIÓN
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST["titulo"]);
    $texto = trim($_POST["descripcion"]);
    $precio = trim($_POST["precio"]);
    $ciudad = trim($_POST["ciudad"]);
    $pais = $_POST["pais"];
    $tipo_anuncio = $_POST["tipo_anuncio"];
    $tipo_vivienda = $_POST["tipo_vivienda"];

    $errores = validarAnuncio($titulo, $texto, $precio, $ciudad, $pais, $tipo_anuncio, $tipo_vivienda);

    if (empty($errores)) {
        $sql_upd = "UPDATE ANUNCIOS SET Titulo=?, Texto=?, Precio=?, Ciudad=?, Pais=?, TAnuncio=?, TVivienda=? 
                    WHERE IdAnuncio=? AND Usuario=?";
        $stmt_upd = $db->prepare($sql_upd);
        $stmt_upd->bind_param("ssdsiiiii", $titulo, $texto, $precio, $ciudad, $pais, $tipo_anuncio, $tipo_vivienda, $id_anuncio, $id_usuario);

        if ($stmt_upd->execute()) {
            $exito = true;
        } else {
            $errores[] = "Error al actualizar: " . $stmt_upd->error;
        }
        $stmt_upd->close();
    }
}
$db->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Modificar Anuncio - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/crear_anuncio.css">
</head>

<body>
    <?php require('cabecera.php'); ?>

    <main>
        <?php if ($exito): ?>
            <section class="confirmacion">
                <h3>Modificación realizada con éxito</h3>
                <p>Los datos de tu anuncio se han actualizado.</p>
                <ul>
                    <li><strong>Título:</strong> <?php echo htmlspecialchars($titulo); ?></li>
                </ul>
                <a href="misanuncios.php" class="boton-crear">Volver a mis anuncios</a>
            </section>
        <?php else: ?>
            <h2>Modificar anuncio</h2>
            <?php if (!empty($errores)): ?>
                <div class="errores">
                    <ul><?php foreach ($errores as $e) echo "<li>$e</li>"; ?></ul>
                </div>
            <?php endif; ?>

            <?php
            $texto_boton = "Guardar cambios";
            require __DIR__ . '/includes/formulario_anuncio.php';
            ?>
        <?php endif; ?>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>