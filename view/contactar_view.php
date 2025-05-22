<?php
require_once("view/menu_view.php");

?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/stylesContact.css">
        <title>Contactar</title>
    </head>

    <body>
        <form action="" method="POST">
            <div class="w3-main w3-content w3-padding" style="max-width:1200px;margin-top:100px">
                <div class="w3-row-padding w3-padding-16 w3-center">
                    <h2>Contactar</h2>

                    <label for="correo">Correo:</label><br>
                    <input type="email" id="correo" name="correo" placeholder="Correo..." required>
                    <br><br>
                    <label for="asunto">Asunto:</label><br>
                    <input type="text" placeholder="Asunto..." id="asunto" name="asunto" required>
                    <br><br>
                    <label for="mensaje">Mensaje:</label><br>
                    <textarea id="mensaje" placeholder="Mensaje..." name="mensaje" rows="5" cols="95" required></textarea>
                    <br><br>
                    <input type="submit" name="submit" value="Enviar">
                </div>
            </div>
        </form>
    <?php
    require_once("footer_view.php");
    ?>
    </body>

    </html>