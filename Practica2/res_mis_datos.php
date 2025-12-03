<?php
session_start();
require_once 'verificar_sesion.php';
require_once __DIR__ . '/db.php';

// Verificar que el usuario está autenticado
if (!isset($_SESSION['usuario_autenticado']) || !$_SESSION['usuario_autenticado']) {
    header("Location: index.php");
    exit();
}

// Obtener mensaje específico de la URL
$mensaje_especifico = $_GET['mensaje'] ?? 'Sus datos personales han sido modificados exitosamente.';

// Obtener datos actualizados del usuario
$db = conectarDB();
$usuario = null;

if ($db && isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];

    $sql_usuario = "SELECT u.*, p.NomPais, e.Nombre as NombreEstilo 
                    FROM USUARIOS u 
                    LEFT JOIN PAISES p ON u.Pais = p.IdPais 
                    LEFT JOIN ESTILOS e ON u.Estilo = e.IdEstilo 
                    WHERE u.IdUsuario = ?";

    $stmt = $db->prepare($sql_usuario);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
    }
    $stmt->close();
    $db->close();
}

// Si no se encontró el usuario
if ($usuario === null) {
    echo "Error: No se pudieron cargar los datos del usuario.";
    exit;
}

// Formatear fechas para mostrar
$fecha_nacimiento_formateada = $usuario['FNacimiento'] ? date("d/m/Y", strtotime($usuario['FNacimiento'])) : '';
$fecha_registro_formateada = $usuario['FRegistro'] ? date("d/m/Y", strtotime($usuario['FRegistro'])) : '';

// Mapear sexo a texto 
$sexo_texto = '';
switch ($usuario['Sexo']) {
    case 1:
        $sexo_texto = 'masculino';
        break;
    case 2:
        $sexo_texto = 'femenino';
        break;
    default:
        $sexo_texto = 'No especificado';
}

$zona = 'privada';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Confirmación de modificación de datos en VENTAPLUS">
    <meta name="keywords" content="usuario, perfil, datos actualizados, VENTAPLUS">
    <meta name="author" content="Santino Campessi Lojo">
    <meta name="author" content="Mario Laguna Contreras">
    <title>Datos Actualizados - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/res_mis_datos.css">
    <link rel="stylesheet" type="text/css" href="css/print_formulario.css" media="print">
</head>

<body>
    <?php require('cabecera.php'); ?>

    <main>
        <section class="confirmacion">
            <h2>Datos actualizados correctamente</h2>
            <p><?php echo htmlspecialchars($mensaje_especifico); ?></p>
        </section>

        <section class="datos-actualizados">
            <h3>Sus datos actualizados:</h3>
            
            <div class="foto-perfil">
                <img src="<?php echo !empty($usuario['Foto']) ? htmlspecialchars($usuario['Foto']) : 'img/sin_fto.webp'; ?>" alt="Foto de perfil">
            </div>

            <div class="datos-lista">
                <div class="dato-item">
                    <strong>Usuario:</strong>
                    <span><?php echo htmlspecialchars($usuario['NomUsuario']); ?></span>
                </div>
                
                <div class="dato-item">
                    <strong>Correo electrónico:</strong>
                    <span><?php echo htmlspecialchars($usuario['Email']); ?></span>
                </div>
                
                <div class="dato-item">
                    <strong>Sexo:</strong>
                    <span><?php echo $sexo_texto; ?></span>
                </div>
                
                <div class="dato-item">
                    <strong>Fecha de nacimiento:</strong>
                    <span><?php echo $fecha_nacimiento_formateada; ?></span>
                </div>
                
                <div class="dato-item">
                    <strong>Ciudad:</strong>
                    <span><?php echo $usuario['Ciudad'] ? htmlspecialchars($usuario['Ciudad']) : 'No especificada'; ?></span>
                </div>
                
                <div class="dato-item">
                    <strong>País:</strong>
                    <span><?php echo $usuario['NomPais'] ? htmlspecialchars($usuario['NomPais']) : 'No especificado'; ?></span>
                </div>
                
                <div class="dato-item">
                    <strong>Estilo preferido:</strong>
                    <span><?php echo $usuario['NombreEstilo'] ? htmlspecialchars($usuario['NombreEstilo']) : 'Normal'; ?></span>
                </div>
                
                <div class="dato-item">
                    <strong>Miembro desde:</strong>
                    <span><?php echo $fecha_registro_formateada; ?></span>
                </div>
            </div>
        </section>

        <section class="acciones">
            <a href="mis_datos.php" class="btn">Modificar más datos</a>
            <a href="menu_usuario_registrado.php" class="btn volver">Volver al menú principal</a>
        </section>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>