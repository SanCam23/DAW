<?php
session_start();
require_once __DIR__ . '/validaciones.php';
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
$estilo = $_POST["estilo"] ?? 1; // Estilo por defecto

$errores = [];

// Preparar datos para validación
$datos_formulario = [
    'usuario' => $usuario,
    'password' => $password,
    'confirm_password' => $confirm_password,
    'email' => $email,
    'sexo' => $sexo,
    'fecha_nacimiento' => $fecha_nacimiento,
    'ciudad' => $ciudad
];

// Validar todos los campos usando nuestro fichero de validaciones
$resultado_validacion = validarFormularioUsuario($datos_formulario, false);

if ($resultado_validacion !== true) {
    // Hay errores de validación
    $errores = $resultado_validacion;
} else {
    // Validación exitosa, proceder con inserción en BD
    
    // Conectar a la base de datos
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
        
        // Si no hay errores, insertar el nuevo usuario
        if (empty($errores)) {
            // Convertir fecha de dd/mm/yyyy a yyyy-mm-dd para MySQL
            $fecha_mysql = DateTime::createFromFormat('d/m/Y', $fecha_nacimiento)->format('Y-m-d');
            
            // Hash de la contraseña
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Preparar la inserción
            $sql_insert = "INSERT INTO USUARIOS (NomUsuario, Clave, Email, Sexo, FNacimiento, Ciudad, Pais, Estilo, FRegistro) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt_insert = $db->prepare($sql_insert);
            
            if ($stmt_insert) {
                // Asignar valores por defecto si están vacíos
                $ciudad = empty($ciudad) ? null : $ciudad;
                $pais = empty($pais) ? null : $pais;
                
                $stmt_insert->bind_param("sssisiii", 
                    $usuario, 
                    $password_hash, 
                    $email, 
                    $sexo, 
                    $fecha_mysql, 
                    $ciudad, 
                    $pais, 
                    $estilo
                );
                
                if ($stmt_insert->execute()) {
                    // Registro exitoso - obtener el ID del nuevo usuario
                    $usuario_id = $stmt_insert->insert_id;
                    
                    // Obtener el estilo del usuario para la sesión
                    $sql_estilo = "SELECT e.Fichero FROM USUARIOS u 
                                INNER JOIN ESTILOS e ON u.Estilo = e.IdEstilo 
                                WHERE u.IdUsuario = ?";
                    $stmt_estilo = $db->prepare($sql_estilo);
                    $stmt_estilo->bind_param("i", $usuario_id);
                    $stmt_estilo->execute();
                    $resultado_estilo = $stmt_estilo->get_result();
                    
                    $fichero_estilo = 'general'; // Valor por defecto
                    
                    if ($fila_estilo = $resultado_estilo->fetch_assoc()) {
                        $fichero_estilo = pathinfo($fila_estilo['Fichero'], PATHINFO_FILENAME);
                    }
                    $stmt_estilo->close();
                    
                    // INICIAR SESIÓN AUTOMÁTICAMENTE (igual que en acceso.php)
                    $_SESSION['usuario_autenticado'] = true;
                    $_SESSION['nombre_usuario'] = $usuario;
                    $_SESSION['usuario_id'] = $usuario_id;
                    $_SESSION['estilo_css'] = $fichero_estilo;
                    
                    // Configurar última visita (igual que en acceso.php)
                    unset($_SESSION['visita_para_mostrar']);
                    date_default_timezone_set('Europe/Madrid');
                    $hora_actual = new DateTime('now', new DateTimeZone(date_default_timezone_get()));
                    $hora_actual_str = $hora_actual->format('Y-m-d H:i:s');
                    $_SESSION['ultima_visita'] = $hora_actual_str;
                    
                    // No establecemos cookies de "Recordarme" en registro automático
                    // El usuario puede activarlo manualmente en el futuro si quiere
                    
                    $stmt_insert->close();
                    $db->close();
                    
                    // Redirigir al menú de usuario registrado
                    header("Location: menu_usuario_registrado.php");
                    exit();
                }else {
                    $errores[] = "Error al insertar el usuario en la base de datos: " . $stmt_insert->error;
                }
                
                $stmt_insert->close();
            } else {
                $errores[] = "Error al preparar la consulta de inserción.";
            }
        }
        
        $db->close();
    }
}

// Si hay errores, redirigir de vuelta al formulario
if (!empty($errores)) {
    $_SESSION['error_registro'] = implode(" ", $errores);
    $_SESSION['datos_previos'] = $datos_formulario; // Para sticky form
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
                    switch($sexo) {
                        case '0': echo 'Masculino'; break;
                        case '1': echo 'Femenino'; break;
                        case '2': echo 'Otro'; break;
                        default: echo 'No especificado';
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
                        // Aquí podrías hacer una consulta para obtener el nombre del país
                        echo "País ID: " . htmlspecialchars($pais);
                    } else {
                        echo 'No especificado';
                    }
                    ?>
                </dd>
            </dl>
        </section>

        <div class="acciones">
            <a href="index.php" class="btn volver">Volver al inicio</a>
            <a href="index.php" class="btn login">Iniciar sesión</a>
        </div>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>