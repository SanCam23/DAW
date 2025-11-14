<?php
require_once __DIR__ . '/db.php';

// 1️. Recibir ID del anuncio desde GET
$id_anuncio_destino = $_GET['id'] ?? 0;
$id_anuncio_destino = (int)$id_anuncio_destino;

if ($id_anuncio_destino <= 0) {
    echo "<p>ID de anuncio no válido.</p>";
    exit;
}

// 2️. Conectar y obtener tipos de mensaje
$db = conectarDB();
$tipos_mensaje = [];

if ($db) {
    $sql = "SELECT IdTMensaje, NomTMensaje FROM TIPOSMENSAJES ORDER BY IdTMensaje ASC";
    $resultado = $db->query($sql);

    if ($resultado) {
        $tipos_mensaje = $resultado->fetch_all(MYSQLI_ASSOC);
        $resultado->close();
    }
    $db->close();
}

// 3️. Sticky form
$mensaje_previo = $_POST['mensaje'] ?? '';
$tipo_previo = $_POST['tipo'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar mensaje - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/enviar.css">
    <link rel="stylesheet" type="text/css" href="css/print_enviar.css" media="print">
</head>

<body>
    <?php
    $zona = 'publica';
    require('cabecera.php');
    ?>

    <main>
        <form action="confirmacionmensaje.php" method="post">
            <!-- 4️. Input hidden para pasar el ID del anuncio -->
            <input type="hidden" name="id_anuncio_destino" value="<?php echo $id_anuncio_destino; ?>">

            <section>
                <h2>Tipo de mensaje</h2>
                <label for="tipo">Tipo:</label>
                <select id="tipo" name="tipo">
                    <option value="">-- Seleccione un tipo --</option>
                    <?php if (empty($tipos_mensaje)): ?>
                        <option value="" disabled>Error al cargar tipos</option>
                    <?php else: ?>
                        <?php foreach ($tipos_mensaje as $tipo): ?>
                            <option value="<?php echo $tipo['IdTMensaje']; ?>" <?php if ($tipo_previo == $tipo['IdTMensaje']) echo "selected"; ?>>
                                <?php echo htmlspecialchars($tipo['NomTMensaje']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </section>

            <section>
                <h2>Escribe tu mensaje</h2>
                <label for="mensaje">Mensaje:</label><br>
                <textarea id="mensaje" name="mensaje" rows="6" cols="50"><?php echo htmlspecialchars($mensaje_previo); ?></textarea>
            </section>

            <button type="submit">Enviar mensaje</button>
        </form>
    </main>

    <?php require('pie.php'); ?>

    <dialog class="modal" id="modalErrores">
        <h2>Errores en el formulario</h2>
        <ul id="listaErrores"></ul>
        <button class="cerrar" id="cerrarModal">Cerrar</button>
    </dialog>

</body>

</html>