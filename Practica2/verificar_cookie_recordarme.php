<?php
// Archivo: verificar_cookie_recordarme.php

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
                
                // Actualizar última visita
                $_SESSION['ultima_visita'] = date('d/m/Y H:i:s');
                break;
            }
        }
    }
}
?>