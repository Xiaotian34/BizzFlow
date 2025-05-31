<?php
session_start();

// Importar PhpSpreadsheet
require_once 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

function home()
{
    require_once("model/documentos_model.php");
    $documento = new Documentos_Model();

    $array_documentos = $documento->get_documentos();
    require_once("view/home_view.php");
}

function gestionarDocumentos()
{
    require_once("model/documentos_model.php");
    $documento = new Documentos_Model();

    $message = "";

    // Borrar documento
    if (isset($_POST["borrar"])) {
        $id = isset($_POST["id"]) ? $_POST["id"] : '';
        if ($documento->eliminarDocumento($id)) {
            $message = "Documento borrado correctamente";
        } else {
            $message = "Error al borrar documento";
        }
    }

    // Subir nuevo documento
    if (isset($_POST["insertar"])) {
        date_default_timezone_set('Europe/Madrid');
        $nombre = $_FILES['documento']['name'];
        $tipo = pathinfo($nombre, PATHINFO_EXTENSION);
        $ruta = "documentos/" . $nombre;

        $carpetaDestino = "documentos/";
        if (!file_exists($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true);
        }

        if (move_uploaded_file($_FILES["documento"]["tmp_name"], $ruta)) {
            $id_usuario = obtenerIdUsuarioPorCorreo($_SESSION["correo"]); // Función auxiliar
            if ($documento->insertarDocumento($id_usuario, $nombre, $tipo, $ruta)) {
                $message = "Documento subido correctamente";
            } else {
                $message = "Error al registrar en la base de datos";
            }
        } else {
            $message = "Error al mover el archivo";
        }
    }

    // Modificar documento (solo nombre, tipo y ruta, si se sube uno nuevo)
    if (isset($_POST["modificar"])) {
        $id = $_POST["id"];
        $nombreNuevo = $_FILES["documento"]["name"] ?? null;
        $rutaDestino = null;

        if ($nombreNuevo) {
            $carpetaDestino = "documentos/";
            $rutaDestino = $carpetaDestino . $nombreNuevo;

            $documentoAntiguo = $documento->get_documento_by_id($id);
            if (file_exists($documentoAntiguo["ruta_archivo"])) {
                unlink($documentoAntiguo["ruta_archivo"]);
            }

            move_uploaded_file($_FILES["documento"]["tmp_name"], $rutaDestino);
        } else {
            $rutaDestino = $documento->get_documento_by_id($id)["ruta_archivo"];
            $nombreNuevo = basename($rutaDestino);
        }

        $tipoNuevo = pathinfo($nombreNuevo, PATHINFO_EXTENSION);

        if ($documento->modificarDocumento($id, $nombreNuevo, $rutaDestino)) {
            $message = "Documento modificado correctamente";
        } else {
            $message = "Error al modificar";
        }
    }

    // Obtener todos los documentos
    $id_usuario = obtenerIdUsuarioPorCorreo($_SESSION["correo"]); // Función auxiliar
    $array_documentos = $documento->get_documentos_por_usuario($id_usuario);
    require_once("view/gestionar_view.php");
}

function estadisticas()
{
    require_once("model/documentos_model.php");
    $documento = new Documentos_Model();

    $message = "";

    // Obtener todos los documentos
    $id_usuario = obtenerIdUsuarioPorCorreo($_SESSION["correo"]); // Función auxiliar
    $array_documentos = $documento->get_documentos_por_usuario($id_usuario);
    require_once("view/estadisticas_view.php");
}

