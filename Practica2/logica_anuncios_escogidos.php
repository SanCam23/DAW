<?php
/**
 * Lógica para leer anuncios escogidos y seleccionar uno aleatorio
 * con verificación en la base de datos
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function leerAnunciosEscogidos() {
    $rutaTxt = __DIR__ . '/anuncios_escogidos.txt';
    $anuncios = [];
    
    // Verificar que el archivo existe
    if (!file_exists($rutaTxt)) {
        return $anuncios;
    }
    
    // Leer el archivo línea por línea
    $lineas = file($rutaTxt, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    if ($lineas === false) {
        return $anuncios;
    }
    
    foreach ($lineas as $linea) {
        // Saltar comentarios (líneas que empiezan con #)
        if (strpos(trim($linea), '#') === 0) {
            continue;
        }
        
        // Separar por | (formato: IdAnuncio|Persona|Comentario)
        $partes = explode('|', $linea);
        
        if (count($partes) >= 3) {
            $anuncios[] = [
                'IdAnuncio' => trim($partes[0]),
                'Persona' => trim($partes[1]),
                'Comentario' => trim($partes[2])
            ];
        }
    }
    
    return $anuncios;
}

function obtenerAnuncioEscogidoAleatorio($db) {
    // Leer anuncios del archivo
    $anunciosEscogidos = leerAnunciosEscogidos();
    
    if (empty($anunciosEscogidos)) {
        return [
            'error' => true,
            'mensaje' => 'No hay anuncios escogidos disponibles'
        ];
    }
    
    // Mezclar el array para selección aleatoria
    shuffle($anunciosEscogidos);
    
    $maxIntentos = count($anunciosEscogidos) * 2; // Límite de intentos
    $intentos = 0;
    
    // Intentar encontrar un anuncio válido (Opción 3 del PDF)
    while ($intentos < $maxIntentos && !empty($anunciosEscogidos)) {
        // Tomar el primer anuncio (ya está mezclado)
        $anuncioSeleccionado = array_shift($anunciosEscogidos);
        $idAnuncio = intval($anuncioSeleccionado['IdAnuncio']);
        
        // Verificar si el anuncio existe en la BD
        $sql = "SELECT a.*, p.NomPais 
                FROM ANUNCIOS a 
                LEFT JOIN PAISES p ON a.Pais = p.IdPais 
                WHERE a.IdAnuncio = ?";
        
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            continue; // Si hay error en la consulta, intentar con otro
        }
        
        $stmt->bind_param("i", $idAnuncio);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado && $resultado->num_rows > 0) {
            $anuncioBD = $resultado->fetch_assoc();
            $stmt->close();
            
            // Combinar datos del archivo con datos de la BD
            return array_merge($anuncioBD, $anuncioSeleccionado, ['error' => false]);
        }
        
        $stmt->close();
        $intentos++;
    }
    
    // Si llegamos aquí, no se encontró ningún anuncio válido
    return [
        'error' => true,
        'mensaje' => 'No se pudo encontrar un anuncio válido'
    ];
}

function mostrarAnuncioEscogidoHTML($anuncio) {
    if (isset($anuncio['error']) && $anuncio['error']) {
        return '<p class="mensaje-error">' . htmlspecialchars($anuncio['mensaje']) . '</p>';
    }
    
    // Formatear precio
    $precioFormateado = number_format($anuncio['Precio'], 0, ',', '.') . '€';
    
    return '
    <article class="anuncio-escogido">
        <figure>
            <img src="' . htmlspecialchars($anuncio['FPrincipal']) . '" 
                 alt="' . htmlspecialchars($anuncio['Alternativo']) . '">
        </figure>
        <section class="info-anuncio">
            <h4>' . htmlspecialchars($anuncio['Titulo']) . '</h4>
            <p><strong>Precio:</strong> ' . $precioFormateado . '</p>
            <p><strong>Ubicación:</strong> ' . htmlspecialchars($anuncio['Ciudad']) . 
               ' (' . htmlspecialchars($anuncio['NomPais']) . ')</p>
            <section class="expert-opinion">
                <p><strong>' . htmlspecialchars($anuncio['Persona']) . ' opina:</strong></p>
                <blockquote>"' . htmlspecialchars($anuncio['Comentario']) . '"</blockquote>
            </section>
            <a href="detalle_anuncio.php?id=' . $anuncio['IdAnuncio'] . '" class="btn-ver-detalle">
                Ver detalle completo
            </a>
        </section>
    </article>';
}
?>