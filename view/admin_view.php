<?php
require_once("view/menu_view.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Usuarios</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <main>
        <h1>Panel de Administración</h1>
        
        <h2>Gestionar Usuarios</h2>
        <form action="" method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" required>
            <label for="passwd">Contraseña:</label>
            <input type="password" id="passwd" name="passwd" required>
            <input type="submit" name="regist" value="Registrar Usuario">
        </form>

        <h2>Lista de Usuarios</h2>
        <div id="usuarios">
            <?php if (!empty($usuarios)): ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo $usuario['id']; ?></td>
                            <td><?php echo $usuario['nombre']; ?></td>
                            <td><?php echo $usuario['correo_electronico']; ?></td>
                            <td>
                                <form action="" method="POST">
                                    <input type="hidden" name="correo" value="<?php echo $usuario['correo_electronico']; ?>">
                                    <input type="submit" name="borrar" value="Eliminar">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No hay usuarios registrados.</p>
            <?php endif; ?>
        </div>

        <h2>Estadísticas</h2>
        <div id="estadisticas">
            <!-- Aquí se pueden mostrar estadísticas -->
            <p>Estadísticas de uso y documentos subidos.</p>
        </div>
    </main>
</body>
</html>