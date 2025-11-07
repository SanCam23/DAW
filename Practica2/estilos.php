<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$estilo = $_SESSION['estilo_css'] ?? 'normal';

$mapa_estilos = [
    'normal' => 'css/general.css',
    'contraste_alto' => 'css/contraste_alto.css', 
    'letra_grande' => 'css/letra_grande.css',
    'contraste_letra' => 'css/contraste_letra.css'
];

// Cargar SIEMPRE el CSS base PRIMERO
if (isset($mapa_estilos[$estilo])) {
    echo '<link rel="stylesheet" href="' . htmlspecialchars($mapa_estilos[$estilo]) . '" title="' . htmlspecialchars($estilo) . '">' . PHP_EOL;
} else {
    // Fallback al estilo normal si el estilo no existe
    echo '<link rel="stylesheet" href="css/general.css" title="Normal">' . PHP_EOL;
}

// Estilos globales adicionales
echo '<link rel="stylesheet" href="css/fontello.css">' . PHP_EOL;
echo '<link href="https://fonts.googleapis.com/css2?family=Leckerli+One&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">' . PHP_EOL;
?>