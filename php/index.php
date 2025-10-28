<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php
$css="estilos.css";
$titulo = "Pagina de inicio";
    include 'cabecera.php';

        $mensaje = "OK";

        $mensajes= [
            "Ok",
            "Fallo",
            "Error"
        ];

        $capitales = [
            "Portugal" => "Lisboa",
            "Francia" => "París",
            "Italia" => "Roma"
        ];

        sort($capitales);
        echo"<p>Hello World: ". $mensaje . "</p>";
        echo"<p>Hello World<: $mensaje[1] /p>";
        echo'<p>Hello World</p>';
        echo"<p>La capital de Francia es {$capitales["Francia"]}</p>";
        
        echo"<label for='capitales'>Capitales:</label>";
        echo"<select id='capitales' name='capitales'>";
        foreach($capitales as $pos => $capitales){
            echo"<option value='$pos'>$capitales</option>";
        }
        echo"</select>";

    include 'footer.php';  
    ?>
</body>

</html>