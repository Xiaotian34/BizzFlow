
<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Importar PhpSpreadsheet
require_once 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

require_once("view/menu_view.php");

echo "Procesando con PhpSpreadsheet...\n";

$outputFile = "C:\\xampp\\htdocs\\proyecto\\BizzFlow\\pruebas_output\\output.xlsx";
$inputFile = "C:\\xampp\\htdocs\\proyecto\\BizzFlow\\documentos\\documento1.xlsx";

try {
    // Cargar el archivo Excel existente
    $reader = IOFactory::createReader('Xlsx');
    $spreadsheet = $reader->load($inputFile);
    $worksheet = $spreadsheet->getActiveSheet();

    $fila = 2; // Asumiendo que la fila 1 son los encabezados
    $facturas = []; // Array para almacenar los datos de las facturas
    $totalMonto = 0;
    $totalFacturas = 0;

    while (true) {
        // Leer valores de las celdas
        $fecha = $worksheet->getCell("C$fila")->getCalculatedValue();
        $cliente = $worksheet->getCell("A$fila")->getCalculatedValue();
        $monto = $worksheet->getCell("G$fila")->getCalculatedValue();

        // Convertir a string para verificar si están vacías
        $fechaStr = (string)$fecha;
        $clienteStr = (string)$cliente;
        $montoStr = (string)$monto;

        if ($fechaStr == "" && $clienteStr == "" && $montoStr == "") {
            break; // Salimos si ya no hay más datos
        }

        echo "Factura #$fila:\n";
        echo "Fecha: $fechaStr\n";
        echo "Cliente: $clienteStr\n";
        echo "Monto: $montoStr\n";
        echo "Tipo de dato: " . gettype($monto) . "\n\n";

        // Guardar los datos en el array para usar en el gráfico
        $facturas[] = [
            'fecha' => $fechaStr,
            'cliente' => $clienteStr,
            'monto' => is_numeric($monto) ? (float)$monto : 0
        ];

        // Calcular estadísticas
        if (is_numeric($monto)) {
            $totalMonto += (float)$monto;
            $totalFacturas++;
        }

        $fila++;
    }

    // Calcular estadísticas adicionales
    $facturaPromedio = $totalFacturas > 0 ? $totalMonto / $totalFacturas : 0;
    $clientesUnicos = count(array_unique(array_column($facturas, 'cliente')));

    echo "Hello World!\n";
    echo "Total procesado: " . count($facturas) . " facturas\n";

} catch (Exception $e) {
    echo "Error al procesar el archivo Excel: " . $e->getMessage() . "\n";
    // Valores por defecto en caso de error
    $totalMonto = 15847;
    $totalFacturas = 127;
    $facturaPromedio = 124.78;
    $clientesUnicos = 42;
    $facturas = [];
}

// Preparar datos para el gráfico
$datosGrafico = [];
if (!empty($facturas)) {
    // Agrupar por mes si hay datos de fecha válidos
    $montosPorMes = array_fill(0, 12, 0);
    
    foreach ($facturas as $factura) {
        if (!empty($factura['fecha'])) {
            try {
                $fechaObj = new DateTime($factura['fecha']);
                $mes = (int)$fechaObj->format('n') - 1; // 0-11 para el array
                if ($mes >= 0 && $mes < 12) {
                    $montosPorMes[$mes] += $factura['monto'];
                }
            } catch (Exception $e) {
                // Si no se puede parsear la fecha, ignorar
                continue;
            }
        }
    }
    $datosGrafico = $montosPorMes;
} else {
    // Datos de ejemplo si no hay datos reales
    $datosGrafico = [1200, 1500, 1800, 2100, 1900, 2300, 2000, 1700, 1600, 1400, 1300, 1100];
}

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
                <div class="stat-value" id="totalRevenue">€<?php echo number_format($totalMonto, 2); ?></div>
                <div class="stat-label">Ingresos Totales</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">📄</div>
                <div class="stat-value" id="totalInvoices"><?php echo $totalFacturas; ?></div>
                <div class="stat-label">Facturas Emitidas</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">💳</div>
                <div class="stat-value" id="avgInvoice">€<?php echo number_format($facturaPromedio, 2); ?></div>
                <div class="stat-label">Factura Promedio</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #4bc0c8 0%, #c779d0 100%);">👥</div>
                <div class="stat-value" id="activeClients"><?php echo $clientesUnicos; ?></div>
                <div class="stat-label">Clientes Activos</div>
            </div>
        </div>
</div>
<div class="contenedor-grafico">
    <canvas id="miGrafico" width="400" height="200"></canvas>
</div>

<script>
    console.log("Datos cargados desde PhpSpreadsheet");
    console.log("Total facturas procesadas: <?php echo count($facturas); ?>");
    
    let labels = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    
    const data = {
        labels: labels,
        datasets: [{
            label: 'Ingresos Mensuales (€)',
            data: [<?php echo implode(', ', $datosGrafico); ?>],
            fill: false,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.1,
            pointBackgroundColor: 'rgb(75, 192, 192)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgb(75, 192, 192)'
        }]
    };
    
    const ctx = document.getElementById('miGrafico').getContext('2d');

    const miGrafico = new Chart(ctx, {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Evolución de Ingresos por Mes'
                },
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, values) {
                            return '€' + value.toLocaleString();
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Funcionalidad para los botones de período
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remover clase active de todos los botones
            document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
            // Agregar clase active al botón clickeado
            this.classList.add('active');
            
            // Aquí puedes agregar lógica para filtrar datos según el período
            const period = this.dataset.period;
            console.log('Período seleccionado:', period, 'días');
        });
    });
</script>