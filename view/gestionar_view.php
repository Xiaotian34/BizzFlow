<?php
require_once("view/menu_view.php");
if (isset($_SESSION["correo"])) {
?>
    <body>
        <div id="nuevo" class="formulario">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="container">
                    <label for="titulo">Titulo:</label><br>
                    <input type="text" id="titulo" name="titulo" placeholder="titulo..." required>
                    <br><br>
                    <label for="descripcion">Descripcion:</label><br>
                    <textarea name="descripcion" id="descripcion" rows="4" cols="50" required></textarea>
                    <br><br>
                    <label for="documento">Seleccione el archivo:</label>
                    <input type="file" id="documento" name="documento" required>
                    <br><br>
                    <input type="submit" name="insertar" value="insertar">
                </div>
            </form>
        </div>

        <div id="contenido" class="formulario"></div>
    <?php
    if (isset($array_documentos) && count($array_documentos) > 0) {
        echo "<table class='tabla'><tr><th>Título</th><th>Tipo</th><th>Fecha de subida</th><th></th><th></th></tr>";

        foreach ($array_documentos as $registro) {
            if (is_array($registro)) {
                echo "<tr>";
                echo "<td>" . $registro['nombre_archivo'] . "</td>";
                echo "<td>" . $registro['tipo'] . "</td>";
                echo "<td>" . $registro['fecha_subida'] . "</td>";
                echo '<td>
                        <form action="" method="post">
                            <input type="hidden" name="id" value="' . $registro["id"] . '">
                            <input type="submit" name="borrar" value="Eliminar">
                        </form>
                    </td>';
                echo '<td>
                        <input type="hidden" name="id" value="' . $registro["id"] . '">
                        <input type="hidden" id="titulo'.$registro["id"].'" value="'.$registro["nombre_archivo"].'">
                        <input type="hidden" id="tipo'.$registro["id"].'" value="'.$registro["tipo"].'">
                        <input type="hidden" id="fecha_subida'.$registro["id"].'" value="'.$registro["fecha_subida"].'">
                        <input type="submit" id="modificar" value="Modificar" onclick=modificarImagen(`'.$registro["id"].'`)>
                    </td>';
                echo "</tr>";
            }
        }
        echo "</table>";
    } else {
        echo "<p>No hay documentos registrados.</p>";
    }
}
    ?>

    </body>

    </html>