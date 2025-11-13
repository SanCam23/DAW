<?php
session_start();
require_once 'verificar_sesion.php';
require_once __DIR__ . '/db.php';

// Obtener ID del anuncio desde la URL
$id_anuncio = $_GET['id'] ?? 0;
$id_anuncio = (int)$id_anuncio;

if ($id_anuncio <= 0) {
    echo "Anuncio no encontrado.";
    exit;
}

// Conectar a la BD y obtener datos
$db = conectarDB();
$anuncio = null;
$mensajes = [];
$total_mensajes = 0;

if ($db && isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    
    // Verificar que el anuncio pertenece al usuario y obtener info básica
    $sql_anuncio = "SELECT a.IdAnuncio, a.Titulo, a.TAnuncio, a.TVivienda, a.Ciudad, 
                           p.NomPais, ta.NomTAnuncio, tv.NomTVivienda
                    FROM ANUNCIOS a
                    LEFT JOIN PAISES p ON a.Pais = p.IdPais
                    LEFT JOIN TIPOSANUNCIOS ta ON a.TAnuncio = ta.IdTAnuncio
                    LEFT JOIN TIPOSVIVIENDAS tv ON a.TVivienda = tv.IdTVivienda
                    WHERE a.IdAnuncio = ? AND a.Usuario = ?";
    
    $stmt_anuncio = $db->prepare($sql_anuncio);
    $stmt_anuncio->bind_param("ii", $id_anuncio, $usuario_id);
    $stmt_anuncio->execute();
    $resultado_anuncio = $stmt_anuncio->get_result();
    
    if ($resultado_anuncio && $resultado_anuncio->num_rows > 0) {
        $anuncio = $resultado_anuncio->fetch_assoc();
    }
    $stmt_anuncio->close();
    
    // Obtener mensajes recibidos para este anuncio
    if ($anuncio) {
        $sql_mensajes = "SELECT m.*, u.NomUsuario as Remitente, tm.NomTMensaje as TipoMensaje
                         FROM MENSAJES m
                         LEFT JOIN USUARIOS u ON m.UsuOrigen = u.IdUsuario
                         LEFT JOIN TIPOSMENSAJES tm ON m.TMensaje = tm.IdTMensaje
                         WHERE m.Anuncio = ?
                         ORDER BY m.FRegistro DESC";
        
        $stmt_mensajes = $db->prepare($sql_mensajes);
        $stmt_mensajes->bind_param("i", $id_anuncio);
        $stmt_mensajes->execute();
        $resultado_mensajes = $stmt_mensajes->get_result();
        
        if ($resultado_mensajes) {
            $mensajes = $resultado_mensajes->fetch_all(MYSQLI_ASSOC);
            $total_mensajes = count($mensajes);
            $resultado_mensajes->close();
        }
        $stmt_mensajes->close();
    }
    
    $db->close();
}

// Si el anuncio no existe o no pertenece al usuario
if ($anuncio === null) {
    echo "Anuncio no encontrado o no tienes permisos para verlo.";
    exit;
}

$zona = 'privada';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Mensajes recibidos en el anuncio - VENTAPLUS.">
    <meta name="keywords" content="mensajes, anuncio, contacto, VENTAPLUS">
    <meta name="author" content="Santino Campessi Lojo">
    <meta name="author" content="Mario Laguna Contreras">
    <title>Mensajes del anuncio - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/mensajes_anuncio.css">
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print">
</head>

<body>
    <?php require('cabecera.php'); ?>

    <main>
        <h2>Mensajes recibidos</h2>
        
        <!-- Información básica del anuncio -->
        <section class="info-anuncio">
            <h3>Información del anuncio</h3>
            <p><strong>Título:</strong> <?php echo $anuncio['Titulo']; ?></p>
            <p><strong>Tipo:</strong> <?php echo $anuncio['NomTAnuncio']; ?> - <?php echo $anuncio['NomTVivienda']; ?></p>
            <p><strong>Ubicación:</strong> <?php echo $anuncio['Ciudad']; ?> (<?php echo $anuncio['NomPais']; ?>)</p>
            <p><strong>Total de mensajes recibidos:</strong> <?php echo $total_mensajes; ?></p>
        </section>

        <!-- Listado de mensajes -->
        <section class="lista-mensajes">
            <h3>Mensajes recibidos (<?php echo $total_mensajes; ?>)</h3>
            
            <?php if ($total_mensajes > 0): ?>
                <div class="mensajes-container">
                    <?php foreach ($mensajes as $mensaje): ?>
                        <article class="mensaje-item">
                            <div class="mensaje-header">
                                <h4><?php echo $mensaje['TipoMensaje']; ?></h4>
                                <span class="fecha"><?php echo date("d/m/Y H:i", strtotime($mensaje['FRegistro'])); ?></span>
                            </div>
                            
                            <div class="mensaje-remitente">
                                <strong>De:</strong> 
                                <?php if (!empty($mensaje['Remitente'])): ?>
                                    <a href="perfil_usuario.php?id=<?php echo $mensaje['UsuOrigen']; ?>">
                                        <?php echo $mensaje['Remitente']; ?>
                                    </a>
                                <?php else: ?>
                                    Usuario no registrado
                                <?php endif; ?>
                            </div>
                            
                            <div class="mensaje-texto">
                                <p><?php echo nl2br($mensaje['Texto']); ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="sin-mensajes">Este anuncio no ha recibido mensajes todavía.</p>
            <?php endif; ?>
        </section>

        <p style="text-align: center; margin-top: 30px;">
            <a href="ver_anuncio.php?id=<?php echo $anuncio['IdAnuncio']; ?>" class="volver">
                ← Volver al anuncio
            </a>
        </p>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>