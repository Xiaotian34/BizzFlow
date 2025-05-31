<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturae - Generador de Facturas</title>
    <link rel="stylesheet" href="css/stylesExcelToXml.css">
</head>
<body>
<?php require_once("menu_view.php"); ?>
<h2>Generar Facturae</h2>
<form class="form-container" action="index.php?controlador=documentos&action=convertirExcelXml" method="post" enctype="multipart/form-data">
    <div class="form-blocks-row">
        <div class="form-block">
            <h3>Datos del Emisor (Remitente)</h3>
            <label>Tipo de Persona:
                <select name="emisor_tipo_persona" required>
                    <option value="F">Física</option>
                    <option value="J">Jurídica</option>
                </select>
            </label>
            <label>Tipo de Residencia:
                <select name="emisor_tipo_residencia" required>
                    <option value="R">Residente</option>
                    <option value="N">No Residente</option>
                </select>
            </label>
            <label>NIF/CIF/NIE: <input type="text" name="emisor_nif" required></label>
            <label>Nombre/Razón Social: <input type="text" name="emisor_nombre" required></label>
            <label>Dirección: <input type="text" name="emisor_direccion" required></label>
            <label>Código Postal: <input type="text" name="emisor_cp" required></label>
            <label>Ciudad: <input type="text" name="emisor_ciudad" required></label>
            <label>Provincia: <input type="text" name="emisor_provincia" required></label>
            <label>País: <input type="text" name="emisor_pais" value="ESP" readonly></label>
            <label>Teléfono: <input type="text" name="emisor_telefono"></label>
            <label>Email: <input type="email" name="emisor_email"></label>
        </div>
        <div class="form-block">
            <h3>Datos del Receptor (Cliente)</h3>
            <label>Tipo de Persona:
                <select name="cliente_tipo_persona" required>
                    <option value="F">Física</option>
                    <option value="J">Jurídica</option>
                </select>
            </label>
            <label>Tipo de Residencia:
                <select name="cliente_tipo_residencia" required>
                    <option value="R">Residente</option>
                    <option value="N">No Residente</option>
                </select>
            </label>
            <label>NIF/CIF/NIE: <input type="text" name="cliente_nif" required></label>
            <label>Nombre/Razón Social: <input type="text" name="cliente_nombre" required></label>
            <label>Dirección: <input type="text" name="cliente_direccion" required></label>
            <label>Código Postal: <input type="text" name="cliente_cp" required></label>
            <label>Ciudad: <input type="text" name="cliente_ciudad" required></label>
            <label>Provincia: <input type="text" name="cliente_provincia" required></label>
            <label>País: <input type="text" name="cliente_pais" value="ESP" readonly></label>
            <label>Teléfono: <input type="text" name="cliente_telefono"></label>
            <label>Email: <input type="email" name="cliente_email"></label>
        </div>
    </div>
    <hr>
    <div class="form-blocks-row">
        <div class="form-block">
            <h3>Datos de la Factura</h3>
            <label>Fecha de Emisión: <input type="date" name="fecha" required></label>
            <label>Nº de Factura: <input type="text" name="nfactura" required></label>
            <label>Serie: <input type="text" name="serie" value="A" readonly></label>
            <label>Tipo de Documento: 
                <select name="tipo_documento" required>
                    <option value="FC">Factura</option>
                    <option value="RC">Recibo</option>
                </select>
            </label>
            <label>Clase de Factura: 
                <select name="clase_factura" required>
                    <option value="OO">Ordinaria</option>
                </select>
            </label>
            <label>Moneda: <input type="text" name="moneda" value="EUR" readonly></label>
            <label>Idioma: <input type="text" name="idioma" value="es" readonly></label>
        </div>
        <div class="form-block">
            <h3>Forma de Pago (opcional)</h3>
            <label>Forma de Pago: <input type="text" name="forma_pago"></label>
            <label>Cuenta Bancaria (IBAN): <input type="text" name="iban"></label>
        </div>
    </div>
    <hr>
    <div class="container">
        <div class="form-content">
            <div class="items-section">
                <div class="items-header">
                    <h3>Items de la Factura</h3>
                    <div class="number-input-container">
                        <label for="numItems">Añadir item:</label>
                        <button type="button" id="addItemBtn" title="Añadir item" style="font-size:1.3em;padding:4px 12px;">+</button>
                    </div>
                </div>
                <div id="itemsContainer"></div>
                <div style="display:none" id="itemRowTemplate">
                    <div class="item-row">
                        <input type="text" name="item_descripcion[]" placeholder="Descripción">
                        <input type="number" name="item_cantidad[]" placeholder="Cantidad" min="1" value="1">
                        <input type="number" name="item_precio[]" placeholder="Precio" min="0" step="0.01">
                        <input type="number" name="item_total[]" placeholder="Total" min="0" step="0.01" readonly>
                        <button type="button" class="remove-item-btn">&times;</button>
                    </div>
                </div>
            </div>
            <div class="total-section">
                <h3>Total de la Factura</h3>
                <div class="total-amount" id="totalAmount">€0.00</div>
                <div class="iva-section">
                    <label for="ivaPorcentaje">IVA (%):</label>
                    <input type="number" id="ivaPorcentaje" name="ivaPorcentaje" value="21" min="0" max="100" required>
                    <span class="iva-total-label"><strong>Total con IVA:</strong></span>
                    <span id="totalConIva" class="iva-total-amount">€0.00</span>
                </div>
            </div>
            <button type="submit" name="convertir">Generar Facturae</button>
        </div>
    </div>

        
    </form>
    <?php require_once("footer_view.php"); ?>
    <?php require_once("loading_view.php"); ?>

</body>
</html>