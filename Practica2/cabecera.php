<?php
session_start();

// NUEVO: Determinar la zona automáticamente según la sesión
if (isset($_SESSION['usuario_autenticado']) && $_SESSION['usuario_autenticado'] === true) {
    $zona = 'privada';
} else {
    $zona = 'publica';
}

// NUEVO: Tarea S2 - Saludo por Franja Horaria
$saludo = "";
if (isset($_SESSION['nombre_usuario'])) {
    $nombre_usuario = $_SESSION['nombre_usuario'];
    
    date_default_timezone_set('Europe/Madrid');
    $hora_actual = (int)date('H');

    if ($hora_actual >= 6 && $hora_actual < 12) {
        $saludo = "Buenos días, $nombre_usuario";
    } elseif ($hora_actual >= 12 && $hora_actual < 16) {
        $saludo = "Hola, $nombre_usuario";
    } elseif ($hora_actual >= 16 && $hora_actual < 20) {
        $saludo = "Buenas tardes, $nombre_usuario";
    } else {
        $saludo = "Buenas noches, $nombre_usuario";
    }
}

// El resto del código de estilos se mantiene igual...
$estilos_disponibles = [
    'normal' => 'css/general.css',
    'contraste_alto' => 'css/contraste_alto.css',
    'letra_grande' => 'css/letra_grande.css',
    'contraste_letra' => 'css/contraste_letra.css'
];

$estilo_usuario = $_SESSION['estilo_css'] ?? 'normal';
$fichero_css = $estilos_disponibles[$estilo_usuario] ?? $estilos_disponibles['normal'];
$titulo_css = $estilo_usuario;
?>

<header class="Cabecera">
    <section class="texto">
        <figure>
            <img src="logo.png" alt="Logo">
        </figure>

        <section class="titulo">
            <h1>VENTAPLUS</h1>
            <h3>¿Buscas tu próximo hogar? Empieza aquí.</h3>
            
            <?php if ($saludo !== ""): ?>
                <p class='saludo-usuario'><?php echo $saludo; ?></p>
            <?php endif; ?>
        </section>
    </section>

    <nav class="menu-escritorio">
        <ul>
            <?php if ($zona === 'privada'): ?>
                <li><a href="index.php"><i class="icon-home"></i>Inicio</a></li>
                <li><a href="formulario.php"><i class="icon-search"></i>Buscar</a></li>
                <li><a href="menu_usuario_registrado.php"><i class="icon-user"></i>Mi Perfil</a></li>
                <!-- NUEVO: Opción Salir -->
                <li><a href="salir.php"><i class="icon-logout"></i>Salir</a></li>
            <?php else: ?>
                <li><a href="index.php"><i class="icon-home"></i>Inicio</a></li>
                <li><a href="formulario.php"><i class="icon-search"></i>Buscar</a></li>
                <li><a href="index_identificado.php"><i class="icon-user"></i>Iniciar Sesión</a></li>
                <li><a href="registro.php"><i class="icon-user-plus"></i>Registrarse</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <nav class="menu-movil">
         <ul>
            <?php if ($zona === 'privada'): ?>
                <li><a href="index.php"><i class="icon-home"></i></a></li>
                <li><a href="formulario.php"><i class="icon-search"></i></a></li>
                <li><a href="menu_usuario_registrado.php"><i class="icon-user"></i></a></li>
                <!-- NUEVO: Salir en móvil -->
                <li><a href="salir.php"><i class="icon-logout"></i></a></li>
            <?php else: ?>
                <li><a href="index.php"><i class="icon-home"></i></a></li>
                <li><a href="formulario.php"><i class="icon-search"></i></a></li>
                <li><a href="index_identificado.php"><i class="icon-user"></i></a></li>
                <li><a href="registro.php"><i class="icon-user-plus"></i></a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<link rel="stylesheet" href="<?php echo $fichero_css; ?>" title="<?php echo $titulo_css; ?>">