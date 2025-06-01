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
    require_once("model/usuarios_model.php");
    $usuarioModel = new Usuarios_Model();
    require_once("model/facturas_model.php");
    $facturaModel = new Facturas_Model();

    $message = "";

    // Borrar documento
    if (isset($_POST["borrar"])) {
        $id = isset($_POST["id"]) ? $_POST["id"] : '';
        // Obtener la ruta del archivo antes de borrar
        $doc = $documento->get_documento_by_id($id);
        if ($doc && isset($doc['ruta_archivo']) && file_exists($doc['ruta_archivo'])) {
            unlink($doc['ruta_archivo']); // Elimina el archivo físico
        }
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
            $id_usuario = $usuarioModel->obtenerIdUsuarioPorCorreo($_SESSION["correo"]); // Función auxiliar
            if ($documento->insertarDocumento($id_usuario, $nombre, $tipo, $ruta)) {
                $message = "Documento subido correctamente";
            } else {
                $message = "Error al registrar en la base de datos";
            }
        } else {
            $message = "Error al mover el archivo";
        }
    }

    // Renombrar documento (nombre y ruta)
    if (isset($_POST["guardar_edicion"])) {
        $id = $_POST["id"];
        $nuevo_nombre = trim($_POST["nuevo_nombre"]);
        $doc = $documento->get_documento_by_id($id);
        if ($doc) {
            $ruta_antigua = $doc["ruta_archivo"];
            $extension = pathinfo($ruta_antigua, PATHINFO_EXTENSION);
            // Si el usuario no pone la extensión, la añadimos
            if (strtolower(pathinfo($nuevo_nombre, PATHINFO_EXTENSION)) !== strtolower($extension)) {
                $nuevo_nombre .= "." . $extension;
            }
            $nueva_ruta = dirname($ruta_antigua) . "/" . $nuevo_nombre;

            // Renombrar archivo físico si el nombre ha cambiado
            if ($ruta_antigua !== $nueva_ruta) {
                if (file_exists($ruta_antigua)) {
                    if (rename($ruta_antigua, $nueva_ruta)) {
                        // Actualizar en la base de datos
                        if ($documento->modificarDocumento($id, $nuevo_nombre, $nueva_ruta)) {
                            $message = "Nombre de archivo actualizado correctamente";
                        } else {
                            $message = "Error al actualizar en la base de datos";
                        }
                    } else {
                        $message = "No se pudo renombrar el archivo físico";
                    }
                } else {
                    $message = "El archivo original no existe";
                }
            } else {
                $message = "El nombre es igual al actual";
            }
        } else {
            $message = "Documento no encontrado";
        }
    }

    // Obtener todos los documentos y facturas del usuario
    $id_usuario = $usuarioModel->obtenerIdUsuarioPorCorreo($_SESSION["correo"]);
    $array_documentos = $documento->get_documentos_por_usuario($id_usuario);
    $array_facturas = $facturaModel->get_facturas_por_usuario($id_usuario);

    require_once("view/gestionar_view.php");
}

function estadisticas()
{
    require_once("model/documentos_model.php");
    $documento = new Documentos_Model();
    require_once("model/usuarios_model.php");
    $usuarioModel = new Usuarios_Model();

    $message = "";

    // Obtener todos los documentos
    $id_usuario = $usuarioModel->obtenerIdUsuarioPorCorreo($_SESSION["correo"]); // Función auxiliar
    $array_documentos = $documento->get_documentos_por_usuario($id_usuario);
    require_once("view/estadisticas_view.php");
}

