<?php
function subirImagen($archivo, $directorio = 'img/') {
    // 1. Verificar si no se envió archivo (esto no es un error, simplemente devuelve null)
    if (!isset($archivo) || $archivo['error'] === UPLOAD_ERR_NO_FILE) {
        return ['ruta' => null, 'error' => null];
    }

    // 2. Verificar errores de subida del servidor
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        return ['ruta' => null, 'error' => "Error al subir el archivo. Código: " . $archivo['error']];
    }

    // 3. Validar extensión
    $nombre_original = basename($archivo['name']);
    $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
    $tipos_permitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($extension, $tipos_permitidos)) {
        return ['ruta' => null, 'error' => "Formato no válido. Use JPG, PNG, GIF o WEBP."];
    }

    // 4. Generar nombre único (time + uniqid evita colisiones)
    $nombre_unico = time() . "_" . uniqid() . "." . $extension;
    $ruta_final = $directorio . $nombre_unico;

    // 5. Mover el archivo
    if (move_uploaded_file($archivo['tmp_name'], $ruta_final)) {
        return ['ruta' => $ruta_final, 'error' => null];
    } else {
        return ['ruta' => null, 'error' => "Error al guardar la imagen en el servidor (permisos o ruta incorrecta)."];
    }
}
?>