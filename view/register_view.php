<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stylesRegist.css">
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
                <label for="telefono">Número de teléfono:</label>
                <input type="tel" name="telefono" id="telefono" required placeholder="123456789" style="flex:1;">
                <button type="button" onclick="prevStep(3)">Anterior</button>
                <button type="button" onclick="nextStep(3)">Siguiente</button>
            </div>
            <!-- Paso 4 -->
            <div class="step" id="step4" style="display:none;">
                <label for="passwd">Contraseña:</label>
                <input type="password" name="passwd" id="passwd" required><br><br>
                <label for="confpasswd">Confirmar contraseña:</label>
                <input type="password" name="confpasswd" id="confpasswd" required><br><br>
                <button type="button" onclick="prevStep(4)">Anterior</button>
                <button type="button" onclick="nextStep(4)">Siguiente</button>
            </div>
            <!-- Paso 5 -->
            <div class="step" id="step5" style="display:none;">
                <label>Tipo de usuario:</label><br>
                <div class="user-type-cards">
                    <label class="user-type-card" id="card-usuario">
                        <input type="radio" name="tipo" value="usuario" id="usuario" checked style="display:none;">
                        <div class="card-content">
                            <h4>Usuario</h4>
                            <p>Cuenta personal para autónomos o particulares.</p>
                        </div>
                    </label>
                    <label class="user-type-card" id="card-empresa">
                        <input type="radio" name="tipo" value="empresa" id="empresa" style="display:none;">
                        <div class="card-content">
                            <h4>Empresa</h4>
                            <p>Cuenta para empresas o negocios.</p>
                        </div>
                    </label>
                </div>
                <br>
                <button type="button" onclick="prevStep(5)">Anterior</button>
                <button type="button" id="btnNext5" onclick="nextStep(5)">Finalizar</button>
            </div>
            <!-- Paso 6 (solo para empresa) -->
            <div class="step" id="step6" style="display:none;">
                <label for="nombre_empresa">Nombre de la empresa:</label>
                <input type="text" name="nombre_empresa" id="nombre_empresa" required>
                <br><br>
                <button type="button" onclick="prevStep(6)">Anterior</button>
                <button type="button" onclick="nextStep(6)">Finalizar</button>
            </div>
        </form>
    
        <p>¿Ya tienes cuenta? <a href="index.php?controlador=usuarios&action=login">Iniciar Sesion</a></p>
    </div>
</body>

</html>