function convertirExcelXml() {
    if (isset($_POST["convertir"])) {
        try {
            require_once("model/documentos_model.php");
            require_once("model/usuarios_model.php");
            require_once("model/facturas_model.php"); // Añade el modelo de facturas
            $documento = new Documentos_Model();
            $usuarioModel = new Usuarios_Model();
            $facturaModel = new Facturas_Model(); // Instancia del modelo de facturas

            $correoUsuario = $_SESSION["correo"];
            $correoUsuario = preg_replace('/[^A-Za-z0-9_\-@.]/', '_', $correoUsuario);
            $fechaActual = date("Y-m-d_H-i-s");

            // Rutas organizadas: documentos/[usuario]/excel/ y documentos/[usuario]/xml/
            $carpetaExcel = __DIR__ . "/../documentos/" . $correoUsuario . "/excel/";
            $carpetaXml   = __DIR__ . "/../documentos/" . $correoUsuario . "/xml/";

            if (!is_dir($carpetaExcel)) {
                mkdir($carpetaExcel, 0777, true);
            }
            if (!is_dir($carpetaXml)) {
                mkdir($carpetaXml, 0777, true);
            }

            $nombreBase = $fechaActual;
            $outputExcelFile = $carpetaExcel . $nombreBase . ".xlsx";
            $outputXMLFile   = $carpetaXml   . $nombreBase . ".xml";

            // Recoge los datos del formulario - EMISOR
            $emisor_nif = $_POST['emisor_nif'] ?? '';
            $emisor_nombre = $_POST['emisor_nombre'] ?? '';
            $emisor_direccion = $_POST['emisor_direccion'] ?? '';
            $emisor_cp = $_POST['emisor_cp'] ?? '';
            $emisor_ciudad = $_POST['emisor_ciudad'] ?? '';
            $emisor_provincia = $_POST['emisor_provincia'] ?? '';
            $emisor_pais = $_POST['emisor_pais'] ?? 'ESP';
            $emisor_telefono = $_POST['emisor_telefono'] ?? ''; // FIX: Variable was missing
            $emisor_email = $_POST['emisor_email'] ?? '';

            // Recoge los datos del formulario - CLIENTE
            $cliente_nif = $_POST['cliente_nif'] ?? '';
            $cliente_nombre = $_POST['cliente_nombre'] ?? '';
            $cliente_direccion = $_POST['cliente_direccion'] ?? '';
            $cliente_cp = $_POST['cliente_cp'] ?? '';
            $cliente_ciudad = $_POST['cliente_ciudad'] ?? '';
            $cliente_provincia = $_POST['cliente_provincia'] ?? '';
            $cliente_pais = $_POST['cliente_pais'] ?? 'ESP';
            $cliente_telefono = $_POST['cliente_telefono'] ?? ''; // FIX: Variable was missing
            $cliente_email = $_POST['cliente_email'] ?? '';

            // Datos de la factura
            $fecha = $_POST['fecha'] ?? '';
            $NFactura = $_POST['nfactura'] ?? '';
            $iva = $_POST['ivaPorcentaje'] ?? 21;

            // FIX: Variables that were used but not defined - removing unused variables
            // These variables were referenced but not used consistently:
            // $provincia, $pais - these seem to be duplicates of emisor_provincia/emisor_pais

            // Recoge los items de la factura
            $item_descripcion = $_POST['item_descripcion'] ?? [];
            $item_cantidad = $_POST['item_cantidad'] ?? [];
            $item_precio = $_POST['item_precio'] ?? [];
            $item_total = $_POST['item_total'] ?? [];

            // Crear nuevo spreadsheet
            $spreadsheet = new Spreadsheet();
            $worksheet = $spreadsheet->getActiveSheet();

            // Cabeceras de datos generales
            $worksheet->setCellValue('A1', 'Emisor');
            $worksheet->setCellValue('B1', 'Dirección Emisor');
            $worksheet->setCellValue('C1', 'Teléfono Emisor');
            $worksheet->setCellValue('D1', 'CP Emisor');
            $worksheet->setCellValue('E1', 'Ciudad Emisor');
            $worksheet->setCellValue('F1', 'Provincia Emisor');
            $worksheet->setCellValue('G1', 'País Emisor');
            $worksheet->setCellValue('H1', 'Email Emisor');
            $worksheet->setCellValue('I1', 'Cliente');
            $worksheet->setCellValue('J1', 'Dirección Cliente');
            $worksheet->setCellValue('K1', 'Teléfono Cliente');
            $worksheet->setCellValue('L1', 'CP Cliente');
            $worksheet->setCellValue('M1', 'Ciudad Cliente');
            $worksheet->setCellValue('N1', 'Provincia Cliente');
            $worksheet->setCellValue('O1', 'País Cliente');
            $worksheet->setCellValue('P1', 'Email Cliente');
            $worksheet->setCellValue('Q1', 'Fecha');
            $worksheet->setCellValue('R1', 'NºFactura');
            $worksheet->setCellValue('S1', 'IVA (%)');

            // Datos generales en fila 2
            $worksheet->setCellValue('A2', $emisor_nombre);
            $worksheet->setCellValue('B2', $emisor_direccion);
            $worksheet->setCellValue('C2', $emisor_telefono); // FIX: Now properly defined
            $worksheet->setCellValue('D2', $emisor_cp);
            $worksheet->setCellValue('E2', $emisor_ciudad);
            $worksheet->setCellValue('F2', $emisor_provincia);
            $worksheet->setCellValue('G2', $emisor_pais);
            $worksheet->setCellValue('H2', $emisor_email);
            $worksheet->setCellValue('I2', $cliente_nombre);
            $worksheet->setCellValue('J2', $cliente_direccion);
            $worksheet->setCellValue('K2', $cliente_telefono); // FIX: Now properly defined
            $worksheet->setCellValue('L2', $cliente_cp);
            $worksheet->setCellValue('M2', $cliente_ciudad);
            $worksheet->setCellValue('N2', $cliente_provincia);
            $worksheet->setCellValue('O2', $cliente_pais);
            $worksheet->setCellValue('P2', $cliente_email);
            $worksheet->setCellValue('Q2', $fecha);
            $worksheet->setCellValue('R2', $NFactura);
            $worksheet->setCellValue('S2', $iva);

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

            // GUARDAR DOCUMENTO EN BASE DE DATOS
            $id_usuario = $usuarioModel->obtenerIdUsuarioPorCorreo($_SESSION["correo"]);
            $nombre_archivo = $nombreBase . ".xlsx";
            $tipo = "xlsx";
            $ruta_archivo = "documentos/" . $correoUsuario . "/excel/" . $nombre_archivo; // Ruta relativa para la BD

            $documento->insertarDocumento($id_usuario, $nombre_archivo, $tipo, $ruta_archivo);

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
            // SellerParty (EMISOR)
            $seller = $xml->createElement('SellerParty');
            $taxId = $xml->createElement('TaxIdentification');
            $taxId->appendChild($xml->createElement('PersonTypeCode', 'F'));
            $taxId->appendChild($xml->createElement('ResidenceTypeCode', 'R'));
            $taxId->appendChild($xml->createElement('TaxIdentificationNumber', htmlspecialchars($emisor_nif))); // FIX: Was using $cliente_nif incorrectly
            $seller->appendChild($taxId);

            $individual = $xml->createElement('Individual');
            $individual->appendChild($xml->createElement('Name', htmlspecialchars($emisor_nombre))); // FIX: Was using undefined $nombreApellido
            $seller->appendChild($individual);

            $address = $xml->createElement('AddressInSpain');
            $address->appendChild($xml->createElement('Address', htmlspecialchars($emisor_direccion))); // FIX: Was using undefined $direccion
            $address->appendChild($xml->createElement('PostCode', htmlspecialchars($emisor_cp))); // FIX: Was using undefined $codigo_postal
            $address->appendChild($xml->createElement('Town', htmlspecialchars($emisor_ciudad))); // FIX: Was using undefined $ciudad
            $address->appendChild($xml->createElement('Province', htmlspecialchars($emisor_provincia))); // FIX: Was using undefined $provincia
            $address->appendChild($xml->createElement('CountryCode', htmlspecialchars($emisor_pais))); // FIX: Was using undefined $pais
            $seller->appendChild($address);

            $contact = $xml->createElement('ContactDetails');
            $contact->appendChild($xml->createElement('Telephone', htmlspecialchars($emisor_telefono))); // FIX: Was using undefined $telefono
            $contact->appendChild($xml->createElement('ElectronicMail', htmlspecialchars($emisor_email)));
            $seller->appendChild($contact);

            $parties->appendChild($seller);

            // BuyerParty (CLIENTE)
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
            $address2->appendChild($xml->createElement('Address', htmlspecialchars($cliente_direccion)));
            $address2->appendChild($xml->createElement('PostCode', htmlspecialchars($cliente_cp)));
            $address2->appendChild($xml->createElement('Town', htmlspecialchars($cliente_ciudad)));
            $address2->appendChild($xml->createElement('Province', htmlspecialchars($cliente_provincia)));
            $address2->appendChild($xml->createElement('CountryCode', htmlspecialchars($cliente_pais)));
            $buyer->appendChild($address2);

            $contact2 = $xml->createElement('ContactDetails');
            $contact2->appendChild($xml->createElement('Telephone', htmlspecialchars($cliente_telefono))); // FIX: Now properly defined
            $contact2->appendChild($xml->createElement('ElectronicMail', htmlspecialchars($cliente_email)));
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
            $taxAmount->appendChild($xml->createElement('TotalAmount', number_format($totalFactura * floatval($iva) / 100, 2, '.', '')));
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

            // GUARDAR FACTURA (XML) EN BASE DE DATOS
            $nombre_archivo_xml = $nombreBase . ".xml";
            $ruta_archivo_xml = "documentos/" . $correoUsuario . "/xml/" . $nombre_archivo_xml; // Ruta relativa para la BD

            $facturaModel->insertar_factura($id_usuario, $nombre_archivo_xml, $ruta_archivo_xml);

            require_once("view/procesado_view.php");
        } catch (Exception $e) {
            echo "Error en la conversión: " . $e->getMessage();
        }
    }
    require_once("view/excel_to_xml_view.php");
}