<?php
require_once("view/menu_view.php");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
</head>

<body>
    <h2>Registro de usuario</h2>

    <?php if (!empty($message)): ?>
        <p style="color: red;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="nombre">Nombre completo:</label>
        <input type="text" name="nombre" required><br><br>

        <label for="correo">Correo electrónico:</label>
        <input type="email" name="correo" required><br><br>

        <label for="passwd">Contraseña:</label>
        <input type="password" name="passwd" required><br><br>

        <label for="confpasswd">Confirmar contraseña:</label>
        <input type="password" name="confpasswd" required><br><br>

        <input type="submit" name="regist" value="Registrarse">
    </form>

    <p>¿Ya tienes cuenta? <a href="index.php?controlador=usuarios&action=login">Iniciar Sesion</a></p>
</body>

</html>