<?php
$zona = 'publica';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Baja completada - VENTAPLUS">
    <meta name="keywords" content="baja, cuenta eliminada, VENTAPLUS">
    <meta name="author" content="Santino Campessi Lojo">
    <meta name="author" content="Mario Laguna Contreras">
    <title>Baja completada - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <style>
        .baja-completada {
            text-align: center;
            padding: 40px 20px;
        }

        .baja-completada h2 {
            color: #4caf50;
            margin-bottom: 20px;
        }

        .acciones {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 25px;
            background: #4caf50;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .btn.registrar {
            background: #2196f3;
        }
    </style>
</head>

<body>
    <?php require('cabecera.php'); ?>

    <main>
        <section class="baja-completada">
            <h2>Baja completada</h2>
            <p>Su cuenta y todos sus datos asociados han sido eliminados correctamente.</p>
            <p>Lamentamos que nos deje. Esperamos volver a verle pronto.</p>

            <div class="acciones">
                <a href="index.php" class="btn">Volver al inicio</a>
                <a href="registro.php" class="btn registrar">Crear nueva cuenta</a>
            </div>
        </section>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>