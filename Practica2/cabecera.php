<?php
// NUEVO: Tarea S2 y S3 - Iniciar la sesión
// Esta debe ser la PRIMERA línea del fichero, antes de CUALQUIER HTML.
session_start();

// NUEVO: Tarea S3 - Persistencia de Estilo
// 1. Definimos los estilos disponibles.
$estilos_disponibles = [
    'normal' => 'css/general.css',
    'contraste_alto' => 'css/contraste_alto.css',
    'letra_grande' => 'css/letra_grande.css',
    'contraste_letra' => 'css/contraste_letra.css'
];

// 2. Obtenemos el estilo de la sesión (que la Persona 1 guardará).
// Por ahora, asignamos 'normal' si no existe.
$estilo_usuario = $_SESSION['estilo_css'] ?? 'normal';

// 3. Validamos que el estilo exista; si no, volvemos a 'normal'.
$fichero_css = $estilos_disponibles[$estilo_usuario] ?? $estilos_disponibles['normal'];
$titulo_css = $estilo_usuario; // El 'title' del link


// NUEVO: Tarea S2 - Saludo por Franja Horaria
$saludo = ""; // Variable para el saludo
// Comprobamos si la Persona 1 ya ha guardado el nombre de usuario en la sesión
if (isset($_SESSION['nombre_usuario'])) {
    $nombre_usuario = $_SESSION['nombre_usuario'];
    
    // 1. Establecer la zona horaria (recomendado por la práctica, aunque sea en servidor)
    // Puedes ajustarla a tu zona, ej: 'Europe/Madrid'
    date_default_timezone_set('Europe/Madrid');
    
    // 2. Obtener la hora actual del servidor en formato 24h (solo el número)
    $hora_actual = (int)date('H');

    // 3. Lógica de saludos según las franjas horarias [cite: 141-144]
    if ($hora_actual >= 6 && $hora_actual < 12) {
        $saludo = "Buenos días, $nombre_usuario";
    } elseif ($hora_actual >= 12 && $hora_actual < 16) {
        $saludo = "Hola, $nombre_usuario";
    } elseif ($hora_actual >= 16 && $hora_actual < 20) {
        $saludo = "Buenas tardes, $nombre_usuario";
    } else {
        // Cubre de 20:00 a 05:59
        $saludo = "Buenas noches, $nombre_usuario";
    }
}
?>
<header class="Cabecera">
    <section class="texto">
        <figure>
            <img src="logo.png" alt="Logo">
        </figure>

        <section class="titulo">
            <h1>VENTAPLUS</h1>
            <h3>¿Buscas tu próximo hogar? Empieza aquí.</h3>
            
            <?php
            // NUEVO: Tarea S2 - Mostramos el saludo si existe
            if ($saludo !== "") {
                // Usamos un 'p' para el saludo, puedes darle estilo si quieres
                echo "<p class='saludo-usuario'>$saludo</p>";
            }
            ?>
        </section>
    </section>

    <nav class="menu-escritorio">
        <ul>
            <?php if (isset($zona) && $zona === 'privada'): ?>
                <li><a href="index.php"><i class="icon-home"></i>Inicio</a></li>
                <li><a href="formulario.php"><i class="icon-search"></i>Buscar</a></li>
                <li><a href="menu_usuario_registrado.php"><i class="icon-user"></i>Mi Perfil</a></li>
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
            <?php if (isset($zona) && $zona === 'privada'): ?>
                <li><a href="index.php"><i class="icon-home"></i></a></li>
                <li><a href="formulario.php"><i class="icon-search"></i></a></li>
                <li><a href="menu_usuario_registrado.php"><i class="icon-user"></i></a></li>
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