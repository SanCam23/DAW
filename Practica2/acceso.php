<?php
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

// Conexión a la base de datos usando mysqli
require_once __DIR__ . '/db.php';
$db = conectarDB();

if (!$db) {
    $_SESSION['error_login'] = "Error interno: No se pudo conectar con la base de datos.";
    header("Location: $index_page");
    exit();
}

// Consulta con sentencia preparada y JOIN con ESTILOS
$sql = "SELECT u.IdUsuario, u.NomUsuario, u.Clave, u.Email, e.Fichero 
        FROM USUARIOS u 
        INNER JOIN ESTILOS e ON u.Estilo = e.IdEstilo 
        WHERE u.NomUsuario = ?";
$stmt = $db->prepare($sql);

if (!$stmt) {
    $_SESSION['error_login'] = "Error interno en la consulta.";
    $db->close();
    header("Location: $index_page");
    exit();
}

$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

$usuario_encontrado = false;
$acceso_concedido = false;
$id_usuario = null;
$fichero_estilo = 'normal';

if ($fila = $resultado->fetch_assoc()) {
    $usuario_encontrado = true;
    
    // Verificar contraseña encriptada con password_verify()
    if (password_verify($clave, $fila['Clave'])) {
        $acceso_concedido = true;
        $id_usuario = $fila['IdUsuario'];
        
        // Obtener el nombre del estilo sin la extensión .css
        $fichero_estilo = pathinfo($fila['Fichero'], PATHINFO_FILENAME);
    }
}

$stmt->close();
$db->close();

if ($acceso_concedido) {
    // Establecer sesión de usuario autenticado
    $_SESSION['usuario_autenticado'] = true;
    $_SESSION['nombre_usuario'] = $usuario;
    $_SESSION['usuario_id'] = $id_usuario; // Para usar en otras páginas
    $_SESSION['estilo_css'] = $fichero_estilo;

    // 1. En login manual, NO mostramos visita anterior.
    unset($_SESSION['visita_para_mostrar']);

    // 2. Guardamos la hora ACTUAL para la PRÓXIMA visita
    date_default_timezone_set('Europe/Madrid');

    $hora_actual = new DateTime('now', new DateTimeZone(date_default_timezone_get()));
    $hora_actual_str = $hora_actual->format('Y-m-d H:i:s');
    $hora_actual_ts = $hora_actual->getTimestamp();

    $_SESSION['ultima_visita'] = $hora_actual_str;

    // Lógica para "Recordarme" con tokens seguros
    if ($recordarme) {
        // Generar token seguro
        $token = bin2hex(random_bytes(32));
        $expiracion = time() + (90 * 24 * 60 * 60); // 90 días

        // Crear cookie con token
        setcookie('recordarme_token', $token, [
            'expires' => $expiracion,
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        // Guardar asociación token-usuario en servidor
        $token_data = $usuario . ':' . $token . ':' . $expiracion . "\n";
        file_put_contents('tokens.txt', $token_data, FILE_APPEND | LOCK_EX);

        // También guardar timestamp de última visita para la cookie
        setcookie('ultima_visita_timestamp', $hora_actual_ts, [
            'expires' => $expiracion,
            'path' => '/',
            'httponly' => true
        ]);
        
        $expiracion_estilo = time() + (90 * 24 * 60 * 60);
        setcookie('estilo_css', $fichero_estilo, [
            'expires' => $expiracion_estilo,
            'path' => '/',
            'httponly' => false,
            'samesite' => 'Lax'
        ]);
    }

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
?>