<?php
require_once("view/menu_view.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="css/stylesPerfil.css">
    <link rel="stylesheet" href="css/normalize.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="profile-info">
            <img src="<?php echo $_SESSION['foto_perfil'] ?? 'img/imgPerfil/defaultProfile.svg'; ?>" alt="Foto de perfil">
             
            <h3><?php echo $_SESSION['nombre']; ?></h3>
        </div>
        <a href="#informacionPersonal"><i class="fas fa-user"></i> Mi cuenta</a>
        <a href="#seguridad"><i class="fas fa-lock"></i> Seguridad</a>
        <hr>
        <a href="#actividad"><i class="fas fa-clock"></i> Últimas tareas</a>
        <a href="#configuracion"><i class="fas fa-cog"></i> Configuración</a>
    </div>

    <!-- Main Content -->
    <div class="container">
        <main>
            <!-- Información Personal -->
            <section id="informacionPersonal">
                <h2>Información Personal</h2>
                <form action="" method="POST" enctype="multipart/form-data">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo $_SESSION['nombre']; ?>" required>

                    <label for="correo">Correo:</label>
                    <input type="email" id="correo" name="correo" value="<?php echo $_SESSION['correo']; ?>" required>

                    <label for="foto_perfil">Foto de Perfil:</label>
                    <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*">

                    <input type="submit" name="actualizar" value="Actualizar Información">
                </form>
            </section>

            <!-- Seguridad -->
            <section id="seguridad">
                <h2>Seguridad</h2>
                <form action="" method="POST">
                    <label for="cambiar_passwd">Cambiar Contraseña:</label>
                    <input type="password" id="cambiar_passwd" name="cambiar_passwd" placeholder="Nueva Contraseña" required>

                    <label for="numero_recuperacion">Número de Recuperación:</label>
                    <input type="tel" id="numero_recuperacion" name="numero_recuperacion" value="<?php echo $_SESSION['numero_recuperacion'] ?? ''; ?>" placeholder="Número de Teléfono">

                    <label for="correo_recuperacion">Correo de Recuperación:</label>
                    <input type="email" id="correo_recuperacion" name="correo_recuperacion" value="<?php echo $_SESSION['correo_recuperacion'] ?? ''; ?>" placeholder="Correo Electrónico">

                    <input type="submit" name="actualizar_seguridad" value="Actualizar Seguridad">
                </form>
            </section>

            <!-- Actividad -->
            <section id="actividad">
                <h2>Últimas tareas</h2>
                <p>Aquí puedes ver un resumen de tus últimas actividades.</p>
                <!-- Agrega contenido dinámico aquí -->
            </section>

            <!-- Configuración -->
            <section id="configuracion">
                <h2>Configuración</h2>
                <p>Ajusta las configuraciones de tu cuenta.</p>
                <!-- Agrega contenido dinámico aquí -->
            </section>
        </main>
    </div>
</body>
</html>
<?php
require_once("view/footer_view.php");
?>