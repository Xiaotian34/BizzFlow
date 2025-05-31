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

                // Recoge los nuevos datos del formulario
                $provincia = $_POST['provincia'] ?? '';
                $pais = $_POST['pais'] ?? 'ESP';
                $cliente_provincia = $_POST['cliente_provincia'] ?? '';
                $cliente_pais = $_POST['cliente_pais'] ?? 'ESP';

                // Recoge los items de la factura
                $item_descripcion = isset($_POST['item_descripcion']) ? $_POST['item_descripcion'] : [];
                $item_cantidad = isset($_POST['item_cantidad']) ? $_POST['item_cantidad'] : [];
                $item_precio = isset($_POST['item_precio']) ? $_POST['item_precio'] : [];
                $item_total = isset($_POST['item_total']) ? $_POST['item_total'] : [];

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

                // Cabeceras
                $newWorksheet->setCellValue('A1', 'Descripción');
                $newWorksheet->setCellValue('B1', 'Cantidad');
                $newWorksheet->setCellValue('C1', 'Precio');
                $newWorksheet->setCellValue('D1', 'Total');

                // Escribir los items
                $fila = 2;
                for ($i = 0; $i < count($item_descripcion); $i++) {
                    $newWorksheet->setCellValue('A' . $fila, $item_descripcion[$i]);
                    $newWorksheet->setCellValue('B' . $fila, $item_cantidad[$i]);
                    $newWorksheet->setCellValue('C' . $fila, $item_precio[$i]);
                    $newWorksheet->setCellValue('D' . $fila, $item_total[$i]);
                    $fila++;
                }

                // Guardar archivo Excel
                $writer = new Xlsx($newSpreadsheet);
                $writer->save($outputFile);
                echo "Datos guardados correctamente.";

                // Generación de XML Facturae 3.2.2
                $xml = new DOMDocument('1.0', 'UTF-8');
                $xml->formatOutput = true;

                $facturae = $xml->createElementNS('http://www.facturae.es/Facturae/2014/v3.2.2/Facturae', 'Facturae');
                $facturae->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
                $facturae->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:schemaLocation', 'http://www.facturae.es/Facturae/2014/v3.2.2/Facturae Facturaev3_2_2.xsd');

                // FileHeader
                $fileHeader = $xml->createElement('FileHeader');
                $fileHeader->appendChild($xml->createElement('SchemaVersion', '3.2.2'));
                $fileHeader->appendChild($xml->createElement('Modality', 'I'));
                $fileHeader->appendChild($xml->createElement('InvoiceIssuerType', 'EM'));
                $facturae->appendChild($fileHeader);

                // Parties
                $parties = $xml->createElement('Parties');

                // SellerParty (Emisor)
                $seller = $xml->createElement('SellerParty');
                $taxId = $xml->createElement('TaxIdentification');
                $taxId->appendChild($xml->createElement('PersonTypeCode', 'F'));
                $taxId->appendChild($xml->createElement('ResidenceTypeCode', 'R'));
                $taxId->appendChild($xml->createElement('TaxIdentificationNumber', htmlspecialchars($cliente_nif)));
                $seller->appendChild($taxId);

                $individual = $xml->createElement('Individual');
                $individual->appendChild($xml->createElement('Name', htmlspecialchars($nombreApellido)));
                $seller->appendChild($individual);

                $address = $xml->createElement('AddressInSpain');
                $address->appendChild($xml->createElement('Address', htmlspecialchars($direccion)));
                $address->appendChild($xml->createElement('PostCode', htmlspecialchars($codigo_postal)));
                $address->appendChild($xml->createElement('Town', htmlspecialchars($ciudad)));
                $address->appendChild($xml->createElement('Province', htmlspecialchars($provincia)));
                $address->appendChild($xml->createElement('CountryCode', htmlspecialchars($pais)));
                $seller->appendChild($address);

                $parties->appendChild($seller);

                // BuyerParty (Receptor)
                $buyer = $xml->createElement('BuyerParty');
                $taxId2 = $xml->createElement('TaxIdentification');
                $taxId2->appendChild($xml->createElement('PersonTypeCode', 'F'));
                $taxId2->appendChild($xml->createElement('ResidenceTypeCode', 'R'));
                $taxId2->appendChild($xml->createElement('TaxIdentificationNumber', htmlspecialchars($cliente_nif)));
                $buyer->appendChild($taxId2);

                $individual2 = $xml->createElement('Individual');
                $individual2->appendChild($xml->createElement('Name', htmlspecialchars($cliente_nombre)));
                $buyer->appendChild($individual2);

                $address2 = $xml->createElement('AddressInSpain');
                $address2->appendChild($xml->createElement('Address', htmlspecialchars($cliente_domicilio)));
                $address2->appendChild($xml->createElement('PostCode', htmlspecialchars($cliente_cp)));
                $address2->appendChild($xml->createElement('Town', htmlspecialchars($ciudad)));
                $address2->appendChild($xml->createElement('Province', htmlspecialchars($cliente_provincia)));
                $address2->appendChild($xml->createElement('CountryCode', htmlspecialchars($cliente_pais)));
                $buyer->appendChild($address2);

                $parties->appendChild($buyer);

                $facturae->appendChild($parties);

                // Invoices
                $invoices = $xml->createElement('Invoices');
                $invoice = $xml->createElement('Invoice');
                $invoice->appendChild($xml->createElement('InvoiceNumber', htmlspecialchars($NFactura)));
                $invoice->appendChild($xml->createElement('InvoiceSeriesCode', 'A'));
                $invoice->appendChild($xml->createElement('InvoiceDocumentType', 'FC'));
                $invoice->appendChild($xml->createElement('InvoiceClass', 'OO'));
                $invoice->appendChild($xml->createElement('IssueDate', htmlspecialchars($fecha)));

                // Totales
                $totalFactura = 0;
                foreach ($item_total as $t) $totalFactura += floatval($t);
                $totalConIva = $totalFactura + ($totalFactura * floatval($iva) / 100);

                $invoiceTotals = $xml->createElement('InvoiceTotals');
                $invoiceTotals->appendChild($xml->createElement('TotalGrossAmount', number_format($totalFactura, 2, '.', '')));
                $invoiceTotals->appendChild($xml->createElement('TotalTaxOutputs', number_format($totalFactura * floatval($iva) / 100, 2, '.', '')));
                $invoiceTotals->appendChild($xml->createElement('TotalInvoiceAmount', number_format($totalConIva, 2, '.', '')));
                $invoice->appendChild($invoiceTotals);

                // InvoiceLines
                $invoiceLines = $xml->createElement('Items');
                for ($i = 0; $i < count($item_descripcion); $i++) {
                    $line = $xml->createElement('InvoiceLine');
                    $line->appendChild($xml->createElement('ItemDescription', htmlspecialchars($item_descripcion[$i])));
                    $line->appendChild($xml->createElement('Quantity', number_format($item_cantidad[$i], 2, '.', '')));
                    $line->appendChild($xml->createElement('UnitOfMeasure', '01'));
                    $line->appendChild($xml->createElement('UnitPriceWithoutTax', number_format($item_precio[$i], 2, '.', '')));
                    $line->appendChild($xml->createElement('TotalCost', number_format($item_total[$i], 2, '.', '')));
                    // Taxes
                    $taxesOutputs = $xml->createElement('TaxesOutputs');
                    $tax = $xml->createElement('Tax');
                    $tax->appendChild($xml->createElement('TaxTypeCode', '01'));
                    $tax->appendChild($xml->createElement('TaxRate', number_format($iva, 2, '.', '')));
                    $tax->appendChild($xml->createElement('TaxableBase', number_format($item_total[$i], 2, '.', '')));
                    $taxesOutputs->appendChild($tax);
                    $line->appendChild($taxesOutputs);
                    $invoiceLines->appendChild($line);
                }
                $invoice->appendChild($invoiceLines);

                $invoices->appendChild($invoice);
                $facturae->appendChild($invoices);

                $xml->appendChild($facturae);

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
            $correoUsuario = $_SESSION["correo"];
            $correoUsuario = preg_replace('/[^A-Za-z0-9_\-@.]/', '_', $correoUsuario);
            $fechaActual = date("Y-m-d_H-i-s");

            $carpetaDestinoxlsx = __DIR__ . "/../documentos/" . $correoUsuario . "/excel/ ";
            $carpetaDestinoxml = __DIR__ . "/../documentos/" . $correoUsuario . "/xml/ ";

            if (!is_dir($carpetaDestinoxlsx)) {
                mkdir($carpetaDestinoxlsx, 0777, true);
            }
            if (!is_dir($carpetaDestinoxml)) {
                mkdir($carpetaDestinoxml, 0777, true);
            }
            $nombreBase = $fechaActual;
            $outputExcelFile = $carpetaDestinoxlsx . $nombreBase . ".xlsx";
            $outputXMLFile = $carpetaDestinoxml . $nombreBase . ".xml";

            // Recoge los datos del formulario
            $fecha = $_POST['fecha'] ?? '';
            $nombreApellido = $_POST['nombre'] ?? '';
            $NFactura = $_POST['nfactura'] ?? '';
            $direccion = $_POST['direccion'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $codigo_postal = $_POST['codigo_postal'] ?? '';
            $ciudad = $_POST['ciudad'] ?? '';
            $cliente_nombre = $_POST['cliente_nombre'] ?? '';
            $cliente_nif = $_POST['cliente_nif'] ?? '';
            $cliente_domicilio = $_POST['cliente_domicilio'] ?? '';
            $cliente_cp = $_POST['cliente_cp'] ?? '';
            $cliente_telefono = $_POST['cliente_telefono'] ?? '';
            $iva = $_POST['ivaPorcentaje'] ?? 21;

            // Recoge los nuevos datos del formulario
            $provincia = $_POST['provincia'] ?? '';
            $pais = $_POST['pais'] ?? 'ESP';
            $cliente_provincia = $_POST['cliente_provincia'] ?? '';
            $cliente_pais = $_POST['cliente_pais'] ?? 'ESP';

            // Recoge los items de la factura
            $item_descripcion = $_POST['item_descripcion'] ?? [];
            $item_cantidad = $_POST['item_cantidad'] ?? [];
            $item_precio = $_POST['item_precio'] ?? [];
            $item_total = $_POST['item_total'] ?? [];

            // Crear nuevo spreadsheet
            $spreadsheet = new Spreadsheet();
            $worksheet = $spreadsheet->getActiveSheet();

            // Cabeceras de datos generales
            $worksheet->setCellValue('A1', 'Remitente');
            $worksheet->setCellValue('B1', 'Dirección');
            $worksheet->setCellValue('C1', 'Teléfono');
            $worksheet->setCellValue('D1', 'Código Postal');
            $worksheet->setCellValue('E1', 'Ciudad');
            $worksheet->setCellValue('F1', 'Fecha');
            $worksheet->setCellValue('G1', 'NºFactura');
            $worksheet->setCellValue('H1', 'Cliente');
            $worksheet->setCellValue('I1', 'NIF/NIE/DNI');
            $worksheet->setCellValue('J1', 'Domicilio Cliente');
            $worksheet->setCellValue('K1', 'CP Cliente');
            $worksheet->setCellValue('L1', 'Teléfono Cliente');
            $worksheet->setCellValue('M1', 'IVA (%)');

            // Datos generales en fila 2
            $worksheet->setCellValue('A2', $nombreApellido);
            $worksheet->setCellValue('B2', $direccion);
            $worksheet->setCellValue('C2', $telefono);
            $worksheet->setCellValue('D2', $codigo_postal);
            $worksheet->setCellValue('E2', $ciudad);
            $worksheet->setCellValue('F2', $fecha);
            $worksheet->setCellValue('G2', $NFactura);
            $worksheet->setCellValue('H2', $cliente_nombre);
            $worksheet->setCellValue('I2', $cliente_nif);
            $worksheet->setCellValue('J2', $cliente_domicilio);
            $worksheet->setCellValue('K2', $cliente_cp);
            $worksheet->setCellValue('L2', $cliente_telefono);
            $worksheet->setCellValue('M2', $iva);

            // Cabeceras de items
            $worksheet->setCellValue('A4', 'Descripción');
            $worksheet->setCellValue('B4', 'Cantidad');
            $worksheet->setCellValue('C4', 'Precio');
            $worksheet->setCellValue('D4', 'Total');

            // Escribir los items a partir de la fila 5
            $fila = 5;
            $totalFactura = 0;
            for ($i = 0; $i < count($item_descripcion); $i++) {
                $worksheet->setCellValue('A' . $fila, $item_descripcion[$i]);
                $worksheet->setCellValue('B' . $fila, $item_cantidad[$i]);
                $worksheet->setCellValue('C' . $fila, $item_precio[$i]);
                $worksheet->setCellValue('D' . $fila, $item_total[$i]);
                $totalFactura += floatval($item_total[$i]);
                $fila++;
            }

            // Total y total con IVA
            $worksheet->setCellValue('C' . $fila, 'Total');
            $worksheet->setCellValue('D' . $fila, $totalFactura);
            $fila++;
            $worksheet->setCellValue('C' . $fila, 'Total con IVA');
            $totalConIva = $totalFactura + ($totalFactura * floatval($iva) / 100);
            $worksheet->setCellValue('D' . $fila, $totalConIva);

            // Guardar archivo Excel
            $writer = new Xlsx($spreadsheet);
            $writer->save($outputExcelFile);

            // Calcular totales
            $totalFactura = 0;
            for ($i = 0; $i < count($item_total); $i++) {
                $totalFactura += floatval($item_total[$i]);
            }
            $totalIva = $totalFactura * floatval($iva) / 100;
            $totalConIva = $totalFactura + $totalIva;

            // Crear XML Facturae 3.2.2
            $xml = new DOMDocument('1.0', 'UTF-8');
            $xml->formatOutput = true;

            // Nodo raíz con namespaces
            $facturae = $xml->createElementNS('http://www.facturae.es/Facturae/2009/v3.2.2/Facturae', 'Facturae');
            $facturae->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
            $facturae->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:schemaLocation', 'http://www.facturae.es/Facturae/2009/v3.2.2/Facturae http://www.facturae.es/Facturae/2009/v3.2.2/Facturae.xsd');

            // FileHeader
            $fileHeader = $xml->createElement('FileHeader');
            $fileHeader->appendChild($xml->createElement('SchemaVersion', '3.2.2'));
            $fileHeader->appendChild($xml->createElement('Modality', 'I'));
            $fileHeader->appendChild($xml->createElement('InvoiceIssuerType', 'EM'));

            // Batch
            $batch = $xml->createElement('Batch');
            $batch->appendChild($xml->createElement('BatchIdentifier', '1'));
            $batch->appendChild($xml->createElement('InvoicesCount', '1'));
            $totalAmountNode = $xml->createElement('TotalAmount', number_format($totalFactura, 2, '.', ''));
            $totalInvoicesAmount = $xml->createElement('TotalInvoicesAmount');
            $totalInvoicesAmount->appendChild($totalAmountNode);
            $batch->appendChild($totalInvoicesAmount);

            $totalOutstandingAmount = $xml->createElement('TotalOutstandingAmount');
            $totalOutstandingAmount->appendChild($xml->createElement('TotalAmount', number_format($totalFactura, 2, '.', '')));
            $batch->appendChild($totalOutstandingAmount);

            $totalExecutableAmount = $xml->createElement('TotalExecutableAmount');
            $totalExecutableAmount->appendChild($xml->createElement('TotalAmount', number_format($totalFactura, 2, '.', '')));
            $batch->appendChild($totalExecutableAmount);

            $batch->appendChild($xml->createElement('InvoiceCurrencyCode', 'EUR'));
            $fileHeader->appendChild($batch);
            $facturae->appendChild($fileHeader);

            // Parties
            $parties = $xml->createElement('Parties');
            // SellerParty
            $seller = $xml->createElement('SellerParty');
            $taxId = $xml->createElement('TaxIdentification');
            $taxId->appendChild($xml->createElement('PersonTypeCode', 'F'));
            $taxId->appendChild($xml->createElement('ResidenceTypeCode', 'R'));
            $taxId->appendChild($xml->createElement('TaxIdentificationNumber', htmlspecialchars($cliente_nif)));
            $seller->appendChild($taxId);

            $individual = $xml->createElement('Individual');
            $individual->appendChild($xml->createElement('Name', htmlspecialchars($nombreApellido)));
            $seller->appendChild($individual);

            $address = $xml->createElement('AddressInSpain');
            $address->appendChild($xml->createElement('Address', htmlspecialchars($direccion)));
            $address->appendChild($xml->createElement('PostCode', htmlspecialchars($codigo_postal)));
            $address->appendChild($xml->createElement('Town', htmlspecialchars($ciudad)));
            $address->appendChild($xml->createElement('Province', htmlspecialchars($provincia)));
            $address->appendChild($xml->createElement('CountryCode', htmlspecialchars($pais)));
            $seller->appendChild($address);

            $contact = $xml->createElement('ContactDetails');
            $contact->appendChild($xml->createElement('Telephone', htmlspecialchars($telefono)));
            $seller->appendChild($contact);

            $parties->appendChild($seller);

            // BuyerParty
            $buyer = $xml->createElement('BuyerParty');
            $taxId2 = $xml->createElement('TaxIdentification');
            $taxId2->appendChild($xml->createElement('PersonTypeCode', 'F'));
            $taxId2->appendChild($xml->createElement('ResidenceTypeCode', 'R'));
            $taxId2->appendChild($xml->createElement('TaxIdentificationNumber', htmlspecialchars($cliente_nif)));
            $buyer->appendChild($taxId2);

            $individual2 = $xml->createElement('Individual');
            $individual2->appendChild($xml->createElement('Name', htmlspecialchars($cliente_nombre)));
            $buyer->appendChild($individual2);

            $address2 = $xml->createElement('AddressInSpain');
            $address2->appendChild($xml->createElement('Address', htmlspecialchars($cliente_domicilio)));
            $address2->appendChild($xml->createElement('PostCode', htmlspecialchars($cliente_cp)));
            $address2->appendChild($xml->createElement('Town', htmlspecialchars($ciudad)));
            $address2->appendChild($xml->createElement('Province', htmlspecialchars($cliente_provincia)));
            $address2->appendChild($xml->createElement('CountryCode', htmlspecialchars($cliente_pais)));
            $buyer->appendChild($address2);

            $contact2 = $xml->createElement('ContactDetails');
            $contact2->appendChild($xml->createElement('Telephone', htmlspecialchars($cliente_telefono)));
            $buyer->appendChild($contact2);

            $parties->appendChild($buyer);
            $facturae->appendChild($parties);

            // Invoices
            $invoices = $xml->createElement('Invoices');
            $invoice = $xml->createElement('Invoice');

            // InvoiceHeader
            $invoiceHeader = $xml->createElement('InvoiceHeader');
            $invoiceHeader->appendChild($xml->createElement('InvoiceNumber', htmlspecialchars($NFactura)));
            $invoiceHeader->appendChild($xml->createElement('InvoiceSeriesCode', 'A'));
            $invoiceHeader->appendChild($xml->createElement('InvoiceDocumentType', 'FC'));
            $invoiceHeader->appendChild($xml->createElement('InvoiceClass', 'OO'));
            $invoice->appendChild($invoiceHeader);

            // InvoiceIssueData
            $invoiceIssueData = $xml->createElement('InvoiceIssueData');
            $invoiceIssueData->appendChild($xml->createElement('IssueDate', htmlspecialchars($fecha)));
            $invoiceIssueData->appendChild($xml->createElement('InvoiceCurrencyCode', 'EUR'));
            $invoiceIssueData->appendChild($xml->createElement('TaxCurrencyCode', 'EUR'));
            $invoiceIssueData->appendChild($xml->createElement('LanguageName', 'es'));
            $invoice->appendChild($invoiceIssueData);

            // TaxOutputs
            $taxOutputs = $xml->createElement('TaxOutputs');
            $tax = $xml->createElement('Tax');
            $tax->appendChild($xml->createElement('TaxTypeCode', '01'));
            $tax->appendChild($xml->createElement('TaxRate', number_format($iva, 2, '.', '')));
            $taxableBase = $xml->createElement('TaxableBase');
            $taxableBase->appendChild($xml->createElement('TotalAmount', number_format($totalFactura, 2, '.', '')));
            $tax->appendChild($taxableBase);
            $taxAmount = $xml->createElement('TaxAmount');
            $taxAmount->appendChild($xml->createElement('TotalAmount', number_format($totalIva, 2, '.', '')));
            $tax->appendChild($taxAmount);
            $taxOutputs->appendChild($tax);
            $invoice->appendChild($taxOutputs);

            // InvoiceTotals
            $invoiceTotals = $xml->createElement('InvoiceTotals');
            $invoiceTotals->appendChild($xml->createElement('TotalGrossAmount', number_format($totalFactura, 2, '.', '')));
            $invoiceTotals->appendChild($xml->createElement('TotalTaxOutputs', number_format($totalIva, 2, '.', '')));
            $invoiceTotals->appendChild($xml->createElement('InvoiceTotal', number_format($totalConIva, 2, '.', '')));
            $invoiceTotals->appendChild($xml->createElement('TotalOutstandingAmount', number_format($totalConIva, 2, '.', '')));
            $invoiceTotals->appendChild($xml->createElement('TotalExecutableAmount', number_format($totalConIva, 2, '.', '')));
            $invoice->appendChild($invoiceTotals);

            // Items
            $items = $xml->createElement('Items');
            for ($i = 0; $i < count($item_descripcion); $i++) {
                $cantidad = isset($item_cantidad[$i]) && is_numeric($item_cantidad[$i]) ? floatval($item_cantidad[$i]) : 0;
                $precio = isset($item_precio[$i]) && is_numeric($item_precio[$i]) ? floatval($item_precio[$i]) : 0;
                $total = isset($item_total[$i]) && is_numeric($item_total[$i]) ? floatval($item_total[$i]) : 0;

                $line = $xml->createElement('InvoiceLine');
                $line->appendChild($xml->createElement('IssuerContractReference', htmlspecialchars($NFactura)));
                $line->appendChild($xml->createElement('ItemDescription', htmlspecialchars($item_descripcion[$i])));
                $line->appendChild($xml->createElement('Quantity', number_format($cantidad, 2, '.', '')));
                $line->appendChild($xml->createElement('UnitPriceWithoutTax', number_format($precio, 2, '.', '')));
                $line->appendChild($xml->createElement('TotalCost', number_format($total, 2, '.', '')));
                // TaxesOutputs por línea
                $taxesOutputs = $xml->createElement('TaxesOutputs');
                $taxLine = $xml->createElement('Tax');
                $taxLine->appendChild($xml->createElement('TaxTypeCode', '01'));
                $taxLine->appendChild($xml->createElement('TaxRate', number_format(floatval($iva), 2, '.', '')));
                $taxableBaseLine = $xml->createElement('TaxableBase');
                $taxableBaseLine->appendChild($xml->createElement('TotalAmount', number_format($total, 2, '.', '')));
                $taxLine->appendChild($taxableBaseLine);
                $taxAmountLine = $xml->createElement('TaxAmount');
                $taxAmountLine->appendChild($xml->createElement('TotalAmount', number_format($total * floatval($iva) / 100, 2, '.', '')));
                $taxLine->appendChild($taxAmountLine);
                $taxesOutputs->appendChild($taxLine);
                $line->appendChild($taxesOutputs);
                $items->appendChild($line);
            }
            $invoice->appendChild($items);

            $invoices->appendChild($invoice);
            $facturae->appendChild($invoices);

            $xml->appendChild($facturae);

            // Guardar XML
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