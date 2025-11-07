<?php
require 'anuncios.php';

// NUEVO: Tarea C3 - Lógica para GUARDAR la cookie de últimos visitados
// -----------------------------------------------------------------

// 1. Definimos el nombre de la cookie
$cookie_name = 'ultimos_visitados';

// 2. Obtenemos el ID del anuncio actual (tu código ya lo hacía)
$id_actual = $_GET['id'] ?? 1;
// Forzamos que sea un entero para seguridad
$id_actual = (int)$id_actual;

// 3. Leemos la cookie actual. Usamos '[]' (un JSON array vacío) si no existe.
$visitados_cookie = $_COOKIE[$cookie_name] ?? '[]';
$visitados_array = json_decode($visitados_cookie, true);

// 4. Verificamos que sea un array (por si la cookie se corrompe)
if (!is_array($visitados_array)) {
    $visitados_array = [];
}

// 5. Lógica de la lista [cite: 207]
// Si el anuncio YA está en la lista, lo eliminamos para volver a añadirlo al final.
$visitados_array = array_diff($visitados_array, [$id_actual]);

// 6. Añadimos el ID actual al FINAL del array
$visitados_array[] = $id_actual;

// 7. Nos aseguramos de que solo haya 4 anuncios [cite: 202]
// Si hay más de 4, eliminamos el más antiguo (el primero del array)
while (count($visitados_array) > 4) {
    array_shift($visitados_array); // array_shift() elimina el primer elemento
}

// 8. Preparamos los datos para guardar la cookie
$json_visitados = json_encode($visitados_array);
$expiracion = time() + (7 * 24 * 60 * 60); // 1 semana [cite: 209]
$path = '/'; // Disponible en todo el sitio

// 9. ¡Guardamos la cookie!
// Usamos httponly: true para que NO sea accesible por JavaScript [cite: 847]
setcookie(
    $cookie_name,       // Nombre
    $json_visitados,    // Valor (el JSON)
    [
        'expires' => $expiracion,
        'path' => $path,
        'httponly' => true // Recomendación de seguridad de la práctica [cite: 847]
    ]
);
// FIN Tarea C3 - Guardar Cookie
// -----------------------------------------------------------------


$id = $_GET['id'] ?? 1;

$anuncio = ($id % 2 == 0) ? $anuncios[2] : $anuncios[1];
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
    <title><?= $anuncio["titulo"]; ?> - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/anuncio.css">
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print">
</head>

<body>
    <?php
        $zona = 'privada';
        require('cabecera.php');
        require_once 'verificar_sesion.php'; 
    ?>

    <main>
        <article>
            <section class="col-izquierda">
                <h2><?= $anuncio["titulo"]; ?></h2>

                <figure class="foto-principal">
                    <img src="<?= $anuncio["foto_principal"]; ?>" alt="Foto principal del anuncio">
                </figure>

                <section class="galeria">
                    <h3>Galería de imágenes</h3>
                    <?php foreach ($anuncio["fotos"] as $foto): ?>
                        <figure><img src="<?= $foto; ?>" alt="Imagen del anuncio"></figure>
                    <?php endforeach; ?>
                </section>
            </section>

            <section class="col-derecha">
                <section class="info-general">
                    <h3>Información general</h3>
                    <p><strong><?= $anuncio["titulo"]; ?></strong></p>
                    <p>Tipo de anuncio: <?= $anuncio["tipo_anuncio"]; ?></p>
                    <p>Tipo de vivienda: <?= $anuncio["tipo_vivienda"]; ?></p>
                    <p>Fecha: <?= $anuncio["fecha"]; ?></p>
                    <p>Ciudad: <?= $anuncio["ciudad"]; ?></p>
                    <p>País: <?= $anuncio["pais"]; ?></p>
                    <p>Precio: <?= $anuncio["precio"]; ?></p>
                </section>

                <section class="descripcion">
                    <h3>Descripción</h3>
                    <p><?= $anuncio["descripcion"]; ?></p>
                </section>

                <section class="caracteristicas">
                    <h3>Características</h3>
                    <ul>
                        <?php foreach ($anuncio["caracteristicas"] as $caracteristica): ?>
                            <li><?= $caracteristica; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>

                <p><strong>Publicado por:</strong> <?= $anuncio["usuario"]; ?></p>
            </section>
        </article>

        <section class="contacto">
            <h3>Enviar mensaje</h3>
            <p><a href="enviar.php">Ir al formulario de contacto</a></p>
        </section>
    </main>
    <?php require_once 'panel_visitados.php'; ?>
    <?php require('pie.php'); ?>
</body>

</html>