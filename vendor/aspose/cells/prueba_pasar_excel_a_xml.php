<?php

use aspose\cells;

require_once("Java.inc");
require_once("lib/aspose.cells.php");

// Ruta de entrada y salida
$inputFile = "C:\\Users\\josea\\OneDrive\Escritorio\a\\Book2.xlsx";
$outputFile = "C:\\Users\\josea\\OneDrive\Escritorio\b\\Book2.xml";

// Cargar el archivo Excel
$workbook = new cells\Workbook($inputFile);

// Guardar como XML Spreadsheet 2003
$workbook->save($outputFile, cells\SaveFormat::SPREADSHEET_ML);

echo "Excel convertido exitosamente a XML.\n";

?>