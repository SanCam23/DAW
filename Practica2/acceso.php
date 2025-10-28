<?php
// Recibir datos del formulario
$usuario = $_POST["usuario"] ?? "";
$clave = $_POST["password"] ?? "";

// Validación básica: campos no vacíos ni solo espacios
if (trim($usuario) === "" || trim($clave) === "") {
    header("Location: index.html?error=campos_vacios");
    exit();
}

// Ruta del fichero de usuarios
$fichero = "usuarios.txt";

if (!file_exists($fichero)) {
    header("Location: index.html?error=fichero_inaccesible");
    exit();
}

// Leer el fichero y buscar coincidencia
$usuarios_validos = file($fichero, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$acceso_concedido = false;

foreach ($usuarios_validos as $linea) {
    list($u, $p) = explode(":", $linea);
    if (trim($u) === $usuario && trim($p) === $clave) {
        $acceso_concedido = true;
        break;
    }
}

// Redirecciones según resultado
if ($acceso_concedido) {
    header("Location: menu_usuario_registrado.html");
    exit();
} else {
    header("Location: registro.html");
    exit();
}
?>
