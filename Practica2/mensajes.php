<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="VENTAPLUS: portal de anuncios de venta y alquiler de viviendas. Mensajes enviados y recibidos.">
    <meta name="keywords" content="mensajes, usuario, VENTAPLUS, comunicación">
    <meta name="author" content="Santino Campessi Lojo">
    <meta name="author" content="Mario Laguna Contreras">
    <title>Mis mensajes - VENTAPLUS</title>
    <?php require('estilos.php'); ?>
    <link rel="stylesheet" href="css/mensajes.css">
    <link rel="stylesheet" type="text/css" href="css/print_mensajes.css" media="print">
</head>

<body>

    <?php
        $zona = 'privada';
        include('cabecera.php');
        require_once 'verificar_sesion.php'; 
    ?>

    <main>
        <article>
            <h2>Mis mensajes</h2>

            <!-- Mensajes enviados -->
            <section>
                <h3>Mensajes enviados</h3>
                <article id="enviados">
                    <p><strong>Tipo de mensaje:</strong> Solicitar una cita</p>
                    <p><strong>Texto:</strong> Estoy interesado en visitar el piso de Madrid el sábado por la mañana.
                    </p>
                    <p><strong>Fecha:</strong> 15 de septiembre de 2025</p>
                    <p><strong>Receptor:</strong> Agencia Inmobiliaria Soluciones</p>
                </article>

                <article id="enviados">
                    <p><strong>Tipo de mensaje:</strong> Comunicar una oferta</p>
                    <p><strong>Texto:</strong> Ofrezco 340.000 € por el piso en Madrid Centro.</p>
                    <p><strong>Fecha:</strong> 20 de septiembre de 2025</p>
                    <p><strong>Receptor:</strong> Usuario: Carlos López</p>
                </article>
            </section>

            <!-- Mensajes recibidos -->
            <section>
                <h3>Mensajes recibidos</h3>
                <article id="recibidos">
                    <p><strong>Tipo de mensaje:</strong> Más información</p>
                    <p><strong>Texto:</strong> ¿El piso sigue disponible? ¿Se permiten mascotas?</p>
                    <p><strong>Fecha:</strong> 18 de septiembre de 2025</p>
                    <p><strong>Emisor:</strong> Usuario: Marta Sánchez</p>
                </article>

                <article id="recibidos">
                    <p><strong>Tipo de mensaje:</strong> Solicitar una cita</p>
                    <p><strong>Texto:</strong> Me gustaría agendar una visita el domingo por la tarde.</p>
                    <p><strong>Fecha:</strong> 22 de septiembre de 2025</p>
                    <p><strong>Emisor:</strong> Usuario: Pedro García</p>
                </article>
            </section>
        </article>
    </main>

    <?php require('pie.php'); ?>
</body>

</html>