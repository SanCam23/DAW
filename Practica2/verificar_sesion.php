<?php

// Función para verificar si el usuario está autenticado
function usuarioAutenticado() {
    return isset($_SESSION['usuario_autenticado']) && $_SESSION['usuario_autenticado'] === true;
}

// Si no está autenticado, redirigir al login
if (!usuarioAutenticado()) {
    // Guardar la página a la que intentaba acceder para redirigir después del login
    $_SESSION['pagina_destino'] = $_SERVER['REQUEST_URI'];
    
    // Redirigir al login
    header('Location: index.php');
    exit();
}
?>