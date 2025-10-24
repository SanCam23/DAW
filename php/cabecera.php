<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="<?=$css;?>">
    <style>
        <$php
            $r = rand(0,255);
            $g = rand(0,255);
            $b = rand(0,255);
            echo <<<hereDoc
            body {
                color: rgb(<?="$r, $g, $b"?>);
            }
            hereDoc;
        ?>
    </style>
</head>

<body>
    <header>
        <h1>Pisos e Inmuebles</h1>
        <h2><?=$titulo;?></h2>
    </header>
</body>