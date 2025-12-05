<?php
/**
 * Lógica para leer y seleccionar un consejo aleatorio del archivo JSON
 */

function obtenerConsejoAleatorio() {
    $rutaJson = __DIR__ . '/consejos.json';
    
    // Verificar que el archivo existe
    if (!file_exists($rutaJson)) {
        return [
            'error' => true,
            'mensaje' => 'Archivo de consejos no encontrado'
        ];
    }
    
    // Leer contenido del archivo JSON
    $contenido = file_get_contents($rutaJson);
    if ($contenido === false) {
        return [
            'error' => true,
            'mensaje' => 'Error al leer el archivo de consejos'
        ];
    }
    
    // Decodificar JSON
    $consejos = json_decode($contenido, true);
    
    // Verificar si la decodificación fue exitosa
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($consejos)) {
        return [
            'error' => true,
            'mensaje' => 'Formato JSON inválido en el archivo de consejos'
        ];
    }
    
    // Verificar que haya consejos
    if (empty($consejos)) {
        return [
            'error' => true,
            'mensaje' => 'No hay consejos disponibles'
        ];
    }
    
    // Seleccionar un consejo aleatorio
    $indiceAleatorio = mt_rand(0, count($consejos) - 1);
    $consejoSeleccionado = $consejos[$indiceAleatorio];
    
    // Asegurar que el consejo tenga la estructura esperada
    $consejoSeleccionado['error'] = false;
    
    return $consejoSeleccionado;
}

// Función para mostrar el consejo en HTML
function mostrarConsejoHTML($consejo) {
    if (isset($consejo['error']) && $consejo['error']) {
        return '<p class="mensaje-error">' . htmlspecialchars($consejo['mensaje']) . '</p>';
    }
    
    // Determinar clase CSS según importancia
    $claseImportancia = '';
    switch ($consejo['importancia']) {
        case 'Alta':
            $claseImportancia = 'importancia-alta';
            break;
        case 'Media':
            $claseImportancia = 'importancia-media';
            break;
        case 'Baja':
            $claseImportancia = 'importancia-baja';
            break;
    }
    
    return '
    <section class="consejo ' . $claseImportancia . '">
        <h3>💡 Consejo del día</h3>
        <p><strong>Categoría:</strong> ' . htmlspecialchars($consejo['categoria']) . '</p>
        <p><strong>Importancia:</strong> <span class="importancia">' . htmlspecialchars($consejo['importancia']) . '</span></p>
        <p class="descripcion-consejo">' . htmlspecialchars($consejo['descripcion']) . '</p>
    </section>';
}
?>