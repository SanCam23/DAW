<?php
session_start();

$usuario = $_POST["usuario"] ?? "";
$clave = $_POST["password"] ?? "";

// ✅ Validación de campos vacíos o solo espacios
if (trim($usuario) === "" || trim($clave) === "") {
    $_SESSION["mensaje_error"] = "Debe rellenar ambos campos sin espacios en blanco.";
    header("Location: index.php");
    exit();
}

// ✅ Comprobar existencia del fichero
$fichero = "usuarios.txt";
if (!file_exists($fichero)) {
    $_SESSION["mensaje_error"] = "Error interno: fichero de usuarios no disponible.";
    header("Location: index.php");
    exit();
}

// ✅ Leer y comprobar credenciales
$usuarios_validos = file($fichero, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$acceso_concedido = false;

foreach ($usuarios_validos as $linea) {
    list($u, $p) = explode(":", $linea);
    if (trim($u) === $usuario && trim($p) === $clave) {
        $acceso_concedido = true;
        break;
    }
}

// ✅ Redirecciones según el resultado
if ($acceso_concedido) {
    header("Location: menu_usuario_registrado.html");
    exit();
} else {
    $_SESSION["mensaje_error"] = "El usuario no está registrado. Por favor, regístrese para acceder.";
    header("Location: registro.php");
    exit();
}
?>
