class InvoiceForm {
            constructor() {
                this.itemsContainer = document.getElementById('itemsContainer');
                this.numItemsInput = document.getElementById('numItems');
                this.totalAmountElement = document.getElementById('totalAmount');
                
                this.initEventListeners();
                this.generateItems(1); // Empezar con 1 item por defecto
            }

            initEventListeners() {
                this.numItemsInput.addEventListener('input', (e) => {
                    const numItems = parseInt(e.target.value) || 1;
                    this.generateItems(numItems);
                });
            }

            generateItems(numItems) {
                // Limpiar container
                this.itemsContainer.innerHTML = '';

                // Generar nuevos items
                for (let i = 1; i <= numItems; i++) {
                    this.createItemRow(i);
                    console.log(`Item ${i} creado`);
                }

                this.calculateTotal();
            }

            createItemRow(itemNumber) {
                const itemRow = document.createElement('div');
                itemRow.className = 'item-row';
                itemRow.innerHTML = `
                    <div>
                        <div class="item-number">Item ${itemNumber}</div>
                        <label for="concept${itemNumber}">Concepto:</label>
                        <input type="text" id="concept${itemNumber}" name="concept${itemNumber}" 
                               placeholder="Descripción del producto o servicio" required>
                    </div>
                    <div>
                        <label for="price${itemNumber}">Precio (€):</label>
                        <input type="number" id="price${itemNumber}" name="price${itemNumber}" 
                               step="0.01" min="0" placeholder="0.00" required>
                    </div>
                    <div>
                        ${numItems > 1 ? `<button type="button" class="delete-btn" onclick="invoiceForm.deleteItem(this)">×</button>` : ''}
                    </div>
                `;

                // Agregar event listener para el cálculo del total
                const priceInput = itemRow.querySelector(`#price${itemNumber}`);
                priceInput.addEventListener('input', () => this.calculateTotal());

                this.itemsContainer.appendChild(itemRow);
            }

            deleteItem(deleteBtn) {
                const itemRow = deleteBtn.closest('.item-row');
                itemRow.remove();
                
                // Actualizar numeración
                this.renumberItems();
                
                // Actualizar el input de número de items
                const remainingItems = this.itemsContainer.children.length;
                this.numItemsInput.value = remainingItems;
                
                this.calculateTotal();
            }

            renumberItems() {
                let itemRows = this.itemsContainer.children;
                Array.from(itemRows).forEach((row, index) => {
                    let itemNumber = index + 1;
                    let numberDiv = row.querySelector('.item-number');
                    let conceptInput = row.querySelector('input[id^="concept"]');
                    let priceInput = row.querySelector('input[id^="price"]');
                    
                    numberDiv.textContent = `Item ${itemNumber}`;
                    conceptInput.id = `concept${itemNumber}`;
                    conceptInput.name = `concept${itemNumber}`;
                    priceInput.id = `price${itemNumber}`;
                    priceInput.name = `price${itemNumber}`;
                });
            }

            calculateTotal() {
                let total = 0;
                let priceInputs = this.itemsContainer.querySelectorAll('input[id^="price"]');
                
                priceInputs.forEach(input => {
                    let price = parseFloat(input.value) || 0;
                    total += price;
                });

                this.totalAmountElement.textContent = `€${total.toFixed(2)}`;
            }

            handleSubmit() {
                let formData = new FormData(document.getElementById('invoiceForm'));
                let invoiceData = {
                    client: {
                        name: formData.get('clientName'),
                        email: formData.get('clientEmail')
                    },
                    items: [],
                    notes: formData.get('notes'),
                    total: 0
                };

                // Recopilar items
                let itemRows = this.itemsContainer.children;
                Array.from(itemRows).forEach((row, index) => {
                    let itemNumber = index + 1;
                    let concept = formData.get(`concept${itemNumber}`);
                    let price = parseFloat(formData.get(`price${itemNumber}`)) || 0;
                    
                    invoiceData.items.push({
                        concept: concept,
                        price: price
                    });
                    
                    invoiceData.total += price;
                });

                // Mostrar datos de la factura
                console.log('Datos de la factura:', invoiceData);
                alert(`Factura creada exitosamente!\n\nCliente: ${invoiceData.client.name}\nTotal: €${invoiceData.total.toFixed(2)}\nItems: ${invoiceData.items.length}`);
            }
        }

        // Inicializar la aplicación
        const invoiceForm = new InvoiceForm();