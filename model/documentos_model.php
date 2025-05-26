<?php
class Documentos_Model {
    private $db;
    private $documentos;

    public function __construct() {
        require_once("model/conectar.php");
        $this->db = Conectar::conexion();
        $this->documentos = [];
    }

    // Obtener todos los documentos
    public function get_documentos() {
        $sql = "SELECT * FROM documentos ORDER BY fecha_subida DESC";
        $consulta = $this->db->query($sql);
        while ($registro = $consulta->fetch_assoc()) {
            $this->documentos[] = $registro;
        }
        return $this->documentos;
    }

    // Obtener documentos por ID de usuario
    public function get_documentos_por_usuario($id_usuario) {
        $sql = "SELECT * FROM documentos WHERE id_usuario = '$id_usuario' ORDER BY fecha_subida DESC";
        $consulta = $this->db->query($sql);
        while ($registro = $consulta->fetch_assoc()) {
            $this->documentos[] = $registro;
        }
        return $this->documentos;
    }

    // Obtener un documento por su ID
    public function get_documento_by_id($id) {
        $sql = "SELECT * FROM documentos WHERE id = '$id'";
        $consulta = $this->db->query($sql);
        return $consulta->fetch_assoc();
    }

    // Insertar documento
    public function insertarDocumento($id_usuario, $nombre_archivo, $tipo, $ruta_archivo, $fecha_subida) {
        $sql = "INSERT INTO documentos (id_usuario, nombre_archivo, tipo, ruta_archivo, fecha_subida) 
                VALUES ('$id_usuario', '$nombre_archivo', '$tipo', '$ruta_archivo', '$fecha_subida')";
        return $this->db->query($sql);
    }

    // Marcar como procesado
    public function marcarProcesado($id) {
        $sql = "UPDATE documentos SET procesado = 1 WHERE id = '$id'";
        return $this->db->query($sql);
    }

    // Eliminar documento
    public function eliminarDocumento($id) {
        $sql = "DELETE FROM documentos WHERE id = '$id'";
        return $this->db->query($sql);
    }

    // Modificar nombre o ruta (opcional, si quieres permitir editar)
    public function modificarDocumento($id, $nombre_archivo, $ruta_archivo) {
        $sql = "UPDATE documentos SET nombre_archivo = '$nombre_archivo', ruta_archivo = '$ruta_archivo' WHERE id = '$id'";
        return $this->db->query($sql);
    }
}
?>
