<?php
// Lista de usuarios y contraseñas de tu usuarios.txt
$usuarios = [
    'admin'   => '1234',
    'mario'   => 'abcd',
    'santino' => '5678',
    'test'    => '0000'
];

echo "<h1>Hashes de Contraseña Generados</h1>";
echo "<p>Copia y pega estas cadenas en tu archivo 'datos.sql' en los lugares indicados.</p>";
echo "<pre>";

foreach ($usuarios as $usuario => $pass) {
    // Genera el hash
    $hash = password_hash($pass, PASSWORD_DEFAULT);

    // Muestra el hash de una forma fácil de copiar
    echo "<b>Hash para '$usuario' (pass: $pass):</b><br>";
    echo htmlspecialchars($hash) . "<br><br>";
}

echo "</pre>";
echo "<p>¡IMPORTANTE! Borra este archivo ('generar_hash.php') después de copiar los hashes.</p>";

?>