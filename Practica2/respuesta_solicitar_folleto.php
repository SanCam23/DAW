<?php
session_start();
require_once __DIR__ . '/db.php';
require_once 'verificar_sesion.php';
require_once __DIR__ . '/includes/validaciones_folleto.php';

// Configuración del cálculo (Constantes)
const FOTOS_POR_PAGINA = 3; 
const PRECIO_ENVIO = 10;

// Tarifas
$tarifas = [
    "paginas" => ["p1a4" => 2.0, "p5a10" => 1.8, "p11ymas" => 1.6],
    "color" => ["bn" => 0, "color" => 0.5],
    "resol" => ["baja" => 0, "alta" => 0.2]
];

// Función de cálculo de coste
function calcularCoste($pags, $num_fotos, $modo_color, $modo_resol, $t) {
    $costePaginas = 0;
    
    if ($pags <= 4) {
        $costePaginas = $pags * $t["paginas"]["p1a4"];
    } elseif ($pags <= 10) {
        $costePaginas += 4 * $t["paginas"]["p1a4"];
        $costePaginas += ($pags - 4) * $t["paginas"]["p5a10"];
    } else {
        $costePaginas += 4 * $t["paginas"]["p1a4"];
        $costePaginas += 6 * $t["paginas"]["p5a10"];
        $costePaginas += ($pags - 10) * $t["paginas"]["p11ymas"];
    }

    $costeColor = ($modo_color === "color") ? $num_fotos * $t["color"]["color"] : 0;
    $costeResol = ($modo_resol === "alta") ? $num_fotos * $t["resol"]["alta"] : 0;

    return PRECIO_ENVIO + $costePaginas + $costeColor + $costeResol;
}

$errores = [];
$exito = false;
$coste_total = 0;
$datos_resumen = [];

