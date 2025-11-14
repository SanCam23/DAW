<?php
require_once __DIR__ . '/db.php';

// 1. Inicializar variables
$db = conectarDB();
$anuncios = [];
$errores = [];

// Arrays para construir la consulta dinámica y segura
$sql_base = "SELECT a.IdAnuncio, a.Titulo, a.FPrincipal, a.Alternativo, a.Ciudad, 
                    a.Precio, a.FRegistro, p.NomPais
             FROM ANUNCIOS a
             LEFT JOIN PAISES p ON a.Pais = p.IdPais";
$sql_where = [];
$sql_params = [];
$sql_types = "";

/* Arrays para "traducir" los IDs a Nombres en el resumen */
$mapa_paises = [];
$mapa_tipos_anuncio = [];
$mapa_tipos_vivienda = [];

if ($db) {

    /* Bucle 'while' manual */
    $res_paises = $db->query("SELECT IdPais, NomPais FROM PAISES");
    if ($res_paises) {
        $mapa_paises = [];
        while ($fila = $res_paises->fetch_assoc()) {
            $mapa_paises[$fila['IdPais']] = $fila['NomPais'];
        }
        $res_paises->close();
    }

    $res_anuncios = $db->query("SELECT IdTAnuncio, NomTAnuncio FROM TIPOSANUNCIOS");
    if ($res_anuncios) {
        $mapa_tipos_anuncio = [];
        while ($fila = $res_anuncios->fetch_assoc()) {
            $mapa_tipos_anuncio[$fila['IdTAnuncio']] = $fila['NomTAnuncio'];
        }
        $res_anuncios->close();
    }

    $res_viviendas = $db->query("SELECT IdTVivienda, NomTVivienda FROM TIPOSVIVIENDAS");
    if ($res_viviendas) {
        $mapa_tipos_vivienda = [];
        while ($fila = $res_viviendas->fetch_assoc()) {
            $mapa_tipos_vivienda[$fila['IdTVivienda']] = $fila['NomTVivienda'];
        }
        $res_viviendas->close();
    }


    // 2. Comprobar si es Búsqueda Rápida (desde index.php)
    $q = $_GET["q"] ?? ""; // 'q' es el name del input en index.php

    if (!empty($q)) {
        $sql_where[] = "(a.Titulo LIKE ? OR a.Texto LIKE ? OR a.Ciudad LIKE ?)";

        $like_q = "%" . $q . "%";
        $sql_params[] = $like_q;
        $sql_params[] = $like_q;
        $sql_params[] = $like_q;
        $sql_types .= "sss"; // Tres strings

    } else {
        /* Búsqueda Avanzada */
        $tipo_anuncio = $_GET["tipo_anuncio"] ?? "";
        $tipo_vivienda = $_GET["tipo_vivienda"] ?? "";
        $ciudad = $_GET["ciudad"] ?? "";
        $pais = $_GET["pais"] ?? "";
        $precio_min = $_GET["precio_min"] ?? "";
        $precio_max = $_GET["precio_max"] ?? "";
        $fecha = $_GET["fecha"] ?? "";

        if (!empty($tipo_anuncio)) {
            $sql_where[] = "a.TAnuncio = ?";
            $sql_params[] = $tipo_anuncio;
            $sql_types .= "i";
        }
        if (!empty($tipo_vivienda)) {
            $sql_where[] = "a.TVivienda = ?";
            $sql_params[] = $tipo_vivienda;
            $sql_types .= "i";
        }
        if (!empty($ciudad)) {
            // Búsqueda no sensible a mayúsculas
            $sql_where[] = "a.Ciudad LIKE ?";
            $sql_params[] = "%" . $ciudad . "%";
            $sql_types .= "s";
        }
        if (!empty($pais)) {
            $sql_where[] = "a.Pais = ?";
            $sql_params[] = $pais;
            $sql_types .= "i";
        }
        if (!empty($precio_min)) {
            $sql_where[] = "a.Precio >= ?";
            $sql_params[] = $precio_min;
            $sql_types .= "d";
        }
        if (!empty($precio_max)) {
            $sql_where[] = "a.Precio <= ?";
            $sql_params[] = $precio_max;
            $sql_types .= "d";
        }
        if (!empty($fecha)) {
            try {
                $fecha_obj = DateTime::createFromFormat('d/m/Y', $fecha);
                if ($fecha_obj) {
                    $fecha_sql = $fecha_obj->format('Y-m-d');
                    $sql_where[] = "a.FRegistro >= ?";
                    $sql_params[] = $fecha_sql;
                    $sql_types .= "s";
                } else {
                    $errores[] = "Formato de fecha incorrecto. Use dd/mm/yyyy.";
                }
            } catch (Exception $e) {
                $errores[] = "Formato de fecha inválido.";
            }
        }
    }

    // 3. Ensamblar y ejecutar la consulta (si no hay errores)
    if (empty($errores)) {
        $sql_final = $sql_base;

        if (!empty($sql_where)) {
            $sql_final .= " WHERE " . implode(" AND ", $sql_where);
        }

        $sql_final .= " ORDER BY a.FRegistro DESC";

        /* Usar sentencias preparadas para seguridad */
        $stmt = $db->prepare($sql_final);

        if ($stmt === false) {
            $errores[] = "Error al preparar la consulta: " . $db->error;
        } else {
            if (!empty($sql_params)) {
                $stmt->bind_param($sql_types, ...$sql_params);
            }

            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado) {
                $anuncios = $resultado->fetch_all(MYSQLI_ASSOC);
            }
            $stmt->close();
        }
    }
    $db->close();
} else {
    $errores[] = "No se pudo conectar a la base de datos.";
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/resultados.css">
    <link rel="stylesheet" type="text/css" href="css/print_resultados.css" media="print">
</head>

<body>
    <?php
    $zona = 'publica';
    require('cabecera.php');
    ?>

    <main>
        <h2>Resultados de la búsqueda</h2>

        <section id="datos-busqueda">
            <h3>Datos de la búsqueda</h3>

            <?php if (!empty($errores)): ?>
                <ul style='color: red;'>
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <ul>
                <?php
                /* Mostramos los nombres "traducidos" en lugar de los IDs */
                if (!empty($q)):
                    echo "<li><strong>Búsqueda Rápida:</strong> " . htmlspecialchars($q) . "</li>";
                else:
                    // Usamos las variables leídas al principio
                    if (!empty($_GET["tipo_anuncio"])) {
                        // Buscamos el ID en nuestro mapa de nombres
                        $nombre = $mapa_tipos_anuncio[$_GET["tipo_anuncio"]] ?? "ID Desconocido";
                        echo "<li><strong>Tipo de anuncio:</strong> " . htmlspecialchars($nombre) . "</li>";
                    }
                    if (!empty($_GET["tipo_vivienda"])) {
                        $nombre = $mapa_tipos_vivienda[$_GET["tipo_vivienda"]] ?? "ID Desconocido";
                        echo "<li><strong>Tipo de vivienda:</strong> " . htmlspecialchars($nombre) . "</li>";
                    }
                    if (!empty($_GET["ciudad"])) {
                        echo "<li><strong>Ciudad:</strong> " . htmlspecialchars($_GET["ciudad"]) . "</li>";
                    }
                    if (!empty($_GET["pais"])) {
                        $nombre = $mapa_paises[$_GET["pais"]] ?? "ID Desconocido";
                        echo "<li><strong>País:</strong> " . htmlspecialchars($nombre) . "</li>";
                    }
                    if (!empty($_GET["precio_min"])) {
                        echo "<li><strong>Precio Mínimo:</strong> " . htmlspecialchars($_GET["precio_min"]) . " €</li>";
                    }
                    if (!empty($_GET["precio_max"])) {
                        echo "<li><strong>Precio Máximo:</strong> " . htmlspecialchars($_GET["precio_max"]) . " €</li>";
                    }
                    if (!empty($_GET["fecha"])) {
                        echo "<li><strong>Fecha desde:</strong> " . htmlspecialchars($_GET["fecha"]) . "</li>";
                    }
                endif;
                ?>
            </ul>
        </section>

        <section id="resultados">
            <h3>Anuncios encontrados (<?php echo count($anuncios); ?>)</h3>

            <?php
            if (empty($anuncios) && empty($errores)):
                echo "<p>No se han encontrado anuncios que coincidan con su búsqueda.</p>";
            else:
                foreach ($anuncios as $anuncio):
                    $fecha_formateada = date("d/m/Y", strtotime($anuncio['FRegistro']));
            ?>
                    <article class="destacado">
                        <figure>
                            <img src="<?php echo htmlspecialchars($anuncio['FPrincipal']); ?>" alt="<?php echo htmlspecialchars($anuncio['Alternativo']); ?>">
                        </figure>
                        <h4><?php echo htmlspecialchars($anuncio['Titulo']); ?></h4>
                        <p>Fecha: <?php echo $fecha_formateada; ?></p>
                        <p>Ciudad: <?php echo htmlspecialchars($anuncio['Ciudad']); ?> (<?php echo htmlspecialchars($anuncio['NomPais']); ?>)</p>
                        <p>Precio: <?php echo number_format($anuncio['Precio'], 0, ',', '.'); ?> €</p>
                        <a href="detalle_anuncio.php?id=<?php echo $anuncio['IdAnuncio']; ?>">Ver detalle</a>
                    </article>
            <?php
                endforeach;
            endif;
            ?>

        </section>

        <a href="formulario.php">Volver al formulario de búsqueda</a>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>