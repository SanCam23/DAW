<?php
$titulo = "Resultado";
include 'cabecera.php';
?>
<p>Resultado de la busqueda</p>
<?php
echo $_POST["ciudad"];

echo $_GET["ciudad"]; //Sale en la direccion la informacion

?>
<?php
include 'footer.php';
?>