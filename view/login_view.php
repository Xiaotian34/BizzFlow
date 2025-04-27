<?php
require_once("view/menu_view.php");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesion</title>
</head>

<body>
    <h2>Iniciar sesión</h2>

    <?php if (!empty($message)): ?>
        <p style="color: red;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="correo">Correo electrónico:</label>
        <input type="email" name="correo" required><br><br>

        <label for="passwd">Contraseña:</label>
        <input type="password" name="passwd" required><br><br>

        <input type="submit" name="login" value="Entrar">
    </form>

    <p>¿No tienes cuenta? <a href="index.php?controlador=usuarios&action=registro">Regístrate aquí</a></p>
</body>

</html>