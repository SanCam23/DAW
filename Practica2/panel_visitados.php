<?php
require_once 'anuncios.php';

// 2. Comprobamos si la cookie existe y no está vacía
if (isset($_COOKIE['ultimos_visitados']) && $_COOKIE['ultimos_visitados'] !== '[]') {
    
    // 3. Decodificamos el JSON
    $visitados = json_decode($_COOKIE['ultimos_visitados'], true);

    if (is_array($visitados) && !empty($visitados)) {
        
        // 4. Invertimos el array para mostrar los más nuevos primero
        $visitados = array_reverse($visitados);
        
        // 5. Empezamos a dibujar el panel
        echo '<section id="ultimos-anuncios">';
        echo '<h2 style="width: 100%; text-align: center;">Últimos anuncios visitados</h2>';

        // 6. Recorremos los IDs guardados
        foreach ($visitados as $id) {
            
            // 7. Obtenemos los datos del anuncio
            // Si el ID es par, anuncio 2; si es impar, anuncio 1.
            $id = (int)$id;
            $anuncio_visitado = ($id % 2 == 0) ? $anuncios[2] : $anuncios[1];

            // 8. Mostramos el anuncio
            echo '<section class="anuncio">';
            echo '  <figure>';
            echo '    <img src="' . $anuncio_visitado["foto_principal"] . '" alt="Foto de ' . $anuncio_visitado["titulo"] . '">';
            echo '  </figure>';
            echo '  <h3>' . $anuncio_visitado["titulo"] . '</h3>';
            echo '  <p>Ciudad: ' . $anuncio_visitado["ciudad"] . '</p>';
            echo '  <p>País: ' . $anuncio_visitado["pais"] . '</p>';
            echo '  <p>Precio: ' . $anuncio_visitado["precio"] . '</p>';
            echo '  <a href="detalle_anuncio.php?id=' . $id . '">Ver detalle</a>';
            echo '</section>';
        }
        
        echo '</section>';
    }
}
?>