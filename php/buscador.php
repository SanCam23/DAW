<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

</body>
<?php
$titulo = "Buscador";
include 'cabecera.php';
?>
<header>
    <h1>Pisos e Inmuebles</h1>
    <h2>Buscador</h2>
    <form action="resultado.php" method="get">
        <p>Buscar: <input type="text" name="ciudad"> <input type="submit"></p>

    </form>
</header>

<?php
include 'footer.php';
?>
</body>