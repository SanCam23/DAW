<?php
if (isset($_COOKIE['recordarme_token']) && !isset($_SESSION['usuario_autenticado'])) {
    $token = $_COOKIE['recordarme_token'];
    
    // Verificar si existe el archivo de tokens
    if (file_exists('tokens.txt')) {
        $tokens = file('tokens.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($tokens as $linea) {
            list($usuario_guardado, $token_guardado, $expiracion_guardada) = explode(':', $linea);
            
            // Verificar token y que no haya expirado
            if ($token_guardado === $token && time() < $expiracion_guardada) {
                // Token válido - iniciar sesión automáticamente
                $_SESSION['usuario_autenticado'] = true;
                $_SESSION['nombre_usuario'] = $usuario_guardado;
                
                // --- LÓGICA DE ÚLTIMA VISITA ---
                
                // 1. LEER la visita anterior (de la cookie) y prepararla para MOSTRAR AHORA
                if (isset($_COOKIE['ultima_visita_timestamp'])) {
                    $_SESSION['visita_para_mostrar'] = date('d/m/Y H:i:s', (int)$_COOKIE['ultima_visita_timestamp']);
                } else {
                    unset($_SESSION['visita_para_mostrar']); // No hay visita anterior registrada
                }

                // 2. GUARDAR la hora ACTUAL para la PRÓXIMA visita (en sesión y cookie)
                $hora_actual_str = date('d/m/Y H:i:s');
                $hora_actual_ts = time();
                
                $_SESSION['ultima_visita'] = $hora_actual_str; // Actualizar la sesión
                
                // Actualizar la cookie de timestamp para la próxima vez
                setcookie('ultima_visita_timestamp', $hora_actual_ts, [
                    'expires' => (int)$expiracion_guardada, // Reutiliza la expiración del token
                    'path' => '/',
                    'httponly' => true
                ]);

                break;
            }
        }
    }
}
?>