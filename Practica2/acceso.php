<?php
// Archivo: acceso.php
session_start();

$usuario = $_POST["usuario"] ?? "";
$clave = $_POST["password"] ?? "";
$recordarme = isset($_POST["recordarme"]); // Checkbox "Recordarme"

$index_page = 'index.php';
$registro_page = 'registro.php';
$menu_page = 'menu_usuario_registrado.php';

// Validación de campos vacíos
if (trim($usuario) === "" || trim($clave) === "") {
    $_SESSION['error_login'] = "Error de validación: Debe rellenar ambos campos (usuario y contraseña).";
    header("Location: $index_page");
    exit();
}

// Verificar existencia del fichero de usuarios
$fichero = "usuarios.txt";
if (!file_exists($fichero)) {
    $_SESSION['error_login'] = "Error interno: Fichero de credenciales no disponible.";
    header("Location: $index_page");
    exit();
}

// Validar credenciales
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
    // Establecer sesión de usuario autenticado
    $_SESSION['usuario_autenticado'] = true;
    $_SESSION['nombre_usuario'] = $usuario;

    if ($usuario === 'santino') {
        $_SESSION['estilo_css'] = 'contraste_alto';
    } elseif ($usuario === 'mario') {
        $_SESSION['estilo_css'] = 'letra_grande';
    } else {
        // Estilo por defecto para otros usuarios
        $_SESSION['estilo_css'] = 'normal';
    }

    // Lógica para "Recordarme" con tokens seguros (C1)
    if ($recordarme) {
        // Generar token seguro
        $token = bin2hex(random_bytes(32));
        $expiracion = time() + (90 * 24 * 60 * 60); // 90 días

        // Crear cookie con token (NO usuario/contraseña)
        setcookie('recordarme_token', $token, [
            'expires' => $expiracion,
            'path' => '/',
            'secure' => false,    // Cambiar a true en producción con HTTPS
            'httponly' => true,   // No accesible por JavaScript
            'samesite' => 'Lax'
        ]);

        // Guardar asociación token-usuario en servidor
        $token_data = $usuario . ':' . $token . ':' . $expiracion . "\n";
        file_put_contents('tokens.txt', $token_data, FILE_APPEND | LOCK_EX);

        // También guardar timestamp de última visita para la cookie
        setcookie('ultima_visita_timestamp', time(), [
            'expires' => $expiracion,
            'path' => '/',
            'httponly' => true
        ]);
    }

    // Guardar fecha de última visita en sesión (C2)
    $_SESSION['ultima_visita'] = date('d/m/Y H:i:s');

    // Redirigir a página destino o menú principal
    if (isset($_SESSION['pagina_destino'])) {
        $destino = $_SESSION['pagina_destino'];
        unset($_SESSION['pagina_destino']);
        header("Location: $destino");
    } else {
        header("Location: $menu_page");
    }
    exit();
} elseif ($usuario_encontrado) {
    $_SESSION['error_login'] = "Error de acceso: La contraseña es incorrecta.";
    header("Location: $index_page");
    exit();
} else {
    $_SESSION['error_registro'] = "El usuario introducido no está registrado. Por favor, complete el formulario para crear una nueva cuenta.";
    header("Location: $registro_page");
    exit();
}
