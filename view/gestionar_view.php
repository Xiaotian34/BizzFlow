<?php
require_once("view/menu_view.php");
if (isset($_SESSION["correo"])) {

// Unificar documentos y facturas en un solo array para mostrar la biblioteca
$biblioteca = [];

if (isset($array_documentos) && is_array($array_documentos)) {
    foreach ($array_documentos as $doc) {
        $biblioteca[] = [
            'id' => $doc['id'],
            'nombre_archivo' => $doc['nombre_archivo'],
            'tipo' => $doc['tipo'],
            'fecha_subida' => $doc['fecha_subida'],
            'ruta_archivo' => $doc['ruta_archivo'],
            'origen' => 'documento'
        ];
    }
}

if (isset($array_facturas) && is_array($array_facturas)) {
    foreach ($array_facturas as $fac) {
        $biblioteca[] = [
            'id' => $fac['id'],
            'nombre_archivo' => $fac['nombre_archivo'],
            'tipo' => 'xml',
            'fecha_subida' => $fac['fecha_subida'],
            'ruta_archivo' => $fac['ruta_archivo'],
            'origen' => 'factura'
        ];
    }
}

// Ordenar por fecha de subida descendente
usort($biblioteca, function($a, $b) {
    return strtotime($b['fecha_subida']) - strtotime($a['fecha_subida']);
});

// Detectar si hay un archivo en edición
$archivoEditar = null;
if (isset($_POST['editar']) && isset($_POST['id'])) {
    foreach ($biblioteca as $archivo) {
        if ($archivo['id'] == $_POST['id']) {
            $archivoEditar = $archivo;
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
    <title>Gestionar Documentos</title>
    <link rel="stylesheet" href="css/stylesGestion.css">
    <style>
        .edit-row {
            background: var(--secondary-light);
        }
        .edit-form td {
            padding: 16px 10px;
            background: var(--secondary-light);
        }
        .edit-form input[type="text"] {
            width: 90%;
        }
        .edit-form input[type="submit"] {
            margin-top: 8px;
        }
        .actions form {
            display: inline;
        }
    </style>
</head>
<body>
    <main>
        <h1>Mi Biblioteca de Archivos</h1>
        <form class="upload-form" action="" method="POST" enctype="multipart/form-data">
            <label for="documento">Subir nuevo documento:</label>
            <input type="file" id="documento" name="documento" required>
            <input type="submit" name="insertar" value="Subir">
        </form>

        <?php if (count($biblioteca) > 0): ?>
            <table>
                <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Fecha de subida</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach ($biblioteca as $archivo): ?>
                    <tr<?php if ($archivoEditar && $archivoEditar['id'] == $archivo['id']) echo ' class="edit-row"'; ?>>
                        <td><?php echo htmlspecialchars($archivo['nombre_archivo']); ?></td>
                        <td><?php echo htmlspecialchars($archivo['tipo']); ?></td>
                        <td><?php echo htmlspecialchars($archivo['fecha_subida']); ?></td>
                        <td class="actions">
                            <form action="" method="POST">
                                <input type="hidden" name="id" value="<?php echo $archivo['id']; ?>">
                                <input type="submit" name="editar" value="Editar">
                            </form>
                            <a href="<?php echo htmlspecialchars($archivo['ruta_archivo']); ?>" target="_blank">
                                <button type="button">Ver</button>
                            </a>
                            <a href="<?php echo htmlspecialchars($archivo['ruta_archivo']); ?>" download>
                                <button type="button">Descargar</button>
                            </a>
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $archivo['id']; ?>">
                                <input type="hidden" name="origen" value="<?php echo $archivo['origen']; ?>">
                                <input type="submit" name="borrar" value="Eliminar">
                            </form>
                        </td>
                    </tr>
                    <?php if ($archivoEditar && $archivoEditar['id'] == $archivo['id']): ?>
                    <tr class="edit-form">
                        <td colspan="4">
                            <form action="" method="POST" style="display:flex; flex-wrap:wrap; gap:18px 24px; align-items:center;">
                                <input type="hidden" name="id" value="<?php echo $archivo['id']; ?>">
                                <label>Nuevo nombre:
                                    <input type="text" name="nuevo_nombre" value="<?php echo htmlspecialchars($archivo['nombre_archivo']); ?>" required>
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
            <p>No has subido ningún archivo.</p>
        <?php endif; ?>
    </main>
</body>
</html>
<?php
}
?>