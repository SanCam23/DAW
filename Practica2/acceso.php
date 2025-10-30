<?php
// NOTA IMPORTANTE: Sin session_start() ni $_SESSION.

$usuario = $_POST["usuario"] ?? "";
$clave = $_POST["password"] ?? "";

// Preparamos la URL base para la redirección
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$index_page = 'index.php';
$registro_page = 'registro.php';

// 1. VALIDACIÓN de campos vacíos o solo espacios
if (trim($usuario) === "" || trim($clave) === "") {
    // Error de validación: Redirección al formulario de acceso (index.php)
    $mensaje_error = urlencode("Error de validación: Debe rellenar ambos campos (usuario y contraseña).");
    $url_redireccion = "http://$host$uri/$index_page?error=$mensaje_error";
    header("Location: $url_redireccion");
    exit();
}

// 2. Comprobar existencia del fichero de usuarios
$fichero = "usuarios.txt"; // Asumimos formato: usuario:contraseña
if (!file_exists($fichero)) {
    // Error interno, redireccionar al formulario de acceso (index.php).
    $mensaje_error = urlencode("Error interno: Fichero de credenciales no disponible.");
    $url_redireccion = "http://$host$uri/$index_page?error=$mensaje_error";
    header("Location: $url_redireccion");
    exit();
}

// 3. Leer y comprobar credenciales
$usuarios_validos = file($fichero, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$usuario_encontrado = false;
$acceso_concedido = false;

foreach ($usuarios_validos as $linea) {
    list($u, $p) = explode(":", $linea, 2); 
    
    // Comprobamos si el usuario existe, independientemente de la contraseña
    if (trim($u) === $usuario) {
        $usuario_encontrado = true;
        
        // Si el usuario existe, comprobamos si la contraseña es correcta
        if (trim($p) === $clave) {
            $acceso_concedido = true;
        }
        break;
    }
}

// 4. Redirecciones según el resultado (Redirección del lado del servidor)
if ($acceso_concedido) {
    // A. ÉXITO: Usuario y contraseña correctos
    header("Location: menu_usuario_registrado.php"); 
    exit();
} elseif ($usuario_encontrado) {
    // B. CREDENCIALES INCORRECTAS: Usuario existe, pero clave incorrecta. Vuelve al login (index.php).
    $mensaje_error = urlencode("Error de acceso: La contraseña es incorrecta.");
    $url_redireccion = "http://$host$uri/$index_page?error=$mensaje_error";
    header("Location: $url_redireccion");
    exit();
} else {
    // C. USUARIO NO REGISTRADO: El usuario no fue encontrado en el fichero. Redirige a registro.php.
    // Usamos el parámetro 'motivo' para que registro.php muestre el mensaje específico.
    $url_redireccion = "http://$host$uri/$registro_page?motivo=no_registrado";
    header("Location: $url_redireccion");
    exit();
}
?>