function excelToXmlForm() {
    require_once("model/documentos_model.php");
    $documento = new Documentos_Model();
    
    echo "aqui 1";
    if (isset($_POST["convertir"])) {
        echo "aqui 2";
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
            echo "aqui 3";
            try {
                // Guardar archivo temporalmente
                $excelTmp = $_FILES['excel_file']['tmp_name'];
                $excelName = $_FILES['excel_file']['name'];
                $destPath = __DIR__ . '/../uploads/' . uniqid() . '_' . $excelName;
                move_uploaded_file($excelTmp, $destPath);

                $outputFile = "C:\\xampp\\htdocs\\proyecto\\BizzFlow\\pruebas_output\\output.xlsx";

                // Leer Excel con PhpSpreadsheet
                $reader = IOFactory::createReader('Xlsx');
                $spreadsheet = $reader->load($destPath);
                $worksheet = $spreadsheet->getActiveSheet();

                // Ejemplo: leer datos de la primera fila (ajusta según tu plantilla)
                $concepto = $worksheet->getCell('A2')->getValue();
                $importe = $worksheet->getCell('B2')->getValue();

                // Recoge los datos del formulario
                $fecha = $_POST['fecha'];
                $nombreApellido = $_POST['nombre'];
                $NFactura = $_POST['nfactura'];
                $direccion = $_POST['direccion'];
                $telefono = $_POST['telefono'];
                $codigo_postal = $_POST['codigo_postal'];
                $ciudad = $_POST['ciudad'];
                $cliente_nombre = $_POST['cliente_nombre'];
                $cliente_nif = $_POST['cliente_nif'];
                $cliente_domicilio = $_POST['cliente_domicilio'];
                $cliente_cp = $_POST['cliente_cp'];
                $cliente_telefono = $_POST['cliente_telefono'];

                // Crear nuevo spreadsheet
                $newSpreadsheet = new Spreadsheet();
                $newWorksheet = $newSpreadsheet->getActiveSheet();

                // Añadir datos al Excel
                $newWorksheet->setCellValue('A1', 'Cliente');
                $newWorksheet->setCellValue('B1', 'Domicilio');
                $newWorksheet->setCellValue('C1', 'Fecha');
                $newWorksheet->setCellValue('D1', 'NºFactura');
                $newWorksheet->setCellValue('E1', 'Concepto');
                $newWorksheet->setCellValue('F1', 'Precio');
                $newWorksheet->setCellValue('G1', 'Total');

                // Escribir valores del formulario en fila 2
                $newWorksheet->setCellValue('A2', $cliente_nombre);
                $newWorksheet->setCellValue('B2', $cliente_domicilio);
                $newWorksheet->setCellValue('C2', $fecha);
                $newWorksheet->setCellValue('D2', $NFactura);
                $newWorksheet->setCellValue('E2', $concepto);
                
                if (is_numeric($importe)) {
                    $newWorksheet->setCellValue('F2', (float)$importe);
                } else {
                    $newWorksheet->setCellValue('F2', 'Monto inválido');
                }

                // Guardar archivo Excel
                $writer = new Xlsx($newSpreadsheet);
                $writer->save($outputFile);
                echo "Datos guardados correctamente.";

                // Crear XML semántico
                $xml = new DOMDocument('1.0', 'UTF-8');
                $factura = $xml->createElement('factura');

                $clienteTag = $xml->createElement('cliente', htmlspecialchars($cliente_nombre));
                $fechaTag = $xml->createElement('fecha', htmlspecialchars($fecha));
                $precioTag = $xml->createElement('precio', $importe);

                $factura->appendChild($clienteTag);
                $factura->appendChild($fechaTag);
                $factura->appendChild($precioTag);

                $xml->appendChild($factura);

                // Guardar XML
                $xml->formatOutput = true;
                $xmlOutputFile = "C:\\xampp\\htdocs\\proyecto\\BizzFlow\\pruebas_output\\output.xml";
                $xml->save($xmlOutputFile);

                echo "XML semántico creado correctamente.";
                
                // Limpiar archivo temporal
                unlink($destPath);
                
            } catch (Exception $e) {
                echo "Error al procesar el archivo: " . $e->getMessage();
            }
        } else {
            echo "Error al procesar el archivo.";
        }
    }
    require_once("view/excel_to_xml_view.php");
}

