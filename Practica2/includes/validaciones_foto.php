<?php

// Validar datos de una foto
function validarDatosFoto($titulo, $alt)
{
    $errores = [];

    // Validar título obligatorio
    if (empty(trim($titulo))) {
        $errores[] = "El título de la foto es obligatorio.";
    }

    // Validar texto alternativo
    $alt = trim($alt);
    if (empty($alt)) {
        $errores[] = "El texto alternativo es obligatorio.";
    } else {
        // Validar longitud mínima de 10 caracteres
        if (mb_strlen($alt) < 10) {
            $errores[] = "El texto alternativo debe tener una longitud mínima de 10 caracteres.";
        }

        // Validar que no empiece por 'foto' o 'imagen'
        if (preg_match('/^(foto|imagen)/i', $alt)) {
            $errores[] = "El texto alternativo no debe empezar por 'foto' o 'imagen', ya que es redundante.";
        }
    }

    return $errores;
}
