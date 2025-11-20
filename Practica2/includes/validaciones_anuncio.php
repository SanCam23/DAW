<?php

/**
 * Valida los datos obligatorios de un anuncio.
 * Devuelve un array de errores.
 */
function validarAnuncio($titulo, $texto, $precio, $ciudad, $pais, $tipo_anuncio, $tipo_vivienda)
{
    $errores = [];

    // Validaciones obligatorias
    if (empty(trim($titulo))) {
        $errores[] = "El título es obligatorio.";
    }
    if (empty(trim($texto))) {
        $errores[] = "La descripción (texto) es obligatoria.";
    }
    if (empty($precio) || !is_numeric($precio) || $precio < 0) {
        $errores[] = "El precio debe ser un número positivo.";
    }
    if (empty(trim($ciudad))) {
        $errores[] = "La ciudad es obligatoria.";
    }
    if (empty($pais) || !is_numeric($pais)) {
        $errores[] = "Debes seleccionar un país.";
    }
    if (empty($tipo_anuncio) || !is_numeric($tipo_anuncio)) {
        $errores[] = "Debes seleccionar un tipo de anuncio.";
    }
    if (empty($tipo_vivienda) || !is_numeric($tipo_vivienda)) {
        $errores[] = "Debes seleccionar un tipo de vivienda.";
    }

    return $errores;
}
