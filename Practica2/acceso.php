<?php
$usuario = $_POST["usuario"] ?? "";
$clave = $_POST["password"] ?? "";
$index_page = 'index.php';
$registro_page = 'registro.php';
$menu_page = 'menu_usuario_registrado.php';

if (trim($usuario) === "" || trim($clave) === "") {
    $mensaje_error = urlencode("Error de validación: Debe rellenar ambos campos (usuario y contraseña).");
    $url_redireccion = "$index_page?error=$mensaje_error";    
    header("Location: $url_redireccion");
    exit();
}

$fichero = "usuarios.txt";
if (!file_exists($fichero)) {
    $mensaje_error = urlencode("Error interno: Fichero de credenciales no disponible.");
    $url_redireccion = "$index_page?error=$mensaje_error";    
    header("Location: $url_redireccion");
    exit();
}

$usuarios_validos = file($fichero, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$usuario_encontrado = false;
$acceso_concedido = false;

foreach ($usuarios_validos as $linea) {
    list($u, $p) = explode(":", $linea, 2); 
    
    if (trim($u) === $usuario) {
        $usuario_encontrado = true;
        
        if (trim($p) === $clave) {
            $acceso_concedido = true;
        }
        break;
    }
}

if ($acceso_concedido) {
    header("Location: $menu_page"); 
    exit();
} elseif ($usuario_encontrado) {
    $mensaje_error = urlencode("Error de acceso: La contraseña es incorrecta.");
    $url_redireccion = "$index_page?error=$mensaje_error";
    header("Location: $url_redireccion");
    exit();
} else {
    $url_redireccion = "$registro_page?motivo=no_registrado";
    header("Location: $url_redireccion");
    exit();
}
?>