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
                <label>Nombre y Apellido: <input type="text" name="nombre" ></label>
                <label>NºFactura: <input type="text" name="nfactura" ></label>
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
        <div class="container">
        
        <div class="form-content">
            <form id="invoiceForm">

                <div class="items-section">
                    <div class="items-header">
                        <h3>Items de la Factura</h3>
                        <div class="number-input-container">
                            <label for="numItems">Número de items:</label>
                            <input type="number" id="numItems" min="1" max="50" value="1">
                        </div>
                    </div>
                    
                    <div id="itemsContainer">
                        <!-- Los items se generarán aquí dinámicamente -->
                    </div>
                </div>

                <div class="total-section">
                    <h3>Total de la Factura</h3>
                    <div class="total-amount" id="totalAmount">€0.00</div>
                </div>

                <button type="submit" name="convertir" >Convertir a Factura</button>
            </form>
        </div>
    </div>
        
    </form>
    <?php require_once("footer_view.php"); ?>
</body>
</html>