// 1. Recoger datos del POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Saneamiento básico
    $datos = [
        'nombre' => trim($_POST['nombre'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'telefono' => trim($_POST['telefono'] ?? ''),
        'calle' => trim($_POST['calle'] ?? ''),
        'numero' => trim($_POST['numero'] ?? ''),
        'cp' => trim($_POST['cp'] ?? ''),
        'localidad' => trim($_POST['localidad'] ?? ''),
        'provincia' => trim($_POST['provincia'] ?? ''),
        'pais' => $_POST['pais'] ?? '',
        'anuncio' => $_POST['anuncio'] ?? '',
        'copias' => (int)($_POST['copias'] ?? 1),
        'resolucion' => (int)($_POST['resolucion'] ?? 150),
        'color' => $_POST['color'] ?? '#000000',
        'impresion_color' => $_POST['impresion_color'] ?? 'bn',
        'texto' => trim($_POST['texto'] ?? ''),
        'fecha' => $_POST['fecha'] ?? '',
        'mostrar_precio' => isset($_POST['mostrar_precio']) ? 1 : 0
    ];

    // 2. Validar datos formulario
    $errores = validarSolicitudFolleto($datos);

    // 3. Lógica de Negocio
    if (empty($errores)) {
        $db = conectarDB();
        
        // A. Obtener datos reales del anuncio
        $id_anuncio = (int)$datos['anuncio'];
        $usuario_id = $_SESSION['usuario_id'];

        $sql_info = "SELECT Titulo FROM ANUNCIOS WHERE IdAnuncio = ? AND Usuario = ?";
        $stmt = $db->prepare($sql_info);
        $stmt->bind_param("ii", $id_anuncio, $usuario_id);
        $stmt->execute();
        $res_info = $stmt->get_result();
        
        if ($fila_anuncio = $res_info->fetch_assoc()) {
            $titulo_anuncio = $fila_anuncio['Titulo'];
            
            // Contar fotos reales
            $sql_count = "SELECT COUNT(*) as total FROM FOTOS WHERE Anuncio = ?";
            $stmt_count = $db->prepare($sql_count);
            $stmt_count->bind_param("i", $id_anuncio);
            $stmt_count->execute();
            $res_count = $stmt_count->get_result();
            $fila_count = $res_count->fetch_assoc();
            
            $num_fotos_reales = (int)$fila_count['total'];
            $stmt_count->close();

            // Calcular páginas reales (Mínimo 1 pág)
            $num_paginas_reales = ($num_fotos_reales > 0) ? ceil($num_fotos_reales / FOTOS_POR_PAGINA) : 1;

            // Calcular Coste
            $modo_resol = ($datos['resolucion'] > 300) ? "alta" : "baja";
            $coste_unitario = calcularCoste($num_paginas_reales, $num_fotos_reales, $datos['impresion_color'], $modo_resol, $tarifas);
            $coste_total = $coste_unitario * $datos['copias'];

            $datos_resumen = [
                'titulo' => $titulo_anuncio,
                'num_fotos' => $num_fotos_reales,
                'num_paginas' => $num_paginas_reales,
                'coste_unitario' => $coste_unitario
            ];

            // B. Insertar en BD
            $fecha_sql = null;
            if (preg_match("/^(\d{2})\/(\d{2})\/(\d{4})$/", $datos['fecha'], $partes)) {
                $fecha_sql = "{$partes[3]}-{$partes[2]}-{$partes[1]}";
            }

            $direccion_completa = "{$datos['calle']}, {$datos['numero']}, {$datos['cp']}, {$datos['localidad']}, {$datos['provincia']}, {$datos['pais']}";
            $es_color = ($datos['impresion_color'] === 'color') ? 1 : 0;

            $sql_insert = "INSERT INTO SOLICITUDES 
                           (Anuncio, Texto, Nombre, Email, Direccion, Telefono, Color, Copias, 
                            Resolucion, Fecha, IColor, IPrecio, Coste, FRegistro)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt_ins = $db->prepare($sql_insert);
            $stmt_ins->bind_param("issssssiisidd", 
                $id_anuncio, $datos['texto'], $datos['nombre'], $datos['email'], 
                $direccion_completa, $datos['telefono'], $datos['color'], $datos['copias'],
                $datos['resolucion'], $fecha_sql, $es_color, $datos['mostrar_precio'], $coste_total
            );

            if ($stmt_ins->execute()) {
                $exito = true;
            } else {
                $errores[] = "Error al guardar la solicitud: " . $stmt_ins->error;
            }
            $stmt_ins->close();

        } else {
            $errores[] = "El anuncio seleccionado no existe o no te pertenece.";
        }
        $stmt->close();
        $db->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Respuesta Solicitud - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/respuesta_solicitar_folleto.css">
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print">
</head>
<body>
    <?php require('cabecera.php'); ?>

    <main>
        <?php if ($exito): ?>
            <section class="confirmacion">
                <h2>¡Solicitud Registrada!</h2>
                <p>Gracias <strong><?php echo htmlspecialchars($datos['nombre']); ?></strong>, hemos procesado tu pedido correctamente.</p>
                
                <hr class="separador">
                
                <h3>Detalles del Coste</h3>
                <div class="resumen-costes">
                    <ul>
                        <li><strong>Anuncio:</strong> <?php echo htmlspecialchars($datos_resumen['titulo']); ?></li>
                        <li><strong>Fotos encontradas:</strong> <?php echo $datos_resumen['num_fotos']; ?></li>
                        <li><strong>Páginas calculadas:</strong> <?php echo $datos_resumen['num_paginas']; ?> (a <?php echo FOTOS_POR_PAGINA; ?> fotos/pág)</li>
                        <li><strong>Coste unitario:</strong> <?php echo number_format($datos_resumen['coste_unitario'], 2); ?> €</li>
                        <li><strong>Copias:</strong> <?php echo $datos['copias']; ?></li>
                    </ul>
                </div>
                
                <p class="coste-total">
                    <strong>Coste Total: <?php echo number_format($coste_total, 2); ?> €</strong> 
                </p>

                <div class="acciones">
                    <a href="solicitar_folleto.php" class="btn btn-primario">Solicitar otro</a>
                    <a href="misanuncios.php" class="btn btn-secundario">Volver a mis anuncios</a>
                </div>
            </section>

        <?php else: ?>
            <section class="errores">
                <h2>Error en la solicitud</h2>
                <div class="contenedor-errores">
                    <ul class="lista-errores">
                        <?php foreach ($errores as $e): ?>
                            <li><?php echo htmlspecialchars($e); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="acciones">
                    <a href="solicitar_folleto.php" class="btn btn-secundario">Volver al formulario</a>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <?php require('pie.php'); ?>
</body>
</html>