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
        <div class="form-blocks-row">
            <div class="form-block">
                <h3>Datos del remitente</h3>
                <label>Fecha: <input type="date" name="fecha" ></label>
                <label>Nombre y Apellido: <input type="text" name="nombre" ></label>
                <label>NºFactura: <input type="text" name="nfactura" ></label>
                <label>Dirección: <input type="text" name="direccion" ></label>
                <label>Teléfono: <input type="text" name="telefono" ></label>
                <label>Código Postal: <input type="text" name="codigo_postal" ></label>
                <label>Ciudad: <input type="text" name="ciudad" ></label>
            </div>
            <div class="form-block">
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
                            <label for="numItems">Añadir item:</label>
                            <button type="button" id="addItemBtn" title="Añadir item" style="font-size:1.3em;padding:4px 12px;">+</button>
                        </div>
                    </div>
                    
                    <div id="itemsContainer"></div>
                    <div style="display:none" id="itemRowTemplate">
    <div class="item-row">
        <input type="text" name="item_descripcion[]" placeholder="Descripción" required>
        <input type="number" name="item_cantidad[]" placeholder="Cantidad" min="1" value="1" required>
        <input type="number" name="item_precio[]" placeholder="Precio" min="0" step="0.01" required>
        <input type="number" name="item_total[]" placeholder="Total" min="0" step="0.01" readonly>
        <button type="button" class="remove-item-btn">&times;</button>
    </div>
</div>
                </div>

                <div class="total-section">
                    <h3>Total de la Factura</h3>
                    <div class="total-amount" id="totalAmount">€0.00</div>
                    <div style="margin-top:10px;">
        <label for="ivaPorcentaje">IVA (%):</label>
        <input type="number" id="ivaPorcentaje" value="21" min="0" max="100" style="width:60px;">
    </div>
    <div style="margin-top:10px;">
        <strong>Total con IVA:</strong>
        <span id="totalConIva">€0.00</span>
    </div>
                </div>

                <button type="submit" name="convertir" >Convertir a Factura</button>
            </form>
        </div>
    </div>
        
    </form>
    <?php require_once("footer_view.php"); ?>
    <?php require_once("loading_view.php"); ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form'); // Ajusta el selector si tu formulario tiene un id/clase
        const loading = document.querySelector('.loading-overlay');
        if (form && loading) {
            loading.style.display = 'none';
            form.addEventListener('submit', function() {
                loading.style.display = 'flex';
            });
        }

        // Items dinámicos
    const itemsContainer = document.getElementById('itemsContainer');
    const itemRowTemplate = document.getElementById('itemRowTemplate').innerHTML;
    const totalAmount = document.getElementById('totalAmount');
    const addItemBtn = document.getElementById('addItemBtn');
    const ivaPorcentaje = document.getElementById('ivaPorcentaje');
    const totalConIva = document.getElementById('totalConIva');

    function updateTotal() {
        let total = 0;
        itemsContainer.querySelectorAll('.item-row').forEach(row => {
            const cantidad = parseFloat(row.querySelector('input[name="item_cantidad[]"]').value) || 0;
            const precio = parseFloat(row.querySelector('input[name="item_precio[]"]').value) || 0;
            const subtotal = cantidad * precio;
            row.querySelector('input[name="item_total[]"]').value = subtotal.toFixed(2);
            total += subtotal;
        });
        totalAmount.textContent = "€" + total.toFixed(2);

        // Calcular IVA y total con IVA
        const iva = parseFloat(ivaPorcentaje.value) || 0;
        const totalIva = total + (total * iva / 100);
        totalConIva.textContent = "€" + totalIva.toFixed(2);
    }

    function addItemRow() {
        const temp = document.createElement('div');
        temp.innerHTML = itemRowTemplate;
        const row = temp.firstElementChild;
        row.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', updateTotal);
        });
        row.querySelector('.remove-item-btn').addEventListener('click', function() {
            row.remove();
            updateTotal();
        });
        itemsContainer.appendChild(row);
        updateTotal();
    }

    // Añadir item con el botón +
    addItemBtn.addEventListener('click', addItemRow);

    // Inicializa con un item por defecto
    addItemRow();
    });
    </script>
</body>
</html>