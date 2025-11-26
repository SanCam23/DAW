<?php
/**
 * Obtiene los datos del anuncio y sus fotos asociadas.
 * @param mysqli $db Conexión a la base de datos.
 * @param int $id_anuncio ID del anuncio.
 * @return array|null Devuelve un array con ['anuncio' => ..., 'fotos' => ...] o null si no existe.
 */
function obtenerDatosGaleria($db, $id_anuncio) {
    // 1. Obtener información básica del anuncio
    $sql_anuncio = "SELECT Titulo, Precio FROM ANUNCIOS WHERE IdAnuncio = ?";
    $stmt = $db->prepare($sql_anuncio);
    $stmt->bind_param("i", $id_anuncio);
    $stmt->execute();
    $res_anuncio = $stmt->get_result();
    
    if ($res_anuncio->num_rows === 0) {
        $stmt->close();
        return null;
    }
    
    $datos_anuncio = $res_anuncio->fetch_assoc();
    $stmt->close();

    // 2. Obtener todas las fotos
    $fotos = [];
    $sql_fotos = "SELECT IdFoto, Foto, Alternativo, Titulo FROM FOTOS WHERE Anuncio = ?";
    $stmt_fotos = $db->prepare($sql_fotos);
    $stmt_fotos->bind_param("i", $id_anuncio);
    $stmt_fotos->execute();
    $res_fotos = $stmt_fotos->get_result();
    
    if ($res_fotos->num_rows > 0) {
        $fotos = $res_fotos->fetch_all(MYSQLI_ASSOC);
    }
    $stmt_fotos->close();

    return [
        'anuncio' => $datos_anuncio,
        'fotos' => $fotos,
        'total' => count($fotos)
    ];
}

/**
 * Genera el HTML de la galería de fotos.
 * @param array $fotos Array de fotos.
 * @param bool $modo_edicion Si es true, muestra el enlace de eliminar.
 */
function renderizarGaleria($fotos, $modo_edicion = false) {
    echo '<section class="galeria-ver-fotos">';
    
    if (empty($fotos)) {
        echo '<p style="text-align: center; width: 100%;">Este anuncio no tiene fotos en la galería.</p>';
    } else {
        foreach ($fotos as $foto) {
            echo '<figure>';
            echo '<img src="' . htmlspecialchars($foto["Foto"]) . '" alt="' . htmlspecialchars($foto["Alternativo"]) . '">';
            
            // Figcaption solo si hay título o estamos en modo edición
            if (!empty($foto['Titulo']) || $modo_edicion) {
                echo '<figcaption>';
                
                if (!empty($foto['Titulo'])) {
                    echo '<strong>' . htmlspecialchars($foto['Titulo']) . '</strong><br>';
                }

                if ($modo_edicion) {
                    echo '<a href="eliminar_foto.php?id=' . $foto['IdFoto'] . '" class="btn-eliminar-foto" style="color: #dc3545; font-size: 0.9em; text-decoration: underline;">[Eliminar]</a>';
                }
                
                echo '</figcaption>';
            }
            echo '</figure>';
        }
    }
    
    echo '</section>';
}
?>