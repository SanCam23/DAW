<?php
session_start();
require_once __DIR__ . '/includes/validaciones.php';
// INCLUIMOS EL NUEVO GESTOR
require_once __DIR__ . '/includes/gestor_imagenes.php'; 
require_once __DIR__ . '/db.php';

// Recoger datos del formulario
$usuario = $_POST["usuario"] ?? "";
$password = $_POST["password"] ?? "";
$confirm_password = $_POST["confirm_password"] ?? "";
$email = $_POST["email"] ?? "";
$sexo = $_POST["sexo"] ?? "";
$fecha_nacimiento = $_POST["fecha_nacimiento"] ?? "";
$ciudad = $_POST["ciudad"] ?? "";
$pais = $_POST["pais"] ?? "";
$estilo = $_POST["estilo"] ?? 1;

$errores = [];

$datos_formulario = [
    'usuario' => $usuario,
    'password' => $password,
    'confirm_password' => $confirm_password,
    'email' => $email,
    'sexo' => $sexo,
    'fecha_nacimiento' => $fecha_nacimiento,
    'ciudad' => $ciudad
];

// Validar datos del formulario
$resultado_validacion = validarFormularioUsuario($datos_formulario, false);

if ($resultado_validacion !== true) {
    $errores = $resultado_validacion;
} else {
    // --- Lógica de subida de foto REUTILIZABLE ---
    $ruta_foto_bd = null;

    // Llamamos a la función genérica pasando el input 'foto'
    // Esta función maneja validación, nombre único y movimiento
    $resultado_subida = subirImagen($_FILES['foto'] ?? null);

    if ($resultado_subida['error']) {
        // Si la función devuelve error, lo añadimos
        $errores[] = $resultado_subida['error'];
    } elseif ($resultado_subida['ruta']) {
        // Si devuelve ruta, es que se subió bien
        $ruta_foto_bd = $resultado_subida['ruta'];
    }
    // ---------------------------------------------

    // Si no hay errores, conectar a BD
    if (empty($errores)) {
        $db = conectarDB();
        if (!$db) {
            $errores[] = "Error al conectar con la base de datos.";
        } else {
            // Verificar si el usuario ya existe
            $sql_check_user = "SELECT IdUsuario FROM USUARIOS WHERE NomUsuario = ? OR Email = ?";
            $stmt_check = $db->prepare($sql_check_user);
            $stmt_check->bind_param("ss", $usuario, $email);
            $stmt_check->execute();
            $resultado_check = $stmt_check->get_result();

            if ($resultado_check && $resultado_check->num_rows > 0) {
                $errores[] = "El nombre de usuario o email ya existen.";
            }
            $stmt_check->close();

            if (empty($errores)) {
                // Preparar datos para inserción
                $fecha_mysql = DateTime::createFromFormat('d/m/Y', $fecha_nacimiento)->format('Y-m-d');
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $sql_insert = "INSERT INTO USUARIOS (NomUsuario, Clave, Email, Sexo, FNacimiento, Ciudad, Pais, Estilo, Foto, FRegistro) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

                $stmt_insert = $db->prepare($sql_insert);

                if ($stmt_insert) {
                    $ciudad = empty($ciudad) ? null : $ciudad;
                    $pais = empty($pais) ? null : $pais;

                    $stmt_insert->bind_param(
                        "sssissiis",
                        $usuario,
                        $password_hash,
                        $email,
                        $sexo,
                        $fecha_mysql,
                        $ciudad,
                        $pais,
                        $estilo,
                        $ruta_foto_bd
                    );

                    if ($stmt_insert->execute()) {
                        $usuario_id = $stmt_insert->insert_id;

                        // Obtener estilo del usuario
                        $sql_estilo = "SELECT e.Fichero FROM USUARIOS u 
                                    INNER JOIN ESTILOS e ON u.Estilo = e.IdEstilo 
                                    WHERE u.IdUsuario = ?";
                        $stmt_estilo = $db->prepare($sql_estilo);
                        $stmt_estilo->bind_param("i", $usuario_id);
                        $stmt_estilo->execute();
                        $resultado_estilo = $stmt_estilo->get_result();

                        $fichero_estilo = 'general';

                        if ($fila_estilo = $resultado_estilo->fetch_assoc()) {
                            $fichero_estilo = pathinfo($fila_estilo['Fichero'], PATHINFO_FILENAME);
                        }
                        $stmt_estilo->close();

                        // Iniciar sesión automáticamente
                        $_SESSION['usuario_autenticado'] = true;
                        $_SESSION['nombre_usuario'] = $usuario;
                        $_SESSION['usuario_id'] = $usuario_id;
                        $_SESSION['estilo_css'] = $fichero_estilo;
                        $_SESSION['foto_usuario'] = $ruta_foto_bd;

                        // Configurar última visita
                        unset($_SESSION['visita_para_mostrar']);
                        date_default_timezone_set('Europe/Madrid');
                        $hora_actual = new DateTime('now', new DateTimeZone(date_default_timezone_get()));
                        $hora_actual_str = $hora_actual->format('Y-m-d H:i:s');
                        $_SESSION['ultima_visita'] = $hora_actual_str;
                    } else {
                        $errores[] = "Error al insertar el usuario en la base de datos: " . $stmt_insert->error;
                    }

                    if (isset($stmt_insert) && $stmt_insert) $stmt_insert->close();
                } else {
                    $errores[] = "Error al preparar la consulta de inserción.";
                }
            }
            $db->close();
        }
    }
}