function convertirExcelXml() {
    
    if (isset($_POST["convertir"])) {
        
        try {
            // Usa el correo electrónico de la sesión y sanitízalo
            $correoUsuario = $_SESSION["correo"];
            $correoUsuario = preg_replace('/[^A-Za-z0-9_\-@.]/', '_', $correoUsuario);

            // Carpeta con la fecha actual (segundos incluidos)
            $fechaActual = date("Y-m-d_H-i-s");
            $carpetaDestinoxlsx = __DIR__ . "/../documentos/" . $correoUsuario . "/excel/ ";
            $carpetaDestinoxml = __DIR__ . "/../documentos/" . $correoUsuario . "/xml/ ";

            if (!is_dir($carpetaDestinoxlsx)) {
                mkdir($carpetaDestinoxlsx, 0777, true);
            }
            if (!is_dir($carpetaDestinoxml)) {
                mkdir($carpetaDestinoxml, 0777, true);
            }

            // Nombres de archivo con la fecha y hora actual
            $nombreBase = $fechaActual;
            $outputExcelFile = $carpetaDestinoxlsx . $nombreBase . ".xlsx";
            $outputXMLFile = $carpetaDestinoxml . $nombreBase . ".xml";

            // Crear nuevo spreadsheet
            $spreadsheet = new Spreadsheet();
            $worksheet = $spreadsheet->getActiveSheet();

            // Recoge los datos del formulario
            $fecha = $_POST['fecha'];
            $nombreApellido = $_POST['nombre'];
            $NFactura = $_POST['nfactura'];
            $direccion = $_POST['direccion'];
            $telefono = $_POST['telefono'];
            $codigo_postal = $_POST['codigo_postal'];
            $ciudad = $_POST['ciudad'];
            $cliente_nombre = $_POST['cliente_nombre'];
            $cliente_nif = $_POST['cliente_nif'];
            $cliente_domicilio = $_POST['cliente_domicilio'];
            $cliente_cp = $_POST['cliente_cp'];
            $cliente_telefono = $_POST['cliente_telefono'];

            // Añadir datos al Excel
            $worksheet->setCellValue('A1', 'Cliente');
            $worksheet->setCellValue('B1', 'Domicilio');
            $worksheet->setCellValue('C1', 'Fecha');
            $worksheet->setCellValue('D1', 'NºFactura');
            $worksheet->setCellValue('E1', 'Concepto');
            $worksheet->setCellValue('F1', 'Precio');
            $worksheet->setCellValue('G1', 'Total');

            // Escribir valores del formulario en fila 2
            $worksheet->setCellValue('A2', $cliente_nombre);
            $worksheet->setCellValue('B2', $cliente_domicilio);
            $worksheet->setCellValue('C2', $fecha);
            $worksheet->setCellValue('D2', $NFactura);
            $worksheet->setCellValue('E2', $telefono);
            
            if (is_numeric($NFactura)) {
                $worksheet->setCellValue('F2', (float)$NFactura);
            } else {
                $worksheet->setCellValue('F2', 'Monto inválido');
            }

            // Guardar archivo Excel
            $writer = new Xlsx($spreadsheet);
            $writer->save($outputExcelFile);
            echo "Datos guardados correctamente.";

            // Crear XML semántico
            $xml = new DOMDocument('1.0', 'UTF-8');
            $factura = $xml->createElement('factura');

            $clienteTag = $xml->createElement('cliente', htmlspecialchars($cliente_nombre));
            $fechaTag = $xml->createElement('fecha', htmlspecialchars($fecha));
            $precioTag = $xml->createElement('precio', $NFactura);

            $factura->appendChild($clienteTag);
            $factura->appendChild($fechaTag);
            $factura->appendChild($precioTag);

            $xml->appendChild($factura);

            // Guardar XML
            $xml->formatOutput = true;
            $xml->save($outputXMLFile);

            require_once("view/procesado_view.php");
            
        } catch (Exception $e) {
            echo "Error en la conversión: " . $e->getMessage();
        }
    }
}

// Función auxiliar para obtener el id del usuario a partir del correo
function obtenerIdUsuarioPorCorreo($correo)
{
    require_once("model/usuarios_model.php");
    $usuarioModel = new Usuarios_Model();
    $usuarios = $usuarioModel->get_usuarios();
    foreach ($usuarios as $usuario) {
        if ($usuario["correo_electronico"] === $correo) {
            return $usuario["id"];
        }
    }
    return null;
}
?>