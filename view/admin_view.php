<?php
require_once("view/menu_view.php");

// Detectar si hay un usuario en edición
$usuarioEditar = null;
if (isset($_POST['editar']) && isset($_POST['correo'])) {
    foreach ($usuarios as $u) {
        if ($u['correo_electronico'] === $_POST['correo']) {
            $usuarioEditar = $u;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Usuarios</title>
    <link rel="stylesheet" href="css/stylesAdmin.css">
    <style>
        .edit-row {
            background: var(--secondary-light);
        }
        .edit-form td {
            padding: 16px 10px;
            background: var(--secondary-light);
        }
        .edit-form input[type="text"],
        .edit-form input[type="email"],
        .edit-form input[type="number"],
        .edit-form input[type="password"] {
            width: 90%;
        }
        .edit-form label {
            margin-right: 8px;
        }
        .edit-form input[type="submit"] {
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <main>
        <h1>Panel de Administración</h1>
        
        <h2>Gestionar Usuarios</h2>
        <form class="admin-regist" action="" method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="apellidos">Apellidos:</label>
            <input type="text" name="apellidos" id="apellidos" required><br><br>

            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" required>
            
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required>
            
            <label for="passwd">Contraseña:</label>
            <input type="password" id="passwd" name="passwd" required>

            <label>Tipo de usuario:</label><br>
            <input type="radio" name="tipo" value="usuario" id="usuario" checked>
            <label for="usuario">Usuario</label>
            <input type="radio" name="tipo" value="empresa" id="empresa">
            <label for="empresa">Empresa</label><br><br>
            <input type="radio" name="tipo" value="admin" id="admin">
            <label for="admin">Administrador</label><br><br>
            <input type="submit" name="regist" value="Registrar">
        </form>

        <h2>Lista de Usuarios</h2>
        <div id="usuarios">
            <?php if (!empty($usuarios)): ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Edad</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Contraseña (hash)</th>
                        <th>Tipo</th>
                        <th>Fecha de registro</th>
                        <th>Imagen de perfil</th>
                        <th> </th>
                    </tr>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr<?php if ($usuarioEditar && $usuarioEditar['correo_electronico'] === $usuario['correo_electronico']) echo ' class="edit-row"'; ?>>
                            <td><?php echo $usuario['id']; ?></td>
                            <td><?php echo $usuario['nombre']; ?></td>
                            <td><?php echo $usuario['apellidos']; ?></td>
                            <td><?php echo $usuario['edad']; ?></td>
                            <td><?php echo $usuario['correo_electronico']; ?></td>
                            <td><?php echo $usuario['telefono']; ?></td>
                            <td><?php echo $usuario['contrasena_hash']; ?></td>
                            <td><?php echo $usuario['tipo']; ?></td>
                            <td><?php echo $usuario['fecha_registro']; ?></td>
                            <td>
                                <img src="<?php echo $usuario['imagen_perfil']; ?>" alt="Perfil" width="40" height="40">
                            </td>
                            <td>
                                <form action="" method="POST" style="display:inline;">
                                    <input type="hidden" name="correo" value="<?php echo $usuario['correo_electronico']; ?>">
                                    <input type="submit" name="editar" value="Editar">
                                </form>
                                <form action="" method="POST" style="display:inline;">
                                    <input type="hidden" name="correo" value="<?php echo $usuario['correo_electronico']; ?>">
                                    <input type="submit" name="borrar" value="Eliminar">
                                </form>
                            </td>
                        </tr>
                        <?php if ($usuarioEditar && $usuarioEditar['correo_electronico'] === $usuario['correo_electronico']): ?>
                        <tr class="edit-form">
                            <td colspan="11">
                                <form action="" method="POST" style="display:flex; flex-wrap:wrap; gap:18px 24px; align-items:center;">
                                    <input type="hidden" name="correo_original" value="<?php echo $usuario['correo_electronico']; ?>">
                                    <label>Nombre:
                                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                                    </label>
                                    <label>Apellidos:
                                        <input type="text" name="apellidos" value="<?php echo htmlspecialchars($usuario['apellidos']); ?>" required>
                                    </label>
                                    <label>Edad:
                                        <input type="number" name="edad" min="0" value="<?php echo $usuario['edad']; ?>" required>
                                    </label>
                                    <label>Correo:
                                        <input type="email" name="correo" value="<?php echo htmlspecialchars($usuario['correo_electronico']); ?>" required>
                                    </label>
                                    <label>Teléfono:
                                        <input type="text" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>" required>
                                    </label>
                                    <label>Nueva contraseña:
                                        <input type="password" name="passwd" placeholder="(Dejar en blanco para no cambiar)">
                                    </label>
                                    <label>Tipo:
                                        <select name="tipo" required>
                                            <option value="usuario" <?php if($usuario['tipo']=='usuario') echo 'selected'; ?>>Usuario</option>
                                            <option value="empresa" <?php if($usuario['tipo']=='empresa') echo 'selected'; ?>>Empresa</option>
                                            <option value="admin" <?php if($usuario['tipo']=='admin') echo 'selected'; ?>>Administrador</option>
                                        </select>
                                    </label>
                                    <input type="submit" name="guardar_edicion" value="Guardar cambios">
                                    <input type="submit" name="cancelar_edicion" value="Cancelar" style="background:var(--primary-dark);color:#fff;">
                                </form>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No hay usuarios registrados.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>