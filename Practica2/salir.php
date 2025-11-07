<?php
session_start();

// 1. Destruir todas las variables de sesión
$_SESSION = array();

// 2. Borrar la cookie de sesión
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// 3. Borrar la cookie "Recordarme" si existe
if (isset($_COOKIE['recordarme_token'])) {
    setcookie('recordarme_token', '', time() - 42000, '/');
    if (file_exists('tokens.txt')) {
        file_put_contents('tokens.txt', '');
    }
}

// 4. Borrar cookies de últimos anuncios visitados
if (isset($_COOKIE['ultimos_visitados'])) {
    setcookie('ultimos_visitados', '', time() - 42000, '/');
}

// 5. Destruir la sesión
session_destroy();

// 6. Redirigir al index
header('Location: index.php');
exit();
?>