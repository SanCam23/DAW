<?php
session_start();
require_once 'verificar_sesion.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/validaciones_anuncio.php';

$zona = 'privada';
$db = conectarDB();

// Inicializar variables vacías
$titulo = $texto = $precio = $ciudad = $pais = $tipo_anuncio = $tipo_vivienda = "";
$errores = [];
$exito = false;
$id_nuevo_anuncio = 0;

// Cargar datos para los desplegables
$paises = $db->query("SELECT IdPais, NomPais FROM PAISES ORDER BY NomPais")->fetch_all(MYSQLI_ASSOC);
$tipos_anuncio = $db->query("SELECT IdTAnuncio, NomTAnuncio FROM TIPOSANUNCIOS ORDER BY NomTAnuncio")->fetch_all(MYSQLI_ASSOC);
$tipos_vivienda = $db->query("SELECT IdTVivienda, NomTVivienda FROM TIPOSVIVIENDAS ORDER BY NomTVivienda")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST["titulo"] ?? "");
    $texto = trim($_POST["descripcion"] ?? "");
    $precio = trim($_POST["precio"] ?? "");
    $ciudad = trim($_POST["ciudad"] ?? "");
    $pais = $_POST["pais"] ?? "";
    $tipo_anuncio = $_POST["tipo_anuncio"] ?? "";
    $tipo_vivienda = $_POST["tipo_vivienda"] ?? "";

    $errores = validarAnuncio($titulo, $texto, $precio, $ciudad, $pais, $tipo_anuncio, $tipo_vivienda);

    if (empty($errores)) {
        // Insertar en BD
        // Nota: 'Alternativo' es NOT NULL, ponemos un texto temporal.
        $alt_temp = "Pendiente de imagen";
        $usuario_id = $_SESSION['usuario_id'];

        $sql = "INSERT INTO ANUNCIOS (Titulo, Texto, Precio, Ciudad, Pais, TAnuncio, TVivienda, Usuario, Alternativo, FRegistro) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssdsiiiis", $titulo, $texto, $precio, $ciudad, $pais, $tipo_anuncio, $tipo_vivienda, $usuario_id, $alt_temp);

        if ($stmt->execute()) {
            $exito = true;
            $id_nuevo_anuncio = $db->insert_id;
        } else {
            $errores[] = "Error en la base de datos: " . $stmt->error;
        }
        $stmt->close();
    }
}
$db->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Crear Anuncio - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/crear_anuncio.css">
</head>

<body>
    <?php require('cabecera.php'); ?>

    <main>
        <?php if ($exito): ?>
            <section class="confirmacion">
                <h3>¡Anuncio creado correctamente!</h3>
                <p>Datos guardados:</p>
                <ul>
                    <li><strong>Título:</strong> <?php echo htmlspecialchars($titulo); ?></li>
                    <li><strong>Ciudad:</strong> <?php echo htmlspecialchars($ciudad); ?></li>
                    <li><strong>Precio:</strong> <?php echo number_format($precio, 2); ?> €</li>
                </ul>
                <p>Ahora puedes añadir la primera foto a tu anuncio:</p>
                <a href="añadir_foto.php?id=<?php echo $id_nuevo_anuncio; ?>" class="boton-crear">Añadir Foto</a>
            </section>
        <?php else: ?>
            <h2>Crear un nuevo anuncio</h2>
            <?php if (!empty($errores)): ?>
                <div class="errores">
                    <ul><?php foreach ($errores as $e) echo "<li>$e</li>"; ?></ul>
                </div>
            <?php endif; ?>

            <?php
            $texto_boton = "Crear anuncio";
            require __DIR__ . '/includes/formulario_anuncio.php';
            ?>
        <?php endif; ?>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>