// Si hay errores, redirigir de vuelta al formulario
if (!empty($errores)) {
    $_SESSION['error_registro'] = implode(" ", $errores);
    $_SESSION['datos_previos'] = $datos_formulario;
    header("Location: registro.php");
    exit();
}

// Si llegamos aquí, el registro fue exitoso
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Confirmación de registro en VENTAPLUS">
    <meta name="keywords" content="registro, confirmación, usuario, VENTAPLUS">
    <meta name="author" content="Santino Campessi Lojo">
    <meta name="author" content="Mario Laguna Contreras">
    <title>Registro Completado - VENTAPLUS</title>
    <link rel="stylesheet" href="css/general.css" title="Estilo normal">
    <link rel="stylesheet" href="css/res_registro.css">
    <link rel="alternate stylesheet" href="css/contraste_alto.css" title="Alto contraste">
    <link rel="alternate stylesheet" href="css/letra_grande.css" title="Letra Grande">
    <link rel="alternate stylesheet" href="css/contraste_letra.css" title="Letra Grande+Alto contraste">
    <link rel="stylesheet" type="text/css" href="css/print_formulario.css" media="print">
    <link rel="stylesheet" href="css/fontello.css">
    <link href="https://fonts.googleapis.com/css2?family=Leckerli+One&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
</head>

<body>
    <?php require('cabecera.php'); ?>

    <main>
        <h2>Registro completado correctamente</h2>
        <p>Gracias por registrarte, <strong><?php echo htmlspecialchars($usuario); ?></strong>.</p>

        <section id="foto-registro" style="text-align: center; margin: 20px 0;">
            <h3>Tu foto de perfil:</h3>
            <?php if ($ruta_foto_bd && file_exists($ruta_foto_bd)): ?>
                <figure>
                    <img src="<?php echo htmlspecialchars($ruta_foto_bd); ?>" alt="Foto de perfil de <?php echo htmlspecialchars($usuario); ?>"
                        style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%; border: 3px solid #1b9986;">
                </figure>
            <?php else: ?>
                <figure>
                    <img src="<?php echo !empty($ruta_foto_bd) ? htmlspecialchars($ruta_foto_bd) : 'img/sin_fto.webp'; ?>" alt="Foto de perfil"
                        style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%; opacity: 0.7;">
                    <figcaption>No has seleccionado foto (se usará el icono por defecto)</figcaption>
                </figure>
            <?php endif; ?>
        </section>
        <section id="datos-registro">
            <h3>Datos de tu cuenta:</h3>
            <dl>
                <dt>Usuario:</dt>
                <dd><?php echo htmlspecialchars($usuario); ?></dd>

                <dt>Correo electrónico:</dt>
                <dd><?php echo htmlspecialchars($email); ?></dd>

                <dt>Sexo:</dt>
                <dd>
                    <?php
                    switch ($sexo) {
                        case '1':
                            echo 'Masculino';
                            break;
                        case '2':
                            echo 'Femenino';
                            break;
                        default:
                            echo 'No especificado';
                    }
                    ?>
                </dd>

                <dt>Fecha de nacimiento:</dt>
                <dd><?php echo htmlspecialchars($fecha_nacimiento); ?></dd>

                <dt>Ciudad:</dt>
                <dd><?php echo !empty($ciudad) ? htmlspecialchars($ciudad) : 'No especificada'; ?></dd>

                <dt>País:</dt>
                <dd>
                    <?php
                    if (!empty($pais)) {
                        echo "Registrado (ID: " . htmlspecialchars($pais) . ")";
                    } else {
                        echo 'No especificado';
                    }
                    ?>
                </dd>
            </dl>
        </section>

        <div class="acciones">
            <a href="index.php" class="btn volver">Volver al inicio</a>
            <a href="menu_usuario_registrado.php" class="btn volver">Ir a mi menú privado</a>
        </div>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>