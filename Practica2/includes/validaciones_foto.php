<?php

/**
 * Valida los datos de una foto según las reglas estrictas de la Práctica 10.
 * * @param string $titulo Título de la foto.
 * @param string $alt Texto alternativo de la foto.
 * @return array Lista de errores encontrados.
 */
function validarDatosFoto($titulo, $alt)
{
    $errores = [];

    // 1. El título de una foto es obligatorio
    if (empty(trim($titulo))) {
        $errores[] = "El título de la foto es obligatorio.";
    }

    // 2. Validaciones del Texto Alternativo
    $alt = trim($alt);
    if (empty($alt)) {
        $errores[] = "El texto alternativo es obligatorio.";
    } else {
        // Longitud mínima de 10 caracteres
        if (mb_strlen($alt) < 10) {
            $errores[] = "El texto alternativo debe tener una longitud mínima de 10 caracteres.";
        }

        // No empezar por palabras redundantes (insensible a mayúsculas/minúsculas)
        // Comprobamos "foto" o "imagen" al principio
        if (preg_match('/^(foto|imagen)/i', $alt)) {
            $errores[] = "El texto alternativo no debe empezar por 'foto' o 'imagen', ya que es redundante.";
        }
    }

    return $errores;
}
