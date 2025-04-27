<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stylesLogin.css">
    <title>Iniciar Sesion</title>
</head>

<body>
    <div class="login-form">
        <a href="index.php">
            <img src="img/logoBF.png" alt="Logo BizzFlow" style="display: block; margin: 0 auto; height: 80px;">
        </a>
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

        <!-- Botón de Google Sign-In -->
        <div id="g_id_onload"
            data-client_id="TU_CLIENT_ID"
            data-context="signin"
            data-ux_mode="popup"
            data-callback="handleCredentialResponse">
        </div>
        <div class="g_id_signin" data-type="standard"></div>
    </div>
</body>

</html>