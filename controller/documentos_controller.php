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
        $fecha = date('Y-m-d');
        console_log($_FILES["documento"]["name"]."3");
        $nombre = $_FILES['documento']['name'];
        $tipo = pathinfo($nombre, PATHINFO_EXTENSION);
        console_log($tipo);
        $ruta = "documentos/" . $nombre;

        $carpetaDestino = "documentos/";

        if($_FILES["documento"]["size"]==0){
            console_log("No se ha subido ningun fichero");
        }else{
            if(file_exists($carpetaDestino) || @mkdir($carpetaDestino, 0777, true)){
                // Intentar mover el archivo subido
                $origen  =$_FILES["documento"]["tmp_name"];
                $destino = $carpetaDestino.$_FILES["documento"]["name"];
                if(@move_uploaded_file($origen,$destino)){
                    // Si el archivo se ha movido correctamente, insertar en la base de datos
                    $id_usuario = obtenerIdUsuarioPorCorreo($_SESSION["correo"]); // Función auxiliar
                    if ($documento->insertarDocumento($id_usuario, $nombre, $tipo, $ruta, $fecha)) {
                        $message = "Documento subido correctamente";
                    } else {
                        $message = "Error al registrar en la base de datos";
                    }
                }else{
                    console_log($_FILES["documento"]["name"]." -> No se ha movido");
                }
            }
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