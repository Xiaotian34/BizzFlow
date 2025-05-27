<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel a XML</title>
    <link rel="stylesheet" href="css/stylesExcelToXml.css">
</head>
<body>
    <?php require_once("menu_view.php"); ?>
    <h2>Conversión Excel a XML</h2>
    <form class="form-container" action="index.php?controlador=documentos&action=convertirExcelXml" method="post" enctype="multipart/form-data">
        <div class="form-columns">
            <div class="form-column">
                <h3>Datos del remitente</h3>
                <label>Fecha: <input type="date" name="fecha" ></label>
                <label>Nombre: <input type="text" name="nombre" ></label>
                <label>Apellido: <input type="text" name="apellido" ></label>
                <label>Dirección: <input type="text" name="direccion" ></label>
                <label>Teléfono: <input type="text" name="telefono" ></label>
                <label>Código Postal: <input type="text" name="codigo_postal" ></label>
                <label>Ciudad: <input type="text" name="ciudad" ></label>
            </div>
            <div class="form-column">
                <h3>Datos del cliente</h3>
                <label>Nombre del cliente: <input type="text" name="cliente_nombre" ></label>
                <label>NIF/NIE/DNI: <input type="text" name="cliente_nif" ></label>
                <label>Domicilio: <input type="text" name="cliente_domicilio" ></label>
                <label>Código Postal: <input type="text" name="cliente_cp" ></label>
                <label>Teléfono: <input type="text" name="cliente_telefono" ></label>
            </div>
        </div>
        <hr>
        <div class="file-section">
            <label>Archivo Excel: <input type="file" name="excel_file" accept=".xls,.xlsx" ></label>
        </div>
        <button type="submit">Convertir a Factura</button>
    </form>
    <?php require_once("footer_view.php"); ?>
</body>
</html>