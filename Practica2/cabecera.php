<header class="Cabecera">
    <section class="texto">
        <figure>
            <img src="logo.png" alt="Logo">
        </figure>

        <section class="titulo">
            <h1>VENTAPLUS</h1>
            <h3>¿Buscas tu próximo hogar? Empieza aquí.</h3>
        </section>
    </section>

    <!-- Menú escritorio -->
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

    <!-- Menú móvil -->
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
