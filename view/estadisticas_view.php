<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);

use com\aspose\cells;
require_once("view/menu_view.php");
require_once("C:\\xampp\\tomcat\webapps\JavaBridge\java\Java.inc");
require_once("C:\\xampp\htdocs\proyecto\BizzFlow\\vendor\aspose\cells\lib\aspose.cells.php");

echo "Java version: ".java("java.lang.System")->getProperty("java.version");
echo "\n";

$outputFile = "C:\\xampp\htdocs\proyecto\BizzFlow\pruebas_output\output.xlsx";
$saveOptions = java("com.aspose.cells.SaveFormat");
$format = $saveOptions->XLSX;

$inputFile = "C:\\xampp\htdocs\proyecto\BizzFlow\documentos\documento1.xlsx";

$workbook = new cells\Workbook($inputFile);
$worksheet = $workbook->getWorksheets()->get(0); // Primera hoja
$cells = $worksheet->getCells();

$fila = 2; // Asumiendo que la fila 0 son los encabezados

while (true) {
    $fechaCell = $cells->get("C$fila");
    $clienteCell = $cells->get("A$fila");
    $montoCell = $cells->get("G$fila");

    $fecha = $fechaCell->getStringValue();
    $cliente = $clienteCell->getStringValue();
    $monto = $montoCell->getStringValue(); // O getStringValue si no estás seguro del tipo

    if ($fecha == "" && $cliente == "" && $monto == "") {
        break; // Salimos si ya no hay más datos
    }

    echo "Factura #$fila:\n";
    echo "Fecha: $fecha\n";
    echo "Cliente: $cliente\n";
    echo "Monto: $monto\n\n";
    echo "Tipo de celda: " . $cells->get("G$fila")->getType();
    echo gettype($monto);

    $fila++;
}

echo "Hello World!\n";

?>
<div class="header">
    <h1>📊 Dashboard de Facturas</h1>
    <p>Análisis completo de tus ventas y facturación</p>
            
    <div class="period-selector">
        <button class="period-btn active" data-period="7">Últimos 7 días</button>
        <button class="period-btn" data-period="30">Último mes</button>
        <button class="period-btn" data-period="90">Últimos 3 meses</button>
        <button class="period-btn" data-period="365">Último año</button>
    </div>
    <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">💰</div>
                <div class="stat-value" id="totalRevenue">€15,847</div>
                <div class="stat-label">Ingresos Totales</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">📄</div>
                <div class="stat-value" id="totalInvoices">127</div>
                <div class="stat-label">Facturas Emitidas</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">💳</div>
                <div class="stat-value" id="avgInvoice">€124.78</div>
                <div class="stat-label">Factura Promedio</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #4bc0c8 0%, #c779d0 100%);">👥</div>
                <div class="stat-value" id="activeClients">42</div>
                <div class="stat-label">Clientes Activos</div>
            </div>
        </div>
</div>
<div class="contenedor-grafico">
    <canvas id="miGrafico" width="400" height="200"></canvas>
</div>

<script>
    console.log(<?php $monto; ?>);
    console.log("aqui");
    let labels = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    const data = {
    labels: labels,
    datasets: [{
        label: 'My First Dataset',
        data: [<?php echo $monto; ?>, 59, 80, 81, 56, 55, 40],
        fill: false,
        borderColor: 'rgb(75, 192, 192)',
        tension: 0.1
    }]
    };
    
    const ctx = document.getElementById('miGrafico').getContext('2d');

    const miGrafico = new Chart(ctx, {
        type: 'line',
        data: data,
        
    });
</script>