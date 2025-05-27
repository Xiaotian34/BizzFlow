<?php
require_once("view/menu_view.php");
if (isset($_SESSION["correo"])) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Documentos</title>
    <link rel="stylesheet" href="css/stylesGestion.css">
</head>
<body>
    <main>
        <h1>Mis Documentos</h1>
        <form class="upload-form" action="" method="POST" enctype="multipart/form-data">
            <label for="documento">Subir nuevo documento:</label>
            <input type="file" id="documento" name="documento" required>
            <input type="submit" name="insertar" value="Subir">
        </form>

        <?php if (isset($array_documentos) && count($array_documentos) > 0): ?>
            <table>
                <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Fecha de subida</th>
                    <th>Archivo</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach ($array_documentos as $doc): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($doc['nombre_archivo']); ?></td>
                        <td><?php echo htmlspecialchars($doc['tipo']); ?></td>
                        <td><?php echo htmlspecialchars($doc['fecha_subida']); ?></td>
                        <td>
                            <a href="<?php echo htmlspecialchars($doc['ruta_archivo']); ?>" target="_blank">Ver/Descargar</a>
                        </td>
                        <td class="actions">
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $doc['id']; ?>">
                                <input type="submit" name="borrar" value="Eliminar">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No has subido ningún documento.</p>
        <?php endif; ?>
    </main>
</body>
</html>
<?php
}
?>