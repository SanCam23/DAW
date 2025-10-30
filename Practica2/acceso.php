<?php
// NOTA IMPORTANTE: Se elimina session_start() y $_SESSION para cumplir con la práctica.
// Los errores se pasan a la página de destino (index.php o registro.php) mediante la URL.

$usuario = $_POST["usuario"] ?? "";
$clave = $_POST["password"] ?? "";

// Preparamos la URL base para la redirección de errores
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$index_page = 'index.php';
$registro_page = 'registro.php';

// 1. VALIDACIÓN de campos vacíos o solo espacios
// Se debe comprobar que el usuario ha escrito algo en ambos campos y se debe evitar que el usuario escriba únicamente espacios en blanco o tabuladores[cite: 136].
if (trim($usuario) === "" || trim($clave) === "") {
    // Redirección al formulario de acceso (index.php) con mensaje de error[cite: 140].
    $mensaje_error = urlencode("Error de validación: Debe rellenar ambos campos (usuario y contraseña) sin espacios en blanco.");
    $url_redireccion = "http://$host$uri/$index_page?error=$mensaje_error";

    header("Location: $url_redireccion");
    exit();
}

// 2. Comprobar existencia del fichero de usuarios
// Los datos deben estar almacenados en un fichero separado[cite: 138].
$fichero = "usuarios.txt"; // Asumimos formato: usuario:contraseña
if (!file_exists($fichero)) {
    // Error interno, redireccionar al formulario de acceso (index.php)[cite: 140].
    $mensaje_error = urlencode("Error interno: Fichero de credenciales no disponible.");
    $url_redireccion = "http://$host$uri/$index_page?error=$mensaje_error";

    header("Location: $url_redireccion");
    exit();
}

// 3. Leer y comprobar credenciales (máximo cuatro posibles usuarios)
// Los datos de los usuarios permitidos están en un fichero separado[cite: 138].
$usuarios_validos = file($fichero, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$acceso_concedido = false;

foreach ($usuarios_validos as $linea) {
    // Asumimos que el fichero tiene el formato "usuario:contraseña"
    list($u, $p) = explode(":", $linea, 2); 
    
    // Comparamos el usuario y la clave (ambos deben estar limpios de espacios para la verificación)
    if (trim($u) === $usuario && trim($p) === $clave) {
        $acceso_concedido = true;
        break;
    }
}

// 4. Redirecciones según el resultado (Redirección del lado del servidor)
if ($acceso_concedido) {
    // Si el usuario está registrado, redireccionar al menú de usuario registrado[cite: 139].
    header("Location: menu_usuario_registrado.php"); 
    exit();
} else {
    // Si el usuario NO está registrado, redireccionar al formulario de acceso (index.php) con mensaje de error[cite: 140].
    $mensaje_error = urlencode("Error de acceso: El usuario o la contraseña no son correctos.");
    $url_redireccion = "http://$host$uri/$index_page?error=$mensaje_error";

    header("Location: $url_redireccion");
    exit();
}
?>