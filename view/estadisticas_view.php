<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);
phpinfo();
use com\aspose\cells;
require_once("view/menu_view.php");
require_once("C:\\nuevoxampp\\tomcat\webapps\JavaBridge\java\Java.inc");
require_once("C:\\nuevoxampp\htdocs\proyecto\BizzFlow\\vendor\aspose\cells\lib\aspose.cells.php");

echo "Java version: ".java("java.lang.System")->getProperty("java.version");
echo "\n";

$inputXml = "C:\\xampp\htdocs\proyecto\BizzFlow\pruebas_input\input.xlsx";
$outputFile = "C:\\xampp\htdocs\proyecto\BizzFlow\pruebas_output\output.xlsx";
$saveOptions = java("com.aspose.cells.SaveFormat");
$format = $saveOptions->XLSX;

$workbook = new cells\Workbook($inputXml);
$sheets = $workbook->getWorksheets();
$cells = $sheets->get(0)->getCells();
$cells->get("A1")->putValue("Hello world!2");
$workbook->save($outputFile, $format);

echo "Hello World!\n";

?>