<?php
require_once("view/menu_view.php");
if (isset($_SESSION["correo"])) {
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestionar Imagenes</title>
    </head>

    <body>
        <div id="nuevo">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="container">
                    <label for="titulo">Titulo:</label><br>
                    <input type="text" id="titulo" name="titulo" placeholder="titulo..." required>
                    <br><br>
                    <label for="descripcion">Descripcion:</label><br>
                    <textarea name="descripcion" id="descripcion" rows="4" cols="50" required></textarea>
                    <br><br>
                    <label for="imagen">Seleccione el archivo:</label>
                    <input type="file" id="imagen" name="imagen" required>
                    <br><br>
                    <input type="submit" name="insertar" value="insertar">
                </div>
            </form>
        </div>

    <div id="contenido"></div>
    <?php
    if (isset($array_imagenes) && count($array_imagenes) > 0) {
        echo "<table class='tabla'><tr><th>Título</th><th>Autor</th><th>Fecha</th><th>Descripción</th><th></th><th></th></tr>";

        foreach ($array_imagenes as $registro) {
            if (is_array($registro)) {
                echo "<tr>";
                echo "<td>" . $registro['titulo'] . "</td>";
                echo "<td>" . $registro['autor'] . "</td>";
                echo "<td>" . $registro['fecha_subida'] . "</td>";
                echo "<td>" . $registro['descripcion'] . "</td>";
                echo '<td>
                        <form action="" method="post">
                            <input type="hidden" name="id" value="' . $registro["id"] . '">
                            <input type="submit" name="borrar" value="Eliminar">
                        </form>
                    </td>';
                echo '<td>
                        <input type="hidden" name="id" value="' . $registro["id"] . '">
                        <input type="hidden" id="titulo'.$registro["id"].'" value="'.$registro["titulo"].'">
                        <input type="hidden" id="descripcion'.$registro["id"].'" value="'.$registro["descripcion"].'">
                        <input type="hidden" id="imagen'.$registro["id"].'" value="'.$registro["nombre_fichero"].'">
                        <input type="submit" id="modificar" value="Modificar" onclick=modificarImagen(`'.$registro["id"].'`)>
                    </td>';
                echo "</tr>";
            }
        }
        echo "</table>";
    } else {
        echo "<p>No hay imágenes registradas.</p>";
    }
}
    ?>

    </body>

    </html>