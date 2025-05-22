<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stylesRegist.css">
    <script src="js/script.js" defer></script>
    <title>Registro</title>
</head>

<body>
    <div class="regist-form">
        <a href="index.php">
            <img src="img/logoBF.png" alt="Logo BizzFlow" style="display: block; margin: 0 auto; height: 80px;">
        </a>
        <h2>Registro de usuario</h2>
    
        <?php if (!empty($message)): ?>
            <p style="color: red;"><?php echo $message; ?></p>
        <?php endif; ?>
    
        <form id="registForm" method="POST" action="">
            <!-- Paso 1 -->
            <div class="step" id="step1">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" required><br><br>
                <label for="apellidos">Apellidos:</label>
                <input type="text" name="apellidos" id="apellidos" required><br><br>
                <label for="edad">Fecha de nacimiento:</label>
                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required>
                <input type="hidden" name="edad" id="edad">
                <br><br>
                <button type="button" onclick="nextStep(1)">Siguiente</button>
            </div>
            <!-- Paso 2 -->
            <div class="step" id="step2" style="display:none;">
                <label for="correo">Correo electrónico:</label>
                <input type="email" name="correo" id="correo" required><br><br>
                <button type="button" onclick="prevStep(2)">Anterior</button>
                <button type="button" onclick="nextStep(2)">Siguiente</button>
            </div>
            <!-- Paso 3 -->
            <div class="step" id="step3" style="display:none;">
                <label for="passwd">Contraseña:</label>
                <input type="password" name="passwd" id="passwd" required><br><br>
                <label for="confpasswd">Confirmar contraseña:</label>
                <input type="password" name="confpasswd" id="confpasswd" required><br><br>
                <button type="button" onclick="prevStep(3)">Anterior</button>
                <button type="button" onclick="nextStep(3)">Siguiente</button>
            </div>
            <!-- Paso 4 -->
            <div class="step" id="step4" style="display:none;">
                <label>Tipo de usuario:</label><br>
                <input type="radio" name="tipo" value="usuario" id="usuario" checked>
                <label for="usuario">Usuario</label>
                <input type="radio" name="tipo" value="empresa" id="empresa">
                <label for="empresa">Empresa</label><br><br>
                <button type="button" onclick="prevStep(4)">Anterior</button>
                <input type="submit" name="regist" value="Registrarse">
            </div>
        </form>
    
        <p>¿Ya tienes cuenta? <a href="index.php?controlador=usuarios&action=login">Iniciar Sesion</a></p>

        <!-- Botón de Google Sign-In -->
        <div id="g_id_onload"
            data-client_id="TU_CLIENT_ID"
            data-context="signup"
            data-ux_mode="popup"
            data-callback="handleCredentialResponse">
        </div>
        <div class="g_id_signin" data-type="standard"></div>
    </div>
</body>

</html>