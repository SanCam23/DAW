<?php
function validarSolicitudFolleto($datos) {
    $errores = [];

    // 1. Datos Personales
    if (empty(trim($datos['nombre'] ?? ''))) {
        $errores[] = "El nombre es obligatorio.";
    }

    $email = trim($datos['email'] ?? '');
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido.";
    }

    // 2. Dirección Completa
    if (empty(trim($datos['calle'] ?? '')) || 
        empty(trim($datos['numero'] ?? '')) || 
        empty(trim($datos['cp'] ?? '')) || 
        empty(trim($datos['localidad'] ?? '')) || 
        empty(trim($datos['provincia'] ?? '')) ||
        empty(trim($datos['pais'] ?? ''))) {
        $errores[] = "La dirección debe estar completa (Calle, Número, CP, Localidad, Provincia y País).";
    }

    // 3. Datos Técnicos
    $copias = (int)($datos['copias'] ?? 0);
    if ($copias < 1 || $copias > 100) {
        $errores[] = "El número de copias debe estar entre 1 y 100.";
    }

    $resolucion = (int)($datos['resolucion'] ?? 0);
    if ($resolucion < 150 || $resolucion > 900) {
        $errores[] = "La resolución debe estar entre 150 y 900 DPI.";
    }

    $color_hex = $datos['color'] ?? '';
    // Validamos formato hexadecimal de color (#RRGGBB)
    if (!preg_match('/^#[a-f0-9]{6}$/i', $color_hex)) {
        $errores[] = "El color de portada no es válido.";
    }

    // 4. Anuncio seleccionado
    if (empty($datos['anuncio']) || !is_numeric($datos['anuncio'])) {
        $errores[] = "Debes seleccionar un anuncio válido.";
    }

    return $errores;
}
?>