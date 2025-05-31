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

try {

    $directorio = "C:\\nuevoxampp\\htdocs\\proyecto\\BizzFlow\\BizzFlow\documentos\butano@gmail.com\\excel";
    $archivosFiltrados = [];

    $periodoDias = isset($_GET['period']) ? intval($_GET['period']) : 7;
    // Fechas de intervalo
    $hoy = new DateTime();
    $fechaInicio = (clone $hoy)->modify("-$periodoDias days");

    // Obtener lista de archivos
    $archivos = scandir($directorio);

    foreach ($archivos as $archivo) {
        // Ignorar . y ..
        if ($archivo === '.' || $archivo === '..') {
            continue;
        }

        // Extraer nombre sin extensión
        $nombreSinExtension = pathinfo($archivo, PATHINFO_FILENAME);

        // Intentar crear objeto DateTime desde el nombre
        $fechaArchivo = DateTime::createFromFormat('Y-m-d_H-i-s', $nombreSinExtension);

        if ($fechaArchivo && $fechaArchivo >= $fechaInicio && $fechaArchivo <= $hoy) {
            $archivosFiltrados[] = $archivo;
        }
    }

    // Leer datos de cada archivo con PhpSpreadsheet
    $totalMonto = 0;
    $totalFacturas = 0;
    $clientesUnicos = [];
    foreach ($archivosFiltrados as $archivo) {
        $rutaArchivo = $directorio . DIRECTORY_SEPARATOR . $archivo;

        try {
            $spreadsheet = IOFactory::load($rutaArchivo);
            $hojaActiva = $spreadsheet->getActiveSheet();
            $datos = $hojaActiva->toArray();
            $totalFacturas++;

            // Buscar la posición de "precio" en el primer array
            $indicePrecio = array_search("Precio", $datos[0]);

            if ($indicePrecio != false) {
                // Extraer el precio de los otros sub-arrays
                for ($i = 1; $i < count($datos); $i++) {
                    $totalMonto = $totalMonto + $datos[$i][$indicePrecio];
                }
            } else {
                echo "No se encontró la columna 'precio'.";
            }

            // Buscar el índice de la columna "cliente"
            $indiceCliente = array_search("Cliente", $datos[0]);

            if ($indiceCliente !== false) {

                // Recorrer los sub-arrays desde la fila 1 (omitimos cabecera)
                for ($i = 1; $i < count($datos); $i++) {
                    $cliente = $datos[$i][$indiceCliente];
                    if ($cliente != null) {
                        $clientesUnicos[$cliente] = true; // Usamos el nombre como clave
                    }
                }
            } else {
                echo "No se encontró la columna 'cliente'.";
            }
        } catch (Exception $e) {
            echo "Error al leer el archivo $archivo: " . $e->getMessage() . "\n";
        }
    }
    // Obtener solo los nombres únicos
    $clientesUnicos = array_keys($clientesUnicos);
    $facturaPromedio = $totalMonto / $totalFacturas;
} catch (Exception $e) {
    echo "Error al procesar el archivo Excel: " . $e->getMessage() . "\n";
    // Valores por defecto en caso de error
    $totalMonto = "No Data";
    $totalFacturas = "No Data";
    $facturaPromedio = "No Data";
    $clientesUnicos = "No Data";
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
    <h1>Dashboard de Facturas</h1>
    <p>Análisis completo de tus ventas y facturación</p>

    <div class="period-selector">
        <button id="1" class="period-btn" data-period="7">Últimos 7 días</button>
        <button id="2" class="period-btn" data-period="30">Último mes</button>
        <button id="3" class="period-btn" data-period="90">Últimos 3 meses</button>
        <button id="4" class="period-btn" data-period="365">Último año</button>
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
            <div class="stat-value" id="activeClients"><?php echo count($clientesUnicos); ?></div>
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