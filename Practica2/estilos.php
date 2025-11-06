<?php
// Archivo: Practica2/estilos.php

// session_start() se quita de aquí. La página que lo incluya lo llamará.
if (session_status() === PHP_SESSION_NONE) {
    // Si ninguna página lo ha iniciado, lo iniciamos.
    // Pero es mejor que la página principal lo controle.
    session_start();
}

$estilo = $_SESSION['estilo_css'] ?? 'normal';

$mapa_estilos = [
    'normal' => 'css/general.css',
    'contraste_alto' => 'css/contraste_alto.css',
    'letra_grande' => 'css/letra_grande.css',
    'contraste_letra' => 'css/contraste_letra.css'
];

// 1️⃣ Cargar siempre el CSS general
echo '<link rel="stylesheet" href="css/general.css" title="Normal">' . PHP_EOL;

// 2️⃣ Si el estilo es distinto del normal, cargar el correspondiente
if ($estilo !== 'normal' && isset($mapa_estilos[$estilo])) {
    echo '<link rel="stylesheet" href="' . htmlspecialchars($mapa_estilos[$estilo]) . '" title="' . htmlspecialchars($estilo) . '">' . PHP_EOL;
}

// 3️⃣ Estilos globales adicionales (sin el print.css genérico)
echo '<link rel="stylesheet" href="css/fontello.css">' . PHP_EOL;
echo '<link href="https://fonts.googleapis.com/css2?family=Leckerli+One&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">' . PHP_EOL;
?>