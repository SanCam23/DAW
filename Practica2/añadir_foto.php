<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Listado de anuncios del usuario en VENTAPLUS.">
    <meta name="keywords" content="mis anuncios, viviendas, pisos, venta, alquiler">
    <meta name="author" content="Santino Campessi Lojo">
    <meta name="author" content="Mario Laguna Contreras">
    <title>Mis anuncios - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/añadir_foto.css">
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print">
</head>

<?php

$anuncios = [
    1 => 'Apartamento en Barcelona',
    2 => 'Vivienda en Madrid',
    3 => 'Casa en Sevilla'
];

$id_seleccionado = isset($_GET['id']) ? intval($_GET['id']) : null;


$zona = 'privada';
require('cabecera.php');
require_once 'verificar_sesion.php';
?>

<main>
    <h1>Añadir foto a anuncio</h1>

    <form action="#" method="post" enctype="multipart/form-data">
        <section>
            <label for="foto">Selecciona la foto:</label><br>
            <input type="file" id="foto" name="foto" accept="image/*" required>
        </section>

        <section>
            <label for="alt">Texto alternativo (mínimo 10 caracteres):</label><br>
            <input type="text" id="alt" name="alt" minlength="10" required>
        </section>

        <section>
            <label for="titulo">Título de la foto:</label><br>
            <input type="text" id="titulo" name="titulo">
        </section>

        <section>
            <label for="anuncio">Selecciona el anuncio:</label><br>
            <select id="anuncio" name="anuncio" <?php echo $id_seleccionado ? 'disabled' : ''; ?> required>
                <option value="">-- Selecciona un anuncio --</option>
                <?php foreach ($anuncios as $id => $titulo): ?>
                    <option value="<?php echo $id; ?>" 
                        <?php echo ($id_seleccionado == $id) ? 'selected' : ''; ?>>
                        <?php echo $titulo; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if($id_seleccionado): ?>
                <input type="hidden" name="anuncio" value="<?php echo $id_seleccionado; ?>">
            <?php endif; ?>
        </section>

        <button type="submit">Añadir foto</button>
    </form>
</main>

<?php require('pie.php'); ?>
