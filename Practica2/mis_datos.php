<?php
session_start();
require_once 'verificar_sesion.php';
require_once __DIR__ . '/db.php';

// Obtener datos del usuario desde la BD
$db = conectarDB();
$usuario = null;
$paises = [];

if ($db && isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    
    // Obtener datos del usuario
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
    
    // Cargar países para el desplegable
    $sql_paises = "SELECT IdPais, NomPais FROM PAISES ORDER BY NomPais ASC";
    $resultado_paises = $db->query($sql_paises);
    if ($resultado_paises) {
        $paises = $resultado_paises->fetch_all(MYSQLI_ASSOC);
        $resultado_paises->close();
    }
    
    $db->close();
}

// Si no se encontró el usuario
if ($usuario === null) {
    echo "Error: No se pudieron cargar los datos del usuario.";
    exit;
}

// Formatear fecha para mostrar
$fecha_nacimiento_formateada = $usuario['FNacimiento'] ? date("d/m/Y", strtotime($usuario['FNacimiento'])) : '';
$fecha_registro_formateada = $usuario['FRegistro'] ? date("d/m/Y", strtotime($usuario['FRegistro'])) : '';

// Mapear sexo a texto (igual que en registro.php)
$sexo_texto = '';
switch($usuario['Sexo']) {
    case 1: $sexo_texto = 'masculino'; break;
    case 2: $sexo_texto = 'femenino'; break;
    default: $sexo_texto = 'No especificado';
}

$zona = 'privada';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Mis datos de usuario en VENTAPLUS.">
    <meta name="keywords" content="usuario, perfil, datos personales, VENTAPLUS">
    <meta name="author" content="Santino Campessi Lojo">
    <meta name="author" content="Mario Laguna Contreras">
    <title>Mis datos - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/registro.css">
    <link rel="stylesheet" type="text/css" href="css/print_formulario.css" media="print">
</head>

<body>
    <?php require('cabecera.php'); ?>

    <main>
        <h2>Mis datos</h2>

        <!-- Información actual del usuario -->
        <section class="info-actual">
            <h3>Mis datos actuales</h3>
            
            <?php if (!empty($usuario['Foto'])): ?>
                <div class="foto-perfil">
                    <img src="<?php echo $usuario['Foto']; ?>" alt="Foto de perfil">
                </div>
            <?php endif; ?>
            
            <div class="datos-actuales">
                <p><strong>Usuario:</strong> <?php echo $usuario['NomUsuario']; ?></p>
                <p><strong>Email:</strong> <?php echo $usuario['Email']; ?></p>
                <p><strong>Sexo:</strong> <?php echo $sexo_texto; ?></p>
                <p><strong>Fecha nacimiento:</strong> <?php echo $fecha_nacimiento_formateada; ?></p>
                <p><strong>Ciudad:</strong> <?php echo $usuario['Ciudad'] ?: 'No especificada'; ?></p>
                <p><strong>País:</strong> <?php echo $usuario['NomPais'] ?: 'No especificado'; ?></p>
                <p><strong>Estilo:</strong> <?php echo $usuario['NombreEstilo'] ?: 'Normal'; ?></p>
                <p><strong>Miembro desde:</strong> <?php echo $fecha_registro_formateada; ?></p>
            </div>
        </section>

        <!-- Formulario deshabilitado (igual que registro.php) -->
        <form id="form-mis-datos" action="#" method="POST" enctype="multipart/form-data" style="opacity: 0.6; pointer-events: none;">
            <h3>Modificar mis datos</h3>
            
            <label for="usuario">Nombre de usuario:</label>
            <input type="text" id="usuario" name="usuario" value="<?php echo $usuario['NomUsuario']; ?>" disabled>
            
            <label for="email">Correo electrónico:</label>
            <input type="text" id="email" name="email" value="<?php echo $usuario['Email']; ?>" disabled>
            
            <label for="sexo">Sexo:</label>
            <select id="sexo" name="sexo" disabled>
                <option value="">Selecciona tu sexo</option>
                <option value="1" <?php echo $usuario['Sexo'] == 1 ? 'selected' : ''; ?>>Masculino</option>
                <option value="2" <?php echo $usuario['Sexo'] == 2 ? 'selected' : ''; ?>>Femenino</option>
            </select>
            
            <label for="fecha_nacimiento">Fecha de nacimiento:</label>
            <input type="text" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo $fecha_nacimiento_formateada; ?>" placeholder="dd/mm/yyyy" disabled>
            
            <label for="ciudad">Ciudad:</label>
            <input type="text" id="ciudad" name="ciudad" value="<?php echo $usuario['Ciudad']; ?>" disabled>
            
            <label for="pais">País:</label>
            <select id="pais" name="pais" disabled>
                <option value="">-- Seleccione un país --</option>
                <?php if (empty($paises)): ?>
                    <option value="" disabled>Error al cargar países</option>
                <?php else: ?>
                    <?php foreach ($paises as $pais_item): ?>
                        <option value="<?php echo $pais_item['IdPais']; ?>" 
                            <?php echo $usuario['Pais'] == $pais_item['IdPais'] ? 'selected' : ''; ?>>
                            <?php echo $pais_item['NomPais']; ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            
            <label for="foto">Foto de perfil:</label>
            <input type="file" id="foto" name="foto" accept="image/*" disabled>
            
            <button type="submit" disabled>Guardar cambios</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px;">
            <a href="menu_usuario_registrado.php" class="volver">Volver al menú de usuario</a>
        </p>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>