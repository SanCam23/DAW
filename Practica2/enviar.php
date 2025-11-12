<?php
/*
 * MODIFICADO: Tarea 4 (Persona 2)
 * Incluimos el conector a la BD
 */
require_once __DIR__ . '/db.php';

/*
 * MODIFICADO: Tarea 4 (Persona 2)
 * Conectamos a la BD para obtener los tipos de mensajes
 */
$db = conectarDB();
$tipos_mensaje = [];

if ($db) {
    /*
     * Requisito PDF: "mostrar los tipos a partir de la tabla TiposMensajes"
     */
    $sql = "SELECT IdTMensaje, NomTMensaje FROM TIPOSMENSAJES ORDER BY IdTMensaje ASC";
    $resultado = $db->query($sql);

    if ($resultado) {
        $tipos_mensaje = $resultado->fetch_all(MYSQLI_ASSOC);
        $resultado->close();
    }
    $db->close();
}

// Mantenemos la lógica "sticky" para el formulario
$mensaje_previo = $_POST['mensaje'] ?? '';
$tipo_previo = $_POST['tipo'] ?? ''; // Para el 'selected'
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