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