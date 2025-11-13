<?php
require_once __DIR__ . '/db.php';

// 1. Comprobamos si la cookie existe y no está vacía
if (isset($_COOKIE['ultimos_visitados']) && $_COOKIE['ultimos_visitados'] !== '[]') {

    // 2. Decodificamos el JSON
    $visitados = json_decode($_COOKIE['ultimos_visitados'], true);

    if (is_array($visitados) && !empty($visitados)) {

        // 3. Invertimos el array para mostrar los más nuevos primero
        $visitados = array_reverse($visitados);

        // 4. Conectamos a la BD para buscar ESTOS IDs
        $db = conectarDB();
        $anuncios_visitados = [];

        if ($db) {
            /* 5. Preparamos una consulta IN () para traer solo los anuncios que están en nuestra cookie, de forma segura */
            // Creamos los placeholders: (?,?,?,?)
            $placeholders = implode(',', array_fill(0, count($visitados), '?'));

            // Creamos los tipos: "iiii"
            $types = str_repeat('i', count($visitados));

            $sql = "SELECT a.IdAnuncio, a.Titulo, a.FPrincipal, a.Alternativo, 
                           a.Ciudad, a.Precio, p.NomPais
                    FROM ANUNCIOS a
                    LEFT JOIN PAISES p ON a.Pais = p.IdPais
                    WHERE a.IdAnuncio IN ($placeholders)";

            $stmt = $db->prepare($sql);

            // $visitados "desempaqueta" el array [5, 4, 3] en argumentos
            $stmt->bind_param($types, ...$visitados);

            $stmt->execute();
            $resultado = $stmt->get_result();

            // Mapa para reordenar los resultados
            // (Ej: [ 5 => [datos_anuncio_5], 3 => [datos_anuncio_3] ])
            if ($resultado) {
                while ($row = $resultado->fetch_assoc()) {
                    $anuncios_visitados[$row['IdAnuncio']] = $row;
                }
            }
            $stmt->close();
            $db->close();
        }

        // 6. Empezamos a dibujar el panel
        echo '<section id="ultimos-anuncios">';
        echo '<h2 style="width: 100%; text-align: center;">Últimos anuncios visitados</h2>';

        /*
         * 7. Recorremos el array de la cookie (que tiene el orden correcto)
         * y usamos el "mapa" de anuncios que trajimos de la BD.
         */
        foreach ($visitados as $id) {

            // Si el anuncio existe en nuestro mapa, lo mostramos
            if (isset($anuncios_visitados[$id])) {
                $anuncio = $anuncios_visitados[$id];
?>
                <section class="anuncio">
                    <figure>
                        <img src="<?php echo htmlspecialchars($anuncio["FPrincipal"]); ?>" alt="<?php echo htmlspecialchars($anuncio["Alternativo"]); ?>">
                    </figure>
                    <h3><?php echo htmlspecialchars($anuncio["Titulo"]); ?></h3>
                    <p>Ciudad: <?php echo htmlspecialchars($anuncio["Ciudad"]); ?></p>
                    <p>País: <?php echo htmlspecialchars($anuncio["NomPais"]); ?></p>
                    <p>Precio: <?php echo number_format($anuncio["Precio"], 0, ',', '.'); ?> €</p>
                    <a href="detalle_anuncio.php?id=<?php echo $anuncio["IdAnuncio"]; ?>">Ver detalle</a>
                </section>
<?php
            }
        }

        echo '</section>';
    }
}
?>