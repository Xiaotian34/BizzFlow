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
    // Cambia la ruta al directorio correcto y añade comprobación
    $correoUsuario = $_SESSION['correo'];
    $directorio = __DIR__ . "/../documentos/" . $correoUsuario . "/excel";

    $archivosFiltrados = [];
    $facturas = []; // ¡IMPORTANTE! Inicializar la variable facturas

    $periodoDias = isset($_GET['period']) ? intval($_GET['period']) : 7;
    // Fechas de intervalo
    $hoy = new DateTime();
    $fechaInicio = (clone $hoy)->modify("-$periodoDias days");

    // Obtener lista de archivos
    if (is_dir($directorio)) {
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
    } else {
        $archivos = [];
        // Opcional: mostrar mensaje de que no hay archivos
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
            
            // Extraer fecha del nombre del archivo
            $nombreSinExtension = pathinfo($archivo, PATHINFO_FILENAME);
            $fechaArchivo = DateTime::createFromFormat('Y-m-d_H-i-s', $nombreSinExtension);
            $fechaFactura = $fechaArchivo ? $fechaArchivo->format('Y-m-d') : date('Y-m-d');

            // Buscar la posición de "precio" en el primer array
            $indicePrecio = array_search("Precio", $datos[0]);
            $indiceCliente = array_search("Cliente", $datos[0]);

            if ($indicePrecio !== false) {
                // Extraer el precio de los otros sub-arrays
                for ($i = 1; $i < count($datos); $i++) {
                    $valor = $datos[$i][$indicePrecio];
                    $montoItem = is_numeric($valor) ? floatval($valor) : 0;
                    $totalMonto += $montoItem;
                    
                    // Agregar cada item como una factura para el gráfico
                    $facturas[] = [
                        'fecha' => $fechaFactura,
                        'monto' => $montoItem,
                        'cliente' => ($indiceCliente !== false && isset($datos[$i][$indiceCliente])) ? $datos[$i][$indiceCliente] : 'Cliente desconocido'
                    ];
                }
                $totalFacturas++;
            } else {
                echo "No se encontró la columna 'precio' en $archivo.";
            }

            // Procesar clientes únicos
            if ($indiceCliente !== false) {
                // Recorrer los sub-arrays desde la fila 1 (omitimos cabecera)
                for ($i = 1; $i < count($datos); $i++) {
                    $cliente = $datos[$i][$indiceCliente];
                    if ($cliente != null) {
                        $clientesUnicos[$cliente] = true; // Usamos el nombre como clave
                    }
                }
            } else {
                echo "No se encontró la columna 'cliente' en $archivo.";
            }
        } catch (Exception $e) {
            echo "Error al leer el archivo $archivo: " . $e->getMessage() . "\n";
        }
    }
    
    // Obtener solo los nombres únicos
    $clientesUnicos = array_keys($clientesUnicos);
    // Antes de dividir por $totalFacturas, comprueba que no sea 0
    if ($totalFacturas > 0) {
        $facturaPromedio = $totalMonto / $totalFacturas;
    } else {
        $facturaPromedio = 0;
    }
} catch (Exception $e) {
    echo "Error al procesar el archivo Excel: " . $e->getMessage() . "\n";
    // Valores por defecto en caso de error
    $totalMonto = 0;
    $totalFacturas = 0;
    $facturaPromedio = 0;
    $clientesUnicos = [];
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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de Facturas</title>
    <link rel="stylesheet" href="css/stylesEstadisticas.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body>
<div class="header">
    <h1>Estadísticas de Facturas</h1>
    <p>Análisis completo de tus ventas y facturación</p>

    <div class="period-selector">
        <button class="period-btn" data-period="7">Últimos 7 días</button>
        <button class="period-btn" data-period="30">Último mes</button>
        <button class="period-btn" data-period="90">Últimos 3 meses</button>
        <button class="period-btn" data-period="365">Último año</button>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">💰</div>
            <div class="stat-info">
                <div class="stat-value" id="totalRevenue">€<?php echo number_format($totalMonto, 2); ?></div>
                <div class="stat-label">Ingresos Totales</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">📄</div>
            <div class="stat-info">
                <div class="stat-value" id="totalInvoices"><?php echo $totalFacturas; ?></div>
                <div class="stat-label">Facturas Emitidas</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">💳</div>
            <div class="stat-info">
                <div class="stat-value" id="avgInvoice">€<?php echo number_format($facturaPromedio, 2); ?></div>
                <div class="stat-label">Factura Promedio</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #4bc0c8 0%, #c779d0 100%);">👥</div>
            <div class="stat-info">
                <div class="stat-value" id="activeClients"><?php echo count($clientesUnicos); ?></div>
                <div class="stat-label">Clientes Activos</div>
            </div>
        </div>
    </div>
</div>

<div class="charts-section">
    <div class="chart-container">
        <div class="chart-title">Evolución de Ingresos por Mes</div>
        <div style="height:350px;">
            <canvas id="miGrafico"></canvas>
        </div>
    </div>
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
            fill: true,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.4,
            pointBackgroundColor: 'rgb(75, 192, 192)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgb(75, 192, 192)',
            pointRadius: 6,
            pointHoverRadius: 8,
            borderWidth: 3
        }]
    };

    const ctx = document.getElementById('miGrafico').getContext('2d');
    const miGrafico = new Chart(ctx, {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Evolución de Ingresos por Mes',
                    font: {
                        size: 18,
                        weight: 'bold'
                    },
                    color: '#333'
                },
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            size: 14
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, values) {
                            return '€' + value.toLocaleString();
                        },
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
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

            // Redirigir con el nuevo período
            const period = this.dataset.period;
            const url = new URL(window.location);
            url.searchParams.set('period', period);
            window.location.href = url.toString();
        });
    });

    // Activar el botón correcto según el período actual
    const currentPeriod = new URLSearchParams(window.location.search).get('period') || '7';
    document.querySelectorAll('.period-btn').forEach(btn => {
        if (btn.dataset.period === currentPeriod) {
            btn.classList.add('active');
        }
    });
</script>
</body>
</html>