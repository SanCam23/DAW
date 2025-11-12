<?php
// Fichero auxiliar para la conexión a la base de datos 'pibd'

// Usamos __DIR__ para asegurarnos de que encuentra config.php
// en la MISMA CARPETA que este archivo (db.php)
require_once __DIR__ . '/config.php';

/**
 * Crea y devuelve una conexión a la base de datos utilizando mysqli.
 * @return mysqli|false
 */
function conectarDB()
{

    // Conectamos usando las constantes de config.php
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Comprobar si hubo un error de conexión
    if ($db->connect_errno) {
        echo "<p>Error al conectar con la base de datos: " . $db->connect_error . "</p>";
        return false;
    }

    // Establecer el juego de caracteres a utf8mb4
    if (!$db->set_charset(DB_CHARSET)) {
        echo "<p>Error al establecer el juego de caracteres: " . $db->error . "</p>";
        $db->close();
        return false;
    }

    return $db;
}
