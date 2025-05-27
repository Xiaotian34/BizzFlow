<?php
session_start();

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

function excelToXmlForm() {
    require_once("view/excel_to_xml_view.php");
}

function convertirExcelXml() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
        require_once(__DIR__ . '/../vendor/aspose/cells/lib/aspose.cells.php');
        require_once(__DIR__ . '/../vendor/aspose/cells/Java.inc');

        // Guardar archivo temporalmente
        $excelTmp = $_FILES['excel_file']['tmp_name'];
        $excelName = $_FILES['excel_file']['name'];
        $destPath = __DIR__ . '/../uploads/' . uniqid() . '_' . $excelName;
        move_uploaded_file($excelTmp, $destPath);

        // Leer Excel con Aspose.Cells
        $workbook = new \aspose\cells\Workbook($destPath);
        $worksheet = $workbook->getWorksheets()->get(0);
        $cells = $worksheet->getCells();

        // Ejemplo: leer datos de la primera fila (ajusta según tu plantilla)
        $concepto = $cells->get("A2")->getStringValue();
        $importe = $cells->get("B2")->getDoubleValue();

        // Recoge los datos del formulario
        $fecha = $_POST['fecha'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $direccion = $_POST['direccion'];
        $telefono = $_POST['telefono'];
        $codigo_postal = $_POST['codigo_postal'];
        $ciudad = $_POST['ciudad'];
        $cliente_nombre = $_POST['cliente_nombre'];
        $cliente_nif = $_POST['cliente_nif'];
        $cliente_domicilio = $_POST['cliente_domicilio'];
        $cliente_cp = $_POST['cliente_cp'];
        $cliente_telefono = $_POST['cliente_telefono'];

        // Genera el XML factura-e (estructura mínima, debes completarla según la normativa)
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><Facturae></Facturae>');
        $xml->addChild('Fecha', $fecha);
        $xml->addChild('Remitente');
        $xml->Remitente->addChild('Nombre', $nombre);
        $xml->Remitente->addChild('Apellido', $apellido);
        $xml->Remitente->addChild('Direccion', $direccion);
        $xml->Remitente->addChild('Telefono', $telefono);
        $xml->Remitente->addChild('CodigoPostal', $codigo_postal);
        $xml->Remitente->addChild('Ciudad', $ciudad);
        $xml->addChild('Cliente');
        $xml->Cliente->addChild('Nombre', $cliente_nombre);
        $xml->Cliente->addChild('NIF', $cliente_nif);
        $xml->Cliente->addChild('Domicilio', $cliente_domicilio);
        $xml->Cliente->addChild('CodigoPostal', $cliente_cp);
        $xml->Cliente->addChild('Telefono', $cliente_telefono);
        $xml->addChild('Concepto', $concepto);
        $xml->addChild('Importe', $importe);

        // Guarda el XML en un archivo temporal
        $xmlFile = __DIR__ . '/../uploads/facturae_' . uniqid() . '.xml';
        $xml->asXML($xmlFile);

        // Descarga el XML
        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="facturae.xml"');
        readfile($xmlFile);

        // Limpieza
        unlink($destPath);
        unlink($xmlFile);
        exit;
    } else {
        echo "Error al procesar el archivo.";
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