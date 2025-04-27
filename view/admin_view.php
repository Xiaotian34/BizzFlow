<?php
require_once("view/menu_view.php");
if (isset($_SESSION["correo"])) {

?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestionar Usuarios</title>

    </head>

    <body>
        <div id="nuevo">
            <form action="" method="POST">
                <label for="nombre">Nombre:</label><br>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre..." required>
                <br><br>
    
                <label for="edad">Edad:</label><br>
                <input type="number" id="edad" name="edad" placeholder="00" required>
                <br><br>
    
                <label for="correo">Correo:</label><br>
                <input type="email" id="correo" name="correo" placeholder="Correo..." required>
                <br><br>
    
                <label for="passwd">Introduzca us contraseña:</label><br>
                <input type="password" id="passwd" name="passwd" placeholder="Contraseña..." required>
                <br><br>
    
                <input type="submit" id="regist" name="regist" value="Registrar">
            </form>

        </div>

        <div id="contenido"></div>

        <?php
        if (isset($usuarios) && count($usuarios) > 0) {
            echo "<table class='tabla'><tr><th>ID</th><th>Nombre de Usuario</th><th>Edad</th><th>Correo</th><th>Contraseña</th><th></th><th></th></tr>";

            foreach ($usuarios as $usuario) {
                echo "<tr>";
                echo "<td>" . $usuario['id'] . "</td>";
                echo "<td>" . $usuario['nombre'] . "</td>";
                echo "<td>" . $usuario['correo_electronico'] . "</td>";
                echo "<td>" . $usuario['contrasena_hash'] . "</td>";
                echo "<td>" . $usuario['tipo'] . "</td>";
                echo '<td>
                        <form action="" method="post">
                            <input type="hidden" name="nombre" value="' . $usuario['nombre'] . '">
                            <input type="submit" name="borrar" value="Eliminar">
                        </form>
                    </td>';

                echo '<td>
                        <input type="hidden" id="nombre'.$usuario["nombre"].'" value="'.$usuario["nombre"].'">
                        <input type="hidden" id="edad'.$usuario["nombre"].'" value="'.$usuario["correo_electronico"].'">
                        <input type="hidden" id="correo'.$usuario["nombre"].'" value="'.$usuario["contrasena_hash"].'">
                        <input type="hidden" id="passwrd'.$usuario["nombre"].'" value="'.$usuario["tipo"].'">
                        <input type="submit" id="modificar" value="Modificar" onclick=modificarUsuario(`'.$usuario["nombre"].'`)>
                    </td>';
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No hay usuarios registrados.</p>";
        }
        ?>
    </body>

    </html>
<?php

}
?>