<?php
if (isset($_COOKIE['recordarme_token']) && !isset($_SESSION['usuario_autenticado'])) {
    $token = $_COOKIE['recordarme_token'];
    
    // Verificar si existe el archivo de tokens
    if (file_exists('tokens.txt')) {
        $tokens = file('tokens.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($tokens as $linea) {
            list($usuario_id, $token_guardado, $expiracion_guardada) = explode(':', $linea);
            
            // Verificar token y que no haya expirado
            if ($token_guardado === $token && time() < (int)$expiracion_guardada) {
                
                // Buscar usuario por ID (no por nombre)
                require_once __DIR__ . '/db.php';
                $db = conectarDB();
                
                if ($db) {
                    $sql = "SELECT u.IdUsuario, u.NomUsuario, u.Email, e.Fichero 
                            FROM USUARIOS u 
                            INNER JOIN ESTILOS e ON u.Estilo = e.IdEstilo 
                            WHERE u.IdUsuario = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param("i", $usuario_id);
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    
                    if ($fila = $resultado->fetch_assoc()) {
                        // Token válido - iniciar sesión automáticamente
                        $_SESSION['usuario_autenticado'] = true;
                        $_SESSION['nombre_usuario'] = $fila['NomUsuario'];
                        $_SESSION['usuario_id'] = $fila['IdUsuario'];
                        $_SESSION['estilo_css'] = pathinfo($fila['Fichero'], PATHINFO_FILENAME);

                        // --- LÓGICA DE ÚLTIMA VISITA ---
                        date_default_timezone_set('Europe/Madrid');

                        // 1. LEER la visita anterior (de la cookie) y prepararla para mostrar
                        if (isset($_COOKIE['ultima_visita_timestamp'])) {
                            $ultima_visita_ts = (int) $_COOKIE['ultima_visita_timestamp'];
                            $ultima_visita_local = new DateTime("@$ultima_visita_ts");
                            $ultima_visita_local->setTimezone(new DateTimeZone(date_default_timezone_get()));
                            $_SESSION['visita_para_mostrar'] = $ultima_visita_local->format('d/m/Y H:i:s');
                        } else {
                            unset($_SESSION['visita_para_mostrar']);
                        }

                        // 2. GUARDAR la hora ACTUAL para la PRÓXIMA visita (en sesión y cookie)
                        $hora_actual = new DateTime('now', new DateTimeZone(date_default_timezone_get()));
                        $hora_actual_str = $hora_actual->format('Y-m-d H:i:s');
                        $hora_actual_ts = $hora_actual->getTimestamp();

                        $_SESSION['ultima_visita'] = $hora_actual_str;

                        // Actualizar la cookie de timestamp para la próxima vez
                        setcookie('ultima_visita_timestamp', $hora_actual_ts, [
                            'expires' => (int)$expiracion_guardada,
                            'path' => '/',
                            'httponly' => true
                        ]);
                    }
                    
                    $stmt->close();
                    $db->close();
                }
                break;
            }
        }
    }
